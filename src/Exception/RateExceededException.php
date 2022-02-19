<?php

namespace RateLimit\Exception;

use RateLimit\Rate;

class RateExceededException extends \Throwable
{
    private string $identifier;
    private Rate $rate;

    public static function for(string $identifier, Rate $rate): self
    {
        $exception = new self(sprintf(
            'Limit has been exceeded for identifier "%s".',
            $identifier
        ));

        $exception->identifier = $identifier;
        $exception->rate = $rate;

        return $exception;
    }

    public static function err(string $identifier, Rate $rate): self
    {
        $exception = new self(sprintf(
            'Limit has been exceeded for identifier "%s".',
            $identifier
        ));

        $exception->identifier = $identifier;
        $exception->rate = $rate;

        return $exception;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getRate(): Rate
    {
        return $this->rate;
    }
}
