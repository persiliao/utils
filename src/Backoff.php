<?php

declare(strict_types=1);
/**
 * @author Persi.Liao
 * @email xiangchu.liao@gmail.com
 *
 * @see https://www.github.com/persiliao
 */

namespace PersiLiao\Utils;

use PersiLiao\Utils\Exception\InvalidArgumentException;

class Backoff
{
    /**
     * Max backoff.
     */
    private const CAP = 60 * 1000; // 1 minute

    /**
     * @var int
     */
    private $firstMs;

    /**
     * Backoff interval.
     *
     * @var int
     */
    private $currentMs;

    /**
     * @param int the first backoff in milliseconds
     */
    public function __construct(int $firstMs = 0)
    {
        if ($firstMs < 0) {
            throw new InvalidArgumentException('first backoff interval must be greater or equal than 0');
        }

        if ($firstMs > Backoff::CAP) {
            throw new InvalidArgumentException(sprintf('first backoff interval must be less or equal than %d milliseconds', self::CAP));
        }

        $this->firstMs = $firstMs;
        $this->currentMs = $firstMs;
    }

    /**
     * Sleep until the next execution.
     */
    public function sleep(): void
    {
        if (0 === $this->currentMs) {
            return;
        }

        usleep($this->currentMs * 1000);

        // update backoff using Decorrelated Jitter
        // see: https://aws.amazon.com/blogs/architecture/exponential-backoff-and-jitter/
        $this->currentMs = random_int($this->firstMs, $this->currentMs * 3);

        if ($this->currentMs > self::CAP) {
            $this->currentMs = self::CAP;
        }
    }

    /**
     * Get the next backoff for logging, etc.
     *
     * @return int next backoff
     */
    public function nextBackoff(): int
    {
        return $this->currentMs;
    }
}
