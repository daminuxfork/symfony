<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Cache\Tests\Adapter;

use PHPUnit\Framework\SkippedTestSuiteError;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Exception\CacheException;
use Symfony\Component\Cache\Exception\InvalidArgumentException;

/**
 * @group integration
 */
class RedisAdapterSentinelTest extends AbstractRedisAdapterTest
{
    public static function setUpBeforeClass(): void
    {
        if (!class_exists(\Predis\Client::class)) {
            throw new SkippedTestSuiteError('The Predis\Client class is required.');
        }
        if (!$hosts = getenv('REDIS_SENTINEL_HOSTS')) {
            throw new SkippedTestSuiteError('REDIS_SENTINEL_HOSTS env var is not defined.');
        }
        if (!$service = getenv('REDIS_SENTINEL_SERVICE')) {
            throw new SkippedTestSuiteError('REDIS_SENTINEL_SERVICE env var is not defined.');
        }

        self::$redis = AbstractAdapter::createConnection('redis:?host['.str_replace(' ', ']&host[', $hosts).']', ['redis_sentinel' => $service, 'prefix' => 'prefix_']);
    }

    public function testInvalidDSNHasBothClusterAndSentinel()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot use both "redis_cluster" and "redis_sentinel" at the same time:');
        $dsn = 'redis:?host[redis1]&host[redis2]&host[redis3]&redis_cluster=1&redis_sentinel=mymaster';
        RedisAdapter::createConnection($dsn);
    }

    public function testSentinelRequiresPredis()
    {
        $this->expectException(CacheException::class);
        $this->expectExceptionMessage('Cannot use Redis Sentinel: class "Redis" does not extend "Predis\Client":');
        $dsn = 'redis:?host[redis1]&host[redis2]&host[redis3]&redis_cluster=1&redis_sentinel=mymaster';
        RedisAdapter::createConnection($dsn, ['class' => \Redis::class]);
    }
}
