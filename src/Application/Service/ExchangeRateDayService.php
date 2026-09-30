<?php
/**
 * ExchangeRateDayService.php
 * Created: 29.09.2026
 * Author: Alex
 * Project: exchange_rate
 */

declare(strict_types=1);

namespace ExchangeRate\Application\Service;

use ExchangeRate\Infrastructure\Persistence\Doctrine\ExchangeRateDayRepository;

class ExchangeRateDayService
{
    /**
     * @param ExchangeRateDayRepository $_erDayRepository
     */
    public function __construct(private readonly ExchangeRateDayRepository $_erDayRepository) {}

    /**
     * @param array $daysList
     * @param string $source
     * @return array
     */
    public function daysNotIsset(array $daysList, string $source): array
    {
        $existingDates = $this->_erDayRepository->findExistingDates($daysList, $source);
        foreach ($existingDates as $date) {
            unset($daysList[$date]);
        }   
        return $daysList;
    }

    /**
     * @param string $rateDate
     * @param string $source
     * @return bool
     */
    public function writeRateDate(string $rateDate, string $source): bool
    {
        return $this->_erDayRepository->writeRateDate($rateDate, $source);
    }

    /**
     * @param string $rateDate
     * @param string $source
     * @return string|bool
     */
    public function getRowStatus(string $rateDate, string $source): string|bool
    {
        return $this->_erDayRepository->getRowStatus($rateDate, $source);
    }

    /**
     * @param string $rateDate
     * @param string $source
     * @param string $status
     * @return bool
     */
    public function updateStatus(string $rateDate, string $source, string $status): bool
    {
        return $this->_erDayRepository->updateStatus($rateDate, $source, $status);
    }
}
