# Communitales Command Bus Component

A small synchronous command bus with a uniform result contract and lazy command handlers.

## Setup

```bash
composer require communitales/command-bus
```

Enable the bundle in `config/bundles.php`:

```php
use Communitales\Component\CommandBus\CommandBusBundle;

return [
    CommandBusBundle::class => ['all' => true],
];
```

Application services must use Symfony autoconfiguration (enabled by default):

```yaml
# config/services.yaml
services:
    _defaults:
        autowire: true
        autoconfigure: true

    App\:
        resource: '../src/'

    Communitales\Component\Log\ExceptionLogger: ~
    Communitales\Component\Log\ExceptionLoggerInterface: '@Communitales\Component\Log\ExceptionLogger'
```

Merge these entries into the existing configuration. `ExceptionLogger` requires a
`Psr\Log\LoggerInterface` service; register one if your application does not provide it.

The bundle registers `CommandBusInterface` and discovers handlers through the
`#[AsCommandHandler]` attribute. Handlers are stored in a lazy service locator indexed by the
command class. Only the handler selected by `dispatch()` is instantiated.

## Usage

Define a command:

```php
namespace App\Domain\Command\Customer;

use App\Entity\Customer;
use Communitales\Component\CommandBus\Command\CommandInterface;

final readonly class CreateCustomerCommand implements CommandInterface
{
    public function __construct(public Customer $customer)
    {
    }
}
```

Define exactly one handler for the command:

```php
namespace App\Domain\Handler\Customer;

use App\Domain\Command\Customer\CreateCustomerCommand;
use App\Repository\CustomerRepository;
use Communitales\Component\CommandBus\Attribute\AsCommandHandler;
use Communitales\Component\CommandBus\Command\CommandInterface;
use Communitales\Component\CommandBus\Handler\CommandHandlerInterface;
use Communitales\Component\CommandBus\Handler\Result\CommandResult;
use Communitales\Component\CommandBus\Handler\Result\CommandResultInterface;
use Communitales\Component\StatusBus\StatusMessage;

/**
 * @implements CommandHandlerInterface<CreateCustomerCommand> 
 */
#[AsCommandHandler(CreateCustomerCommand::class)]
final class CreateCustomerCommandHandler implements CommandHandlerInterface
{
    public function __construct(private readonly CustomerRepository $customerRepository)
    {
    }

    /** @param CreateCustomerCommand $command */
    public function handle(CommandInterface $command): CommandResultInterface
    {
        $customer = $command->customer;

        $this->customerRepository->save($customer);

        return CommandResult::success(StatusMessage::success(
            'domain_customer.result_created',
            ['name' => $customer->getName()],
        ));
    }
}
```

Dispatch the command through the interface:

```php
use App\Domain\Command\Customer\CreateCustomerCommand;
use Communitales\Component\CommandBus\CommandBusInterface;
use Communitales\Component\CommandBus\Handler\Result\CommandResultStatus;

final readonly class CustomerController
{
    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    public function create(CreateCustomerCommand $command): void
    {
        $result = $commandBus->dispatch($command);
        if ($result->getStatus() === CommandResultStatus::Success) {
            // Success
        } elseif ($result->getStatus() === CommandResultStatus::Error) {
            // Error
        } elseif ($result->getStatus() === CommandResultStatus::Failed) {
            // Missing handler or technical failure
        }
    }
}
```

Every handler must declare one `#[AsCommandHandler]` attribute. Invalid command classes and
multiple handlers for the same command cause container compilation to fail. `dispatch()` always
returns a `CommandResultInterface`: expected application errors have status `Error`; missing
handlers and caught technical exceptions have status `Failed` and are logged.

An `ExceptionLoggerInterface` service is required and must be available for autowiring. When
constructing `CommandBus` directly, pass an implementation as the second argument. This is a
breaking change for callers that previously omitted the logger. If the logger throws, the bus
falls back to PHP's `error_log`, recording the exception class names without their messages or
stack traces. The fallback is best effort: PHP does not report whether a message sent to its
default error log was actually written.

The bus does not start or roll back transactions. A handler can perform side effects before
returning an error or throwing an exception, so a `Failed` result does not imply that nothing
changed. Applications should establish transaction boundaries inside handlers or otherwise
account for the returned result when managing transactions. Make handlers idempotent or use
another deduplication strategy before retrying commands, especially when external services are
involved; database transactions cannot roll back external side effects.

## Migration

See [2.x to 3.0](MIGRATING-2-to-3.md) and [3.x to 4.0](MIGRATING-3-to-4.md).
