<?php
/**
 * ExchangeRateData.php
 * Created: 29.09.2026
 * Author: Alex
 * Project: exchange_rate
 */

declare(strict_types=1);

namespace ExchangeRate\Domain\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'exchange_rate_data')]
#[ORM\UniqueConstraint(
    name: 'uniq_exchange_rate_data_dt_sour_cur',
    columns: ['rate_date', 'source', 'base_currency', 'quote_currency']
)]
class ExchangeRateData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'rate_id')]
    private int $rateId;

    #[ORM\Column(name: 'rate_date', type: 'date_immutable')]
    private DateTimeImmutable $rateDate;

    #[ORM\Column(
        name: 'source',
        length: 30,
        options: ['default' => 'cbr']
    )]
    private string $source;

    #[ORM\Column(name: 'base_currency', length: 3)]
    private string $baseCurrency;

    #[ORM\Column(name: 'quote_currency', length: 3)]
    private string $quoteCurrency;

    #[ORM\Column(type: 'decimal', precision: 20, scale: 8)]
    private string $rateValue;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    /**
     * @param DateTimeImmutable $rateDate
     * @param string $source
     * @param string $baseCurrency
     * @param string $quoteCurrency
     * @param string $rateValue
     */
    public function __construct(DateTimeImmutable $rateDate, string $baseCurrency, string $quoteCurrency, string $rateValue, string $source = 'cbr')
    {
        $this->rateDate = $rateDate;
        $this->source = strtolower($source);
        $this->baseCurrency = strtoupper($baseCurrency);
        $this->quoteCurrency = strtoupper($quoteCurrency);
        $this->rateValue = $rateValue;
        $this->createdAt = new DateTimeImmutable();
    }

    /**
     * @return int
     */
    public function getRateId(): int
    {
        return $this->rateId;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getRateDate(): DateTimeImmutable
    {
        return $this->rateDate;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @return string
     */
    public function getBaseCurrency(): string
    {
        return $this->baseCurrency;
    }

    /**
     * @return string
     */
    public function getQuoteCurrency(): string
    {
        return $this->quoteCurrency;
    }

    /**
     * @return string
     */
    public function getRateValue(): string
    {
        return $this->rateValue;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
