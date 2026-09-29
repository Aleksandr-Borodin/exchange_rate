<?php
/**
 * GetRateStatus.php
 * Created: 29.09.2026
 * Author: Alex
 * Project: exchange_rate
 */

declare(strict_types=1);

namespace ExchangeRate\Domain\Enum;

enum GetRateStatus: string
{
    case UNKNOWN = 'unknown';
    case OK = 'ok';
    case CURRENT_RATE_UNAVAILABLE = 'current_rate_unavailable';
    case PREVIOUS_RATE_UNAVAILABLE = 'previous_rate_unavailable';
    case RATES_UNAVAILABLE = 'rates_unavailable';
    case CURRENT_RATE_PENDING = 'current_rate_pending';
    case PREVIOUS_RATE_PENDING = 'previous_rate_pending';
    case RATES_PENDING = 'rates_pending';
    case CURRENT_RATE_PROCESSING = 'current_rate_processing';
    case PREVIOUS_RATE_PROCESSING = 'previous_rate_processing';
    case RATES_PROCESSING = 'rates_processing';
    case CURRENT_RATE_FAILED = 'current_rate_failed';
    case PREVIOUS_RATE_FAILED = 'previous_rate_failed';
    case RATES_FAILED = 'rates_failed';

    /**
     * @param string $currentStatus
     * @param string $previousStatus
     * @return self
     */
    public static function getResultStatus(string $currentStatus, string $previousStatus): self
    {
        $statuses = [
            'completed' => [
                'completed' => self::OK,
                'unknown' => self::PREVIOUS_RATE_UNAVAILABLE,
                'pending' => self::PREVIOUS_RATE_PENDING,
                'processing' => self::PREVIOUS_RATE_PROCESSING,
                'failed' => self::PREVIOUS_RATE_FAILED,
            ],
            'unknown' => [
                'completed' => self::CURRENT_RATE_UNAVAILABLE,
                'unknown' => self::RATES_UNAVAILABLE,
                'pending' => self::CURRENT_RATE_UNAVAILABLE,
                'processing' => self::CURRENT_RATE_UNAVAILABLE,
                'failed' => self::CURRENT_RATE_UNAVAILABLE,
            ],
            'pending' => [
                'completed' => self::CURRENT_RATE_PENDING,
                'unknown' => self::CURRENT_RATE_PENDING,
                'pending' => self::RATES_PENDING,
                'processing' => self::CURRENT_RATE_PENDING,
                'failed' => self::CURRENT_RATE_PENDING,
            ],
            'processing' => [
                'completed' => self::CURRENT_RATE_PROCESSING,
                'unknown' => self::CURRENT_RATE_PROCESSING,
                'pending' => self::CURRENT_RATE_PROCESSING,
                'processing' => self::RATES_PROCESSING,
                'failed' => self::CURRENT_RATE_PROCESSING,
            ],
            'failed' => [
                'completed' => self::CURRENT_RATE_FAILED,
                'unknown' => self::CURRENT_RATE_FAILED,
                'pending' => self::CURRENT_RATE_FAILED,
                'processing' => self::CURRENT_RATE_FAILED,
                'failed' => self::RATES_FAILED,
            ],
        ];
        return $statuses[$currentStatus][$previousStatus]
            ?? self::UNKNOWN;
    }
}
