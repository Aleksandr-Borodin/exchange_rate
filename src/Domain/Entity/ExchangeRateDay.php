<?php
/**
 * ExchangeRateDay.php
 * Created: 29.09.2026
 * Author: Alex
 * Project: exchange_rate
 */

declare(strict_types=1);

namespace ExchangeRate\Domain\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use ExchangeRate\Domain\Enum\ExchangeRateDayStatus;

#[ORM\Entity]
#[ORM\Table(name: 'exchange_rate_day')]
#[ORM\UniqueConstraint(
    name: 'uniq_exchange_rate_day_date_source',
    columns: ['rate_date', 'source']
)]
class ExchangeRateDay
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'day_id')]
    private int $dayId;

    #[ORM\Column(name: 'rate_date', type: 'date_immutable')]
    private DateTimeImmutable $rateDate;

    #[ORM\Column(
        name: 'source',
        length: 30,
        options: ['default' => 'cbr']
    )]
    private string $source;

    #[ORM\Column(
        name: 'status',
        type: 'string',
        length: 20,
        enumType: ExchangeRateDayStatus::class
    )]
    private ExchangeRateDayStatus $status;

    #[ORM\Column(
        name: 'attempts',
        options: ['default' => 0]
    )]
    private int $attempts;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    /**
     * @param DateTimeImmutable $rateDate
     * @param string $source
     */
    public function __construct(DateTimeImmutable $rateDate, string $source = 'cbr')
    {
        $this->rateDate = $rateDate;
        $this->status = ExchangeRateDayStatus::PENDING;
        $this->attempts = 0;
        $this->source = strtolower($source);
        $dt = new DateTimeImmutable();
        $this->createdAt = $dt;
        $this->updatedAt = $dt;
    }

    /**
     * @return void
     */
    public function incrementAttempts(): void
    {
        ++$this->attempts;
    }

    /**
     * @return void
     */
    public function markProcessing(): void
    {
        $this->status = ExchangeRateDayStatus::PROCESSING;
    }

    /**
     * @return void
     */
    public function markCompleted(): void
    {
        $this->status = ExchangeRateDayStatus::COMPLETED;
    }

    /**
     * @return void
     */
    public function markFailed(): void
    {
        $this->status = ExchangeRateDayStatus::FAILED;
    }

    /**
     * @return int
     */
    public function getDayId(): int
    {
        return $this->dayId;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getRateDate(): DateTimeImmutable
    {
        return $this->rateDate;
    }

    /**
     * @return ExchangeRateDayStatus
     */
    public function getStatus(): ExchangeRateDayStatus
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getAttempts(): int
    {
        return $this->attempts;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
