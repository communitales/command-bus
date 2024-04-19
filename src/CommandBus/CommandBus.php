<?php

/**
 * @copyright   Copyright (c) 2020 - 2024 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Communitales\Component\CommandBus;

use Communitales\Component\CommandBus\Command\CommandInterface;
use Communitales\Component\CommandBus\Handler\CommandHandlerInterface;
use Communitales\Component\CommandBus\Handler\Result\AbstractResult;
use Communitales\Component\CommandBus\Handler\Result\CommandHandlerResultInterface;
use Communitales\Component\CommandBus\Handler\Result\DatabaseErrorResult;
use Communitales\Component\CommandBus\Handler\Result\FatalErrorResult;
use Communitales\Component\Log\LogAwareTrait;
use Communitales\Component\StatusBus\StatusBusAwareInterface;
use Communitales\Component\StatusBus\StatusBusAwareTrait;
use Communitales\Component\StatusBus\StatusMessage;
use Doctrine\DBAL\Exception as DbalException;
use Doctrine\ORM\Exception\ManagerException;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use IteratorAggregate;
use Override;
use Psr\Log\LoggerAwareInterface;
use Symfony\Component\Translation\TranslatableMessage;
use Throwable;

/**
 * CommandBus to handle commands via handlers.
 */
class CommandBus implements CommandBusInterface, LoggerAwareInterface, StatusBusAwareInterface
{
    use StatusBusAwareTrait;
    use LogAwareTrait;

    /**
     * @var CommandHandlerInterface[]
     */
    private array $commandHandlers = [];

    private TranslatableMessage $statusMessageDatabaseError;

    private TranslatableMessage $statusMessageFatalError;

    /**
     * @param IteratorAggregate<CommandHandlerInterface> $commandBusHandlers
     *
     * @throws Exception
     */
    public function __construct(iterable $commandBusHandlers)
    {
        foreach ($commandBusHandlers->getIterator() as $commandHandler) {
            $this->addCommandHandler($commandHandler);
        }

        $this->statusMessageDatabaseError = new TranslatableMessage('status_message.database_error');
        $this->statusMessageFatalError = new TranslatableMessage('status_message.fatal_error');
    }

    public function addCommandHandler(CommandHandlerInterface $commandHandler): void
    {
        $this->commandHandlers[] = $commandHandler;
    }

    /**
     * This method can be used to set a different translation key or set up a plain text message without i18n.
     */
    public function setStatusMessageDatabaseError(string $statusMessageDatabaseError): void
    {
        $this->statusMessageDatabaseError = new TranslatableMessage($statusMessageDatabaseError);
    }

    /**
     * This method can be used to set a different translation key or set up a plain text message without i18n.
     */
    public function setStatusMessageFatalError(string $statusMessageFatalError): void
    {
        $this->statusMessageFatalError = new TranslatableMessage($statusMessageFatalError);
    }

    #[Override]
    public function dispatch(
        CommandInterface $command,
        bool $displayStatusMessage = true
    ): CommandHandlerResultInterface {
        $result = null;

        try {
            foreach ($this->commandHandlers as $commandHandler) {
                if ($commandHandler->canHandle($command)) {
                    $result = $commandHandler->handle($command);
                }
            }
        } catch (DbalException|ORMException|ManagerException $exception) {
            $this->logException($exception);
            $result = new DatabaseErrorResult(StatusMessage::createErrorMessage($this->statusMessageDatabaseError));
        } catch (Throwable $throwable) {
            $this->logException($throwable);
            $result = new FatalErrorResult(StatusMessage::createErrorMessage($this->statusMessageFatalError));
        }

        // If a StatusBus was set, then send StatusMessage of the result.
        if ($result instanceof AbstractResult) {
            $statusMessage = $result->statusMessage;

            if (
                $displayStatusMessage
                && $statusMessage instanceof StatusMessage
                && isset($this->statusBus)
            ) {
                $this->statusBus->addStatusMessage($statusMessage);
            }

            return $result;
        }

        if ($result instanceof CommandHandlerResultInterface) {
            return $result;
        }

        throw CanNotDispatchCommandException::forClass($command::class);
    }
}
