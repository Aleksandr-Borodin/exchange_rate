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
        $rateDates = $this->_crDayService->daysNotIsset($daysList, $source);
        return $this->_writeDaysResult($rateDates, $source);
    }

    /**
     * @param array $rateDates
     * @param string $source
     * @return CollectRateService
     */
    private function _writeDaysResult(array $rateDates, string $source): CollectRateService
    {
        foreach($rateDates as $rateDate => $stub) {
            $this->_writeDayResult($rateDate, $source);
        }
        return $this;
    }

    /**
     * @param string $rateDate
     * @param string $source
     * @return void
     */
    private function _writeDayResult(string $rateDate, string $source): void
    {
        $r = $this->_crDayService->writeRateDate($rateDate, $source);
        $r && $this->_dispatcher->dispatch($rateDate, $source);
    }
}
