<?php
/**
 * CbrClient.php
 * Created: 29.09.2026
 * Author: Alex
 * Project: exchange_rate
 */

declare(strict_types=1);

namespace ExchangeRate\Infrastructure\Cbr;

use DateTimeImmutable;
use Exception;

class CbrClient
{
    /**
     * @var string
     */
    private const URL = 'https://www.cbr.ru/scripts/XML_daily.asp';

    /**
     * @param string $rateDate
     * @return array
     */
    public function getRates(string $rateDate): array
    {
        try {
            $result = [];
            $date = new DateTimeImmutable($rateDate);
            $url = self::URL . '?date_req=' . $date->format('d/m/Y');
            $xml = @simplexml_load_file($url);
            if ($xml === false) {
                return $result;
            }
            foreach ($xml->Valute as $valute) {
                $result[] = [
                    'code' => strtoupper((string) $valute->CharCode),
                    'nominal' => (int) $valute->Nominal,
                    'name' => (string) $valute->Name,
                    'value' => (float) str_replace(
                        ',',
                        '.',
                        (string) $valute->Value
                    ),
                ];
            }
            return $result;
        } catch (Exception $e) {
            return [];
        }
    }
}
