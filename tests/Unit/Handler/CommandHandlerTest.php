<?php

declare(strict_types=1);

/*
 * SPDX-FileCopyrightText: 2020 Communitales GmbH
 *
 * SPDX-License-Identifier: MIT
 */

namespace Communitales\Test\Unit\Component\CommandBus\Handler;

use Communitales\Component\CommandBus\Handler\Result\AbstractResult;
use Communitales\Component\CommandBus\Handler\Result\ErrorResult;
use Communitales\Component\CommandBus\Handler\Result\SuccessResult;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Class CommandHandlerTest
 */
#[CoversClass(AbstractResult::class)]
final class CommandHandlerTest extends TestCase
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

        $this->assertInstanceOf(SuccessResult::class, $result, 'Result should be SuccessResult. Message: '.$result->getStatusMessage());
    }

    public function testHandleError(): void
    {
        $command = new TestCommand('error');

        $result = $this->commandHandler->handle($command);
        $this->assertInstanceOf(ErrorResult::class, $result);
    }
}
