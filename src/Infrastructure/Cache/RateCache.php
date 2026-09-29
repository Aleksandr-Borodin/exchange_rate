<?php
/**
 * RateCache.php
 * Created: 29.09.2026
 * Author: Alex
 * Project: exchange_rate
 */

declare(strict_types=1);

namespace ExchangeRate\Infrastructure\Cache;

use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Contracts\Cache\CacheInterface;

class RateCache
{
    /**
     * @var string
     */
    private const CACHE_PREFIX = 'rate';
    
    private CacheInterface $_cache;

    /**
     * @param string $dsn
     * @param int $ttl
     */
    public function __construct(string $dsn, private readonly int $ttl = 31536000)
    {
        $this->_cache = new RedisAdapter(
            RedisAdapter::createConnection($dsn)
        );
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        $item = $this->_cache->getItem($key);
        return $item->isHit();
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed
    {
        $item = $this->_cache->getItem($key);
        if (!$item->isHit()) {
            return null;
        }
        return $item->get();
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        $item = $this->_cache->getItem($key);
        $item->set($value);
        $item->expiresAfter($this->ttl);
        $this->_cache->save($item);
    }

    /**
     * @param string $key
     * @return void
     */
    public function delete(string $key): void
    {
        $this->_cache->deleteItem($key);
    }

    /**
     * @param string $rateDate
     * @param string $quoteCurrency
     * @param string $baseCurrency
     * @param string $source
     * @return string
     */
    public function prepareKey(string $rateDate, string $quoteCurrency, string $baseCurrency, string $source): string
    {
        return self::CACHE_PREFIX . "-{$rateDate}-{$quoteCurrency}-{$baseCurrency}-{$source}";
    }
}
