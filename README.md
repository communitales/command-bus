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
```

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

/** @implements CommandHandlerInterface<CreateCustomerCommand> */
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
use Communitales\Component\CommandBus\CommandBusInterface;

final readonly class CustomerController
{
    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    public function create(CreateCustomerCommand $command): void
    {
        $result = $this->commandBus->dispatch($command);

        // Present the CommandResultInterface as HTML, JSON, CLI output, etc.
    }
}
```

Every handler must declare one `#[AsCommandHandler]` attribute. Invalid command classes and
multiple handlers for the same command cause container compilation to fail. `dispatch()` always
returns a `CommandResultInterface`: expected application errors have status `Error`; missing
handlers and caught technical exceptions have status `Failed` and are logged when an exception
logger is configured.
