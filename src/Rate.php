<?php

declare(strict_types=1);

namespace RateLimit;

use RateLimit\Exception\RateException;

class Rate
{
    /**
     * The number of operations per $interval.
     *
     * @var integer
     */
    protected int $operations;
    
    /**
     * The interval in seconds.
     *
     * @var integer
     */
    protected int $interval;

    /**
     * Create a new rate.
     *
     * @param integer $operations
     * @param integer $interval
     */
    final protected function __construct(int $operations, int $interval)
    {
        if ($operations <= 0) throw new RateException("Operations must greater than zero");
        if ($interval <= 0) throw new RateException("Interval must greater than zero");

        $this->operations = $operations;
        $this->interval = $interval;
    }

    /**
     * Create a new rate in seconds.
     * 
     * Rate::seconds(10, 60) will allow 10 operations per 60 seconds.
     *
     * @param integer $seconds
     * @param integer $operations
     * @return void
     */
    public static function seconds(int $operations, int $seconds)
    {
        return new static($operations, $seconds);
    }

    /**
     * Create a new rate in minutes.
     * 
     * Rate::minutes(10, 60) will allow 10 operations per 60 minutes.
     *
     * @param integer $minutes
     * @param integer $operations
     * @return void
     */
    public static function minutes(int $operations, int $minutes)
    {
        return new static($operations, $minutes * 60);
    }

    /**
     * Create a new rate in hours.
     * 
     * Rate::hours(10, 24) will allow 10 operations per 24 hours.
     *
     * @param integer $operations
     * @return void
     */
    public static function hours(int $operations, int $hours)
    {
        return new static($operations, $hours * 3600);
    }

    /**
     * Create a new rate per second.
     * 
     * Rate::perSecond(10) will allow 10 operations per second.
     *
     * @param integer $operations
     * @return void
     */
    public static function perSecond(int $operations)
    {
        return new static($operations, 1);
    }

    /**
     * Create a new rate per minute.
     * 
     * Rate::perMinute(10) will allow 10 operations per minute.
     *
     * @param integer $operations
     * @return void
     */
    public static function perMinute(int $operations)
    {
        return new static($operations, 60);
    }

    /**
     * Create a new rate per hour.
     * 
     * Rate::perHour(10) will allow 10 operations per hour.
     *
     * @param integer $operations
     * @return void
     */
    public static function perHour(int $operations)
    {
        return new static($operations, 3600);
    }

    /**
     * Create a new rate per day.
     * 
     * Rate::perDay(10) will allow 10 operations per day.
     *
     * @param integer $operations
     * @return void
     */
    public static function perDay(int $operations)
    {
        return new static($operations, 86400);
    }

    /**
     * Create a new rate in custom interval.
     * 
     * Rate::custom(10, 30) will allow 10 operations per 30 seconds.
     *
     * @param integer $operations
     * @param integer $interval
     * @return void
     */
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
