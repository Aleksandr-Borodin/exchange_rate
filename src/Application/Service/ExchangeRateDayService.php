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
     * @param string $dayResult
     * @param string $source
     * @return bool
     */
    public function writeRateDate(string $dayResult, string $source): bool
    {
        return $this->_erDayRepository->writeRateDate($dayResult, $source);
    }
}
