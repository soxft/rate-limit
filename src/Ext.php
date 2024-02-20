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
        } else {
            $this->keyPrefix = "ratelimit:{$this->keyPrefix}";
        }


        // 如何 存在 : 结尾 则删除 :
        if (str_ends_with($this->keyPrefix, ':')) {
            $this->keyPrefix = substr($this->keyPrefix, 0, -1);
        }

        // 用户传入的 identifier 以 : 开头 则删除 :
        if (str_starts_with($identifier, ':')) {
            $identifier = substr($identifier, 1);
        }

        // 用户传入的 identifier 以 : 结尾 则删除 :
        if (str_ends_with($identifier, ':')) {
            $identifier = substr($identifier, 0, -1);
        }

        return "{$this->keyPrefix}:{$identifier}:{$this->rate->getInterval()}";
    }
}
