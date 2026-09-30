<?php
/**
 * CbrWorkerService.php
 * Created: 29.09.2026
 * Author: Alex
 * Project: exchange_rate
 */

declare(strict_types=1);

namespace ExchangeRate\Application\Service;

use ExchangeRate\Infrastructure\Messaging\CollectRateDispatcher;
use ExchangeRate\Infrastructure\Cbr\CbrClient;
use ExchangeRate\Infrastructure\Cache\RateCache;
use ExchangeRate\Domain\Enum\ExchangeRateDayStatus;
use Exception;
use Error;

class CbrWorkerService
{
    /**
     * @var int
     */
    private const RETRY_DELAY = 10;

    /**
     * @var int
     */
    private const CONTINUE_DELAY = 4;

    /**
     * @var string
     */
    private const DEF_BASE_CURRENCY = 'RUB';

    /**
     * @param ExchangeRateDataService $_crDataService
     * @param ExchangeRateDayService $_crDayService
     * @param CollectRateDispatcher $_dispatcher
     * @param RateCache $_rateCache
     * @param CbrClient $_cbrClient
     */
    public function __construct(private readonly ExchangeRateDataService $_crDataService, private readonly ExchangeRateDayService $_crDayService, private readonly CollectRateDispatcher $_dispatcher, private readonly RateCache $_rateCache, private readonly CbrClient $_cbrClient) {}

    /**
     * @return void
     */
    public function run(): void
    {
        $this->_dispatcher->consume(
            function ($message): void {
                if ($this->_processMessage($message)) {
                    $message->ack();
                    sleep(self::CONTINUE_DELAY);
                    return;
                }
                $message->nack(false, true);
                sleep(self::RETRY_DELAY);
            }
        );
    }

    /**
     * @param type $message
     * @return bool
     */
    private function _processMessage($message): bool
    {
        try {
            $data = $this->_prepareMessageData($message->getBody());
            if (!$data['rate_date'] || !$data['source'] || $data['source'] !== 'cbr') {
                return true;
            }
            return $this->_processData($data['rate_date'], $data['source']);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * @param string $message
     * @return array
     */
    private function _prepareMessageData(string $message): array
    {
        $data = json_decode($message, true,512, JSON_THROW_ON_ERROR);
        return [
            'rate_date' => $data['date'] ?? '',
            'source' => $data['source'] ?? '',
        ];
    }

    /**
     * @param string $rateDate
     * @param string $source
     * @return bool
     */
    private function _processData(string $rateDate, string $source): bool
    {
        try {
            $r1 = $this->_crDayService->updateStatus($rateDate, $source, ExchangeRateDayStatus::PROCESSING->value);
            if (!$r1) { return false; }
            $cbrData = $this->_cbrClient->getRates($rateDate);
            if ($this->_processCbrData($rateDate, $source, $cbrData)) {
                return $this->_crDayService->updateStatus($rateDate, $source, ExchangeRateDayStatus::COMPLETED->value);
            }
            $this->_crDayService->updateStatus($rateDate, $source, ExchangeRateDayStatus::FAILED->value);
            return false;
        } catch (Exception $e) {
            $this->_crDayService->updateStatus($rateDate, $source, ExchangeRateDayStatus::FAILED->value);
            return false;
        } catch (Error $e) {
            $this->_crDayService->updateStatus($rateDate, $source, ExchangeRateDayStatus::FAILED->value);
            return false;
        }
    }

    /**
     * @param string $rateDate
     * @param string $source
     * @param array $cbrData
     * @return bool
     */
    private function _processCbrData(string $rateDate, string $source, array $cbrData): bool
    {
        $issetData = false;
        foreach($cbrData as $row) {
            $this->_processCbrDataRow($issetData, $rateDate, $source, $row);
        }
        return $issetData;
    }

    /**
     * @param bool $issetData
     * @param string $rateDate
     * @param string $source
     * @param array $row
     * @return void
     */
    private function _processCbrDataRow(bool &$issetData, string $rateDate, string $source, array $row): void
    {
        $quoteCurrency = strtoupper((string) ($row['code'] ?? ''));
        if (!$quoteCurrency) { return; }
        $nominal = (int) ($row['nominal'] ?? 0);
        if (!$nominal) { return; }
        $value = (double) ($row['value'] ?? 0.00);
        if (!$value) { return; }
        $rateValue = round($value / $nominal, 20);
        $r2 = $this->_crDataService->tryCreateRow($rateDate, $quoteCurrency, self::DEF_BASE_CURRENCY, $source, $rateValue);
        if (!$r2) { return; }
        $cacheKey = $this->_rateCache->prepareKey($rateDate, $quoteCurrency, self::DEF_BASE_CURRENCY, $source);
        if (!$this->_rateCache->has($cacheKey)) {
            $this->_rateCache->set($cacheKey, $rateValue);
        }
        $issetData = true;
    }
}
