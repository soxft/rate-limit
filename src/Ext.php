<?php

namespace RateLimit;

abstract class Ext
{
    protected \Redis $redis;
    protected Rate $rate;
    protected string $keyPrefix;

    protected function getCurrent(string $key): int
    {
        return (int) $this->redis->get($key);
    }

    protected function key(string $identifier): string
    {
        if ($this->keyPrefix === '') {
            $this->keyPrefix = 'ratelimit';
        }

        return "{$this->keyPrefix}:{$identifier}:{$this->rate->getInterval()}";
    }
}
