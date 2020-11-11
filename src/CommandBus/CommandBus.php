<?php

/**
 * @copyright   Copyright (c) 2020 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Communitales\Component\CommandBus;

use Communitales\Component\CommandBus\Command\CommandInterface;
use Communitales\Component\CommandBus\Handler\CommandHandlerInterface;
use Communitales\Component\CommandBus\Handler\Result\CommandHandlerResultInterface;
use Communitales\Component\CommandBus\Handler\Result\DatabaseErrorResult;
use Communitales\Component\CommandBus\Handler\Result\FatalErrorResult;
use Communitales\Component\Log\LogAwareTrait;
use Communitales\Component\StatusBus\StatusBusAwareInterface;
use Communitales\Component\StatusBus\StatusBusAwareTrait;
use Communitales\Component\StatusBus\StatusMessage;
use IteratorAggregate;
use Psr\Log\LoggerAwareInterface;
use Throwable;
use function get_class;

/**
 * CommandBus to handle commands via handlers.
 */
class CommandBus implements CommandBusInterface, StatusBusAwareInterface, LoggerAwareInterface
{

    use StatusBusAwareTrait;
    use LogAwareTrait;

    /**
     * @var CommandHandlerInterface[]
     */
    private array $commandHandlers = [];

    /**
     * @var string
     */
    private string $statusMessageDatabaseError = 'status_message.database_error';

    /**
     * @var string
     */
    private string $statusMessageFatalError = 'status_message.fatal_error';

    /**
     * @param IteratorAggregate<CommandHandlerInterface> $commandBusHandlers
     */
    public function __construct(iterable $commandBusHandlers)
    {
        try {
            /** @var CommandHandlerInterface $commandHandler */
            foreach ($commandBusHandlers->getIterator() as $commandHandler) {
                $this->addCommandHandler($commandHandler);
            }
        } catch (Throwable $exception) {
            $this->logException($exception);
        }
    }

    /**
     * @param CommandHandlerInterface $commandHandler
     */
    public function addCommandHandler(CommandHandlerInterface $commandHandler): void
    {
        $this->commandHandlers[] = $commandHandler;
    }

    /**
     * This method can be used to set a different translation key or setup a plain text message without i18n.
     *
     * @param string $statusMessageDatabaseError
     */
    public function setStatusMessageDatabaseError(string $statusMessageDatabaseError): void
    {
        $this->statusMessageDatabaseError = $statusMessageDatabaseError;
    }

    /**
     * This method can be used to set a different translation key or setup a plain text message without i18n.
     *
     * @param string $statusMessageFatalError
     */
    public function setStatusMessageFatalError(string $statusMessageFatalError): void
    {
        $this->statusMessageFatalError = $statusMessageFatalError;
    }

    /**
     * @param CommandInterface $command
     *
     * @return CommandHandlerResultInterface
     */
    public function dispatch(CommandInterface $command): CommandHandlerResultInterface
    {
        $commandClass = get_class($command);
        $result = null;

        try {
            foreach ($this->commandHandlers as $commandHandler) {
                if ($commandHandler->canHandle($command)) {
                    $result = $commandHandler->handle($command);
                }
            }
        } catch (\Doctrine\ORM\ORMException $exception) {
            $this->logException($exception);

            $result = new DatabaseErrorResult(StatusMessage::createErrorMessage($this->statusMessageDatabaseError));
        } catch (Throwable $exception) {
            $this->logException($exception);

            $result = new FatalErrorResult(StatusMessage::createErrorMessage($this->statusMessageFatalError));
        }

        if ($result !== null) {

            // If a StatusBus was set, then send StatusMessage of result
            $statusMessage = $result->getStatusMessage();
            if ($statusMessage !== null && $this->statusBus !== null) {
                $this->statusBus->addStatusMessage($statusMessage);
            }

            return $result;
        }

        throw CanNotDispatchCommandException::forClass($commandClass);
    }

}
