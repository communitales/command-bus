<?php

/*
 * SPDX-FileCopyrightText: 2020 Communitales GmbH
 *
 * SPDX-License-Identifier: MIT
 */

namespace Communitales\Component\CommandBus;

use Communitales\Component\CommandBus\Command\CommandInterface;
use Communitales\Component\CommandBus\Handler\CommandHandlerInterface;
use Communitales\Component\CommandBus\Handler\Result\CommandResult;
use Communitales\Component\CommandBus\Handler\Result\CommandResultException;
use Communitales\Component\CommandBus\Handler\Result\CommandResultInterface;
use Communitales\Component\Log\ExceptionLoggerInterface;
use Communitales\Component\StatusBus\StatusBusInterface;
use Communitales\Component\StatusBus\StatusMessage;
use Doctrine\DBAL\Exception as DbalException;
use Doctrine\ORM\Exception\ManagerException;
use Doctrine\ORM\Exception\ORMException;
use LogicException;
use Override;
use Symfony\Contracts\Service\ServiceCollectionInterface;
use Throwable;

use function sprintf;

/**
 * CommandBus to handle commands via handlers.
 */
class CommandBus implements CommandBusInterface
{
    private string $statusMessageDatabaseError = 'status_message.database_error';

    private string $statusMessageFatalError = 'status_message.fatal_error';

    /** @param ServiceCollectionInterface<CommandHandlerInterface<*>> $handlers */
    public function __construct(
        private readonly ServiceCollectionInterface $handlers,
        private readonly ?ExceptionLoggerInterface $exceptionLogger = null,
        private readonly ?StatusBusInterface $statusBus = null
    ) {
    }

    /**
     * This method can be used to set a different translation key or set up a plain text message without i18n.
     */
    public function setStatusMessageDatabaseError(string $statusMessageDatabaseError): void
    {
        $this->statusMessageDatabaseError = $statusMessageDatabaseError;
    }

    /**
     * This method can be used to set a different translation key or set up a plain text message without i18n.
     */
    public function setStatusMessageFatalError(string $statusMessageFatalError): void
    {
        $this->statusMessageFatalError = $statusMessageFatalError;
    }

    #[Override]
    public function dispatch(
        CommandInterface $command,
        bool $displayStatusMessage = true
    ): CommandResultInterface {
        $commandClass = $command::class;

        try {
            if (!$this->handlers->has($commandClass)) {
                throw CanNotDispatchCommandException::forClass($commandClass);
            }

            $handler = $this->requireCommandHandler($this->handlers->get($commandClass), $command);
            $result = $handler->handle($command);
        } catch (CommandResultException $exception) {
            $result = $exception->commandResult;
        } catch (DbalException|ORMException|ManagerException $exception) {
            $this->logException($exception);
            $result = CommandResult::failed(StatusMessage::error($this->statusMessageDatabaseError));
        } catch (Throwable $throwable) {
            $this->logException($throwable);
            $result = CommandResult::failed(StatusMessage::error($this->statusMessageFatalError));
        }

        if ($displayStatusMessage) {
            $this->publishStatusMessage($result);
        }

        return $result;
    }

    private function publishStatusMessage(CommandResultInterface $result): void
    {
        try {
            $statusMessage = $result->getStatusMessage();

            if ($statusMessage instanceof StatusMessage && $this->statusBus instanceof StatusBusInterface) {
                $this->statusBus->publish($statusMessage);
            }
        } catch (Throwable $throwable) {
            $this->logException($throwable);
        }
    }

    private function logException(Throwable $throwable): void
    {
        try {
            $this->exceptionLogger?->logException($throwable);
        } catch (Throwable) {
            // Logging must never prevent the command bus from returning a result.
        }
    }

    /**
     * @template TCommand of CommandInterface
     *
     * @param TCommand $command
     *
     * @return CommandHandlerInterface<TCommand>
     */
    private function requireCommandHandler(mixed $handler, CommandInterface $command): CommandHandlerInterface
    {
        if (!$handler instanceof CommandHandlerInterface) {
            throw new LogicException(sprintf(
                'The handler configured for command "%s" does not implement CommandHandlerInterface.',
                $command::class
            ));
        }

        return $handler;
    }
}
