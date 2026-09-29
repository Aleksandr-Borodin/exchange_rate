<?php
/**
 * TradingDayService.php
 * Created: 29.09.2026
 * Author: Alex
 * Project: exchange_rate
 */

declare(strict_types=1);

namespace ExchangeRate\Application\Service;

use DateTimeImmutable;

class TradingDayService
{
    /**
     * @param int $days
     * @return array
     */
    public function getDaysList(int $days): array
    {
        $result = [];
        $date = new DateTimeImmutable('today');
        for ($i = 0; $i < $days; ++$i) {
            $result[$date->format('Y-m-d')] = 1;
            $date = $date->modify('-1 day');
        }
        return $result;
    }

    /**
     * @param string $rateDate
     * @return string
     */
    public function _preparePrevRateDate(string $rateDate): string
    {
        $date = new DateTimeImmutable($rateDate);
        for ($i = 1; $i <= 7; ++$i) {
            $prevDate = $date->modify("-{$i} day");
            if ((int) $prevDate->format('N') <= 5) {
                return $prevDate->format('Y-m-d');
            }
        }
        return '';
    }
}
