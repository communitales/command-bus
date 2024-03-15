<?php

/**
 * @copyright   Copyright (c) 2024 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Communitales\Test\Unit\Component\CommandBus\Handler;

use Communitales\Component\CommandBus\Handler\Result\SuccessResult;
use Override;
use PHPUnit\Framework\TestCase;

/**
 * Class CommandHandlerTest
 */
class CommandHandlerTest extends TestCase
{
    private TestCommandHandler $commandHandler;

    #[Override]
    protected function setUp(): void
    {
        $this->commandHandler = new TestCommandHandler();
    }

    public function testHandle(): void
    {
        $command = new TestCommand('status.success');

        $result = $this->commandHandler->handle($command);
        $this->assertInstanceOf(SuccessResult::class, $result);
    }

}
