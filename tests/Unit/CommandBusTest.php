<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2020 - 2026 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Communitales\Test\Unit\Component\CommandBus;

use Communitales\Component\CommandBus\CommandBus;
use Communitales\Test\Unit\Component\CommandBus\Fixture\BrokenCommandHandler;
use Exception;
use Generator;
use IteratorAggregate;
use LogicException;
use Override;
use PHPUnit\Framework\TestCase;

/**
 * Class CommandBusTest
 */
class CommandBusTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testConstructorException(): void
    {
        $iterator = new /** @implements IteratorAggregate<BrokenCommandHandler> */ class () implements IteratorAggregate {
            #[Override]
            public function getIterator(): Generator
            {
                yield new BrokenCommandHandler();
            }
        };

        $this->expectException(LogicException::class);
        $this->expectExceptionMessageIsOrContains('Constructor is broken for testing');

        new CommandBus($iterator);
    }
}
