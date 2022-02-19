<?php

declare(strict_types=1);

namespace RateLimit;

use RateLimit\Exception\RateException;

class Rate
{
    protected int $operations;
    protected int $interval;

    final protected function __construct(int $operations, int $interval)
    {
        if ($operations <= 0) throw new RateException("Operations must greater than zero");
        if ($interval <= 0) throw new RateException("Interval must greater than zero");

        $this->operations = $operations;
        $this->interval = $interval;
    }

    public static function seconds(int $seconds, int $operations)
    {
        return new static($operations, $seconds);
    }

    public static function minutes(int $minutes, int $operations)
    {
        return new static($operations, $minutes * 60);
    }

    public static function perSecond(int $operations)
    {
        return new static($operations, 1);
    }

    public static function perMinute(int $operations)
    {
        return new static($operations, 60);
    }

    public static function perHour(int $operations)
    {
        return new static($operations, 3600);
    }

    public static function perDay(int $operations)
    {
        return new static($operations, 86400);
    }

    public static function custom(int $operations, int $interval)
    {
        return new static($operations, $interval);
    }

    public function getOperations(): int
    {
        return $this->operations;
    }

    public function getInterval(): int
    {
        return $this->interval;
    }
}
