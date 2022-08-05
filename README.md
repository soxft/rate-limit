# Rate Limit

> 基于Redis 的 PHP 速率限制
 
## Installation

```bash
composer require soxft/rate-limit
```

## Usage

**Terminating rate limiter**

```php
use RateLimit\Rate;
use RateLimit\RateLimiter;
use RateLimit\Exception\LimitExceededException;

$rateLimiter = new RateLimiter(Rate::perMinute(100), new \Redis());

$apiKey = 'abc123'; // 用户标识

try {
    $rateLimiter->limit($apiKey);
    
    //on success
} catch (LimitExceededException $exception) {
   //on limit exceeded
}
```

**Silent rate limiter**

```php
use RateLimit\Rate;
use RateLimit\RateLimiter;

$rateLimiter = new RateLimiter(Rate::perMinute(100), new \Redis());

$ipAddress = '192.168.1.2';
$status = $rateLimiter->limitSilently($ipAddress);

echo $status->left(); //99
```

## License

Released under MIT License - see the [License File](LICENSE) for details.

Secondary development based on [nikolaposa/rate-limie](https://github.com/nikolaposa/rate-limit)
