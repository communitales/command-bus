<?php

/**
 * @copyright   Copyright (c) 2024 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Communitales\Test\Unit\Component\CommandBus\Handler;

use Communitales\Component\CommandBus\Handler\Result\ErrorResult;
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

    public function testHandleSuccess(): void
    {
        $command = new TestCommand('success');

        $result = $this->commandHandler->handle($command);
        if (!$result instanceof SuccessResult) {
            self::fail('Result should be SuccessResult. Message: '.(string)$result->getStatusMessage());
        }

        $this->assertInstanceOf(SuccessResult::class, $result);
    }

    public function testHandleError(): void
    {
        $command = new TestCommand('error');

        $result = $this->commandHandler->handle($command);
        $this->assertInstanceOf(ErrorResult::class, $result);
    }
}
