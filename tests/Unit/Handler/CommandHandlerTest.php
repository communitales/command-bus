<?php

declare(strict_types=1);

/*
 * SPDX-FileCopyrightText: 2020 Communitales GmbH
 *
 * SPDX-License-Identifier: MIT
 */

namespace Communitales\Test\Unit\Component\CommandBus\Handler;

use Communitales\Component\CommandBus\Handler\Result\CommandResult;
use Communitales\Component\CommandBus\Handler\Result\CommandResultStatus;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Class CommandHandlerTest
 */
#[CoversClass(CommandResult::class)]
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

        $this->assertSame(CommandResultStatus::Success, $result->getStatus());
        $this->assertSame('success', $result->getStatusMessage()?->getMessage());
    }

    public function testHandleError(): void
    {
        $command = new TestCommand('error');

        $result = $this->commandHandler->handle($command);
        $this->assertSame(CommandResultStatus::Error, $result->getStatus());
        $this->assertSame('error', $result->getStatusMessage()?->getMessage());
    }
}
