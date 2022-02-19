<?php

declare(strict_types=1);

namespace Api\Includes\RateLimit;

use \Api\Includes\RateLimit\Exception\RateExceededException;

final class RateLimiter extends Ext
{
    public function __construct(Rate $rate, \Redis $redis, string $keyPrefix = '')
    {
        $this->rate = $rate;
        $this->redis = $redis;
        $this->keyPrefix = $keyPrefix . 'ratelimit:';
    }

    public function limit(string $identifier): void
    {
        $key = $this->key($identifier);

        $current = $this->getCurrent($key);

        if ($current >= $this->rate->getOperations()) {
            throw rateExceededException::for($identifier, $this->rate);
        }

        $this->updateCounter($key);
    }

    public function limitSilently(string $identifier): Status
    {
        $key = $this->key($identifier);

        $current = $this->getCurrent($key);

        if ($current < $this->rate->getOperations()) {
            $current = $this->updateCounter($key);
        }

        return new Status($identifier, $this->keyPrefix, $this->rate, $this->redis);
    }

    public function left(string $identifier): int
    {
        $key = $this->key($identifier);
        $left = $this->rate->getOperations() - $this->getCurrent($key);
        return $left >= 0 ? $left : 0;
    }

    public function reset(string $identifier)
    {
        $key = $this->key($identifier);
        $this->redis->del($key);
    }

    private function updateCounter(string $key): int
    {
        $current = $this->redis->incr($key);

        if ($current === 1) {
            $this->redis->expire($key, $this->rate->getInterval());
        }

        return $current;
    }
}
