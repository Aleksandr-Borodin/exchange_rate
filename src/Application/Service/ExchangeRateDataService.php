<?php
/**
 * ExchangeRateDataService.php
 * Created: 29.09.2026
 * Author: Alex
 * Project: exchange_rate
 */

declare(strict_types=1);

namespace ExchangeRate\Application\Service;

use ExchangeRate\Infrastructure\Persistence\Doctrine\ExchangeRateDataRepository;

class ExchangeRateDataService
{
    /**
     * @param ExchangeRateDataRepository $_erDataRepository
     */
    public function __construct(private readonly ExchangeRateDataRepository $_erDataRepository) {}

    /**
     * @param string $rateDate
     * @param string $quoteCurrency
     * @param string $baseCurrency
     * @param string $source
     * @return float|false
     */
    public function getRow(string $rateDate, string $quoteCurrency, string $baseCurrency, string $source): float|false
    {
        return $this->_erDataRepository->getRow($rateDate, $quoteCurrency, $baseCurrency, $source);
    }
}
