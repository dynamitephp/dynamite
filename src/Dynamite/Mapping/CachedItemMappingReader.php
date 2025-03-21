<?php
declare(strict_types=1);

namespace Dynamite\Mapping;


use Psr\Cache\CacheItemInterface;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * Stores your mapping in cache for some time.
 * Use in production or clear up your cache every single item configuration change.
 * Requires symfony/cache.
 * @author pizzaminded <mikolajczajkowsky@gmail.com>
 * @license MIT
 */
class CachedItemMappingReader extends ItemMappingReader
{
    protected CacheInterface $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function getMappingFor(string $className): ItemMapping
    {
        $cacheKey = sprintf('dnmt.itmppng.%s', md5($className));
        return $this->cache->get($cacheKey, function (CacheItemInterface $item) use ($className) {
            $item->expiresAfter(3600);

            return parent::getMappingFor($className);
        });
    }
}