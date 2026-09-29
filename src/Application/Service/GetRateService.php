<?php
/**
 * GetRateService.php
 * Created: 29.09.2026
 * Author: Alex
 * Project: exchange_rate
 */

declare(strict_types=1);

namespace ExchangeRate\Application\Service;

use ExchangeRate\Infrastructure\Messaging\CollectRateDispatcher;
use ExchangeRate\Domain\Enum\GetRateStatus;
use ExchangeRate\Infrastructure\Cache\RateCache;

class GetRateService
{
    /**
     * @var DEF_RESULT
     */
    private const DEF_RESULT = [
        'rate_date' => '',
        'quote_currency' => '',
        'base_currency' => '',
        'rate_value' => 0.00,
        'difference' => 0.00,
        'status' => '',
        'source' => '',
    ];

    /**
     * @param TradingDayService $_tdService
     * @param ExchangeRateDataService $_crDataService
     * @param ExchangeRateDayService $_crDayService
     * @param CollectRateDispatcher $_dispatcher
     * @param RateCache $_rateCache
     */
    public function __construct(private readonly TradingDayService $_tdService, private readonly ExchangeRateDataService $_crDataService, private readonly ExchangeRateDayService $_crDayService, private readonly CollectRateDispatcher $_dispatcher, private readonly RateCache $_rateCache) {}

    /**
     * @param string $rateDate
     * @param string $quoteCurrency
     * @param string $baseCurrency
     * @param string $source
     * @return array
     */
    public function getRate(string $rateDate, string $quoteCurrency, string $baseCurrency, string $source): array
    {
        $result = $this->_prepareDefResult($rateDate, $quoteCurrency, $baseCurrency, $source);
        $curResult = $this->_getRateData($rateDate, $quoteCurrency, $baseCurrency, $source);
        $prevRateDate = $this->_tdService->_preparePrevRateDate($rateDate);
        if (!$prevRateDate) {
            return $result;
        }
        $prevResult = $this->_getRateData($prevRateDate, $quoteCurrency, $baseCurrency, $source);
        $result['status'] = $this->_getResultStatus($curResult['status'], $prevResult['status']);
        if ($curResult['isset']) {
            $result['rate_value'] = $curResult['rate_value'];
            $prevResult['isset'] && $result['difference'] = $curResult['rate_value'] - $prevResult['rate_value'];
        }
        return $result;
    }

    /**
     * @param string $rateDate
     * @param string $quoteCurrency
     * @param string $baseCurrency
     * @param string $source
     * @return array
     */
    private function _getRateData(string $rateDate, string $quoteCurrency, string $baseCurrency, string $source): array
    {
        $result = ['rate_value' => 0.00, 'status' => 'unknown', 'isset' => false];
        $cacheKey = $this->_rateCache->prepareKey($rateDate, $quoteCurrency, $baseCurrency, $source);
        if ($this->_rateCache->has($cacheKey)) {
            $result['rate_value'] = $this->_rateCache->get($cacheKey);
            $result['status'] = 'completed';
            $result['isset'] = true;
            return $result;
        }
        $rateValue = $this->_crDataService->getRow($rateDate, $quoteCurrency, $baseCurrency, $source);
        if ($rateValue !== false) {
            $this->_rateCache->set($cacheKey, $rateValue);
            $result['rate_value'] = $this->_rateCache->get($cacheKey);
            $result['status'] = 'completed';
            $result['isset'] = true;
            return $result;
        }
        $status = $this->_crDayService->getRowStatus($rateDate, $source);
        $stIsFalse = $status === false;
        $isWritten = $stIsFalse && $this->_crDayService->writeRateDate($rateDate, $source);
        $isDispatched = $isWritten && $this->_dispatcher->dispatch($rateDate, $source);
        $isDispatched && $result['status'] = 'pending';
        !$stIsFalse && $result['status'] = $status;
        return $result;
    }

    /**
     * @param string $rateDate
     * @param string $quoteCurrency
     * @param string $baseCurrency
     * @param string $source
     * @return array
     */
    private function _prepareDefResult(string $rateDate, string $quoteCurrency, string $baseCurrency, string $source): array
    {
        $result = self::DEF_RESULT;
        $result['rate_date'] = $rateDate;
        $result['quote_currency'] = $quoteCurrency;
        $result['base_currency'] = $baseCurrency;
        $result['source'] = $source;
        return $result;
    }

    /**
     * @param string $curStatus
     * @param string $prevStatus
     * @return string
     */
    private function _getResultStatus(string $curStatus, string $prevStatus): string
    {
        return GetRateStatus::getResultStatus($curStatus, $prevStatus)->value;
    }
}
