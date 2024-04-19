<?php

/**
 * @copyright   Copyright (c) 2024 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Communitales\Test\Unit\Component\CommandBus\Fixture;

use Override;
use Closure;
use Countable;
use IteratorAggregate;
use Traversable;

use function is_int;

/**
 * Based on Symfony\Component\DependencyInjection\Argument\RewindableGenerator
 * @implements IteratorAggregate<mixed>
 */
class RewindableGenerator implements Countable, IteratorAggregate
{
    private Closure $generator;

    private Closure|int $count;

    public function __construct(callable $generator, int|callable $count)
    {
        $this->generator = $generator(...);
        $this->count = is_int($count) ? $count : $count(...);
    }

    #[Override]
    public function getIterator(): Traversable
    {
        $g = $this->generator;

        return $g();
    }

    #[Override]
    public function count(): int
    {
        if (!is_int($count = $this->count)) {
            $this->count = $count();
        }

        return $this->count;
    }
}
