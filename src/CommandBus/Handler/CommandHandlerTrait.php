<?php
/**
 * @copyright   Copyright (c) 2020 - 2024 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Communitales\Component\CommandBus\Handler;

use App\Domain\Command\Portal\BufferLike\CreatePostingLikeCommand;
use Communitales\Component\CommandBus\Command\CommandInterface;
use Communitales\Component\CommandBus\Handler\Result\CommandHandlerResultInterface;
use Communitales\Component\CommandBus\Handler\Result\ErrorResult;
use Communitales\Component\StatusBus\StatusMessage;
use Symfony\Component\Translation\TranslatableMessage;

use function lcfirst;
use function method_exists;
use function sprintf;
use function str_replace;
use function strrpos;
use function substr;

/**
 * Some methods to help implementing the HandlerInterface.
 */
trait CommandHandlerTrait
{
    /**
     * Provides some convenience logic to avoid boilerplate code.
     *
     * If the command class is named `InsertCustomerCommand` a method `insertCustomer($command)` will be called.
     */
    public function handle(CommandInterface $command): CommandHandlerResultInterface
    {
        $handleMethod = substr($command::class, strrpos($command::class, '\\') + 1);
        $handleMethod = lcfirst($handleMethod);
        $handleMethod = str_replace('Command', '', $handleMethod);

        if (method_exists($this, $handleMethod)) {
            /** @var CommandHandlerResultInterface */
            return $this->$handleMethod($command);
        }

        return $this->canNotHandle($command);
    }

    /**
     * This method can be used to raise an error as last statement of the handle method.
     */
    private function canNotHandle(CommandInterface $command): ErrorResult
    {
        return new ErrorResult(
            StatusMessage::createErrorMessage(
                new TranslatableMessage(
                    sprintf('Handler %s can not handle %s', $this::class, $command::class)
                )
            )
        );
    }
}
