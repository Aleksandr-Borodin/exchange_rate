<?php
/**
 * CollectRateService.php
 * Created: 29.09.2026
 * Author: Alex
 * Project: exchange_rate
 */

declare(strict_types=1);

namespace ExchangeRate\Application\Service;

use ExchangeRate\Infrastructure\Messaging\CollectRateDispatcher;

class CollectRateService
{
    /**
     * @param TradingDayService $_tdService
     * @param ExchangeRateDataService $_crDataService
     * @param ExchangeRateDayService $_crDayService
     * @param CollectRateDispatcher $_dispatcher
     */
    public function __construct(private readonly TradingDayService $_tdService, private readonly ExchangeRateDataService $_crDataService, private readonly ExchangeRateDayService $_crDayService, private readonly CollectRateDispatcher $_dispatcher) {}

    /**
     * @param int $days
     * @param string $source
     * @return ExchangeRateService
     */
    public function collectDays(int $days, string $source): CollectRateService
    {
        $daysList = $this->_tdService->getDaysList($days);
        $daysResult = $this->_crDayService->daysNotIsset($daysList, $source);
        return $this->_writeDaysResult($daysResult, $source);
    }

    /**
     * @param array $daysResult
     * @param string $source
     * @return CollectRateService
     */
    private function _writeDaysResult(array $daysResult, string $source): CollectRateService
    {
        foreach($daysResult as $dayResult => $stub) {
            $this->_writeDayResult($dayResult, $source);
        }
        return $this;
    }

    /**
     * @param string $dayResult
     * @param string $source
     * @return void
     */
    private function _writeDayResult(string $dayResult, string $source): void
    {
        $r = $this->_crDayService->writeRateDate($dayResult, $source);
        if(!$r) { return; }
        $this->_dispatcher->dispatch($dayResult, $source);
    }
}
