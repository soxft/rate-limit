<?php

namespace Api\Includes\RateLimit\Exception;

use \Api\Includes\RateLimit\Rate;

class RateExceededException extends \RuntimeException
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
