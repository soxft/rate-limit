<?php

namespace RateLimit\Exception;

use RateLimit\Rate;

class RateExceededException extends \Throwable
{
    private string $identifier;
    private Rate $rate;

    /**
     * Create a new rate exceeded exception.
     *
     * @param string $identifier
     * @param Rate $rate
     * @return self
     */
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

    /**
     * Get the identifier that was exceeded.
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Get the rate that was exceeded.
     *
     * @return Rate
     */
    public function getRate(): Rate
    {
        return $this->rate;
    }
}
