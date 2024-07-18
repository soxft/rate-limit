<?php

declare(strict_types=1);

namespace RateLimit;

use RateLimit\Exception\RateExceededException;

/**
 * Create a new rate limiter.
 * 
 * new RedisRateLimiter(Rate::seconds(10, 60), new \Redis());
 *
 * @param Rate $rate        rate to limit
 * @param \Redis $redis     redis instance
 * @param string $keyPrefix key prefix
 */
final class RateLimiter extends Ext
{
    public function __construct(Rate $rate, \Redis $redis, string $keyPrefix = 'limiter')
    {
        $this->rate = $rate;
        $this->redis = $redis;
        $this->keyPrefix = $keyPrefix;
    }

    /**
     * Limit the rate.
     *
     * Demo:
     * ```php
     * try {
     *     $redisLimiter = new RedisRateLimiter(Rate::seconds(10, 60), new \Redis());
     *     $redisLimiter->limit('user:1');
     * } catch (RateExceededException $e) {
     *     echo $e->getMessage();
     * }
     * ```
     * if the rate is exceeded, a RateExceededException will be thrown.
     * 
     * you can use `$redisLimiter->left('user:1')` to get the number of operations left.
     * 
     * @param string $identifier
     * @return void
     */
    public function limit(string $identifier): void
    {
        $key = $this->key($identifier);

        $current = $this->getCurrent($key);

        if ($current >= $this->rate->getOperations()) {
            throw rateExceededException::for($identifier, $this->rate);
        }

        $this->updateCounter($key);
    }

    /**
     * Limit the rate silently.
     *
     * Demo:
     * ```php
     * $redisLimiter = new RedisRateLimiter(Rate::seconds(10, 60), new \Redis());
     * $status = $redisLimiter->limitSilently('user:1');
     * 
     * if ($status->left() === 0) {
     *     echo 'Rate exceeded';
     * }
     * ```
     * 
     * difference from limit() is that limitSilently() will not throw an exception.
     * you can use the returned Status object to check the rate limit status.
     * 
     * @param string $identifier
     * @return Status
     */
    public function limitSilently(string $identifier): Status
    {
        $key = $this->key($identifier);

        $current = $this->getCurrent($key);

        if ($current < $this->rate->getOperations()) {
            $current = $this->updateCounter($key);
        }

        return new Status($identifier, $this->keyPrefix, $this->rate, $this->redis);
    }

    /**
     * Decrement the counter.
     * 
     * Demo:
     * ```php
     * $redisLimiter = new RedisRateLimiter(Rate::seconds(10, 60), new \Redis());
     * $redisLimiter->decr('user:1');
     * ```
     * 
     * you can use this method to decrement the counter.
     *
     * @param string $identifier
     * @return Status
     */
    public function decrSilently(string $identifier): Status
    {
        $key = $this->key($identifier);

        $current = $this->getCurrent($key);

        if ($current > 0) {
            $this->decrCounter($key);
        }

        return new Status($identifier, $this->keyPrefix, $this->rate, $this->redis);
    }

    /**
     * Get the number of operations left.
     *
     * Demo:
     * ```php
     * $redisLimiter = new RedisRateLimiter(Rate::seconds(10, 60), new \Redis());
     * echo $redisLimiter->left('user:1');
     * ```
     * 
     * @param string $identifier
     * @return integer
     */
    public function left(string $identifier): int
    {
        $key = $this->key($identifier);

        $left = $this->rate->getOperations() - $this->getCurrent($key);
        return $left >= 0 ? $left : 0;
    }


    /**
     * Reset the rate limit.
     * 
     * Demo:
     * ```php
     * $redisLimiter = new RedisRateLimiter(Rate::seconds(10, 60), new \Redis());
     * $redisLimiter->reset('user:1');
     * ```
     * 
     * you can use this method to reset the rate limit.
     * 
     *
     * @param string $identifier
     * @return void
     */
    public function reset(string $identifier)
    {
        $key = $this->key($identifier);
        $this->redis->del($key);
    }

    /**
     * Update the counter.
     *
     * @param string $key
     * @return integer
     */
    private function updateCounter(string $key): int
    {
        $current = $this->redis->incr($key);

        if ($current === 1) {
            $this->redis->expire($key, $this->rate->getInterval());
        }

        return $current;
    }


    /**
     * decr the counter.
     *
     * @param string $key
     * @return integer
     */
    private function decrCounter(string $key): int
    {
        $current = $this->redis->decr($key);

        if ($current <= 0) {
            $this->redis->del($key);
        }

        return $current;
    }
}
