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
}
