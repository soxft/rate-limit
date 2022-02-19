<?php

namespace RateLimit;

class Status extends Ext
{
    private $identifier;

    function __construct(
        string $identifier,
        string $keyPrefix,
        Rate $rate,
        \Redis $redis
    ) {
        $this->keyPrefix = $keyPrefix;
        $this->identifier = $identifier;
        $this->rate = $rate;
        $this->redis = $redis;
    }

    function left(): int
    {
        $key = $this->key($this->identifier);
        $left = $this->rate->getOperations() - $this->getCurrent($key);
        return $left >= 0 ? $left : 0;
    }

    function reset()
    {
        $this->redis->del($this->key($this->identifier));
    }
}
