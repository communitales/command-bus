 # Migrating from 2.x to 3.0

Version 3 requires PHP 8.4.1+, `communitales/log` 4.x and `communitales/status-bus` 3.x. Update direct uses of those dependencies as well.

The examples below show the changes needed for migration. The [README](README.md#usage)
contains a complete command and handler example for the current version; when stopping at 3.x,
use the 3.x dependency and logger requirements described here.

## Symfony configuration

**Before — `config/services.yaml`:**

```yaml
services:
    _instanceof:
        Communitales\Component\CommandBus\CommandBusAwareInterface:
            calls:
                - [setCommandBus, ['@Communitales\Component\CommandBus\CommandBus']]
        Communitales\Component\CommandBus\Handler\CommandHandlerInterface:
            tags: ['communitales.command_handler']

    Communitales\Component\CommandBus\CommandBus:
        arguments:
            - !tagged_iterator communitales.command_handler
```

**After — `config/bundles.php`:**

```php
return [
    // Other bundles ...
    Communitales\Component\CommandBus\CommandBusBundle::class => ['all' => true],
];
```

**After — `config/services.yaml`:**

```yaml
services:
    _defaults:
        autowire: true
        autoconfigure: true

    App\:
        resource: '../src/'
```

Remove the old manual bus definition, tags and setter configuration. Merge the examples into your existing configuration rather than duplicating its keys.

## Inject the bus

`CommandBusAwareInterface` and `CommandBusAwareTrait` have been removed.

**Before:**

```php
use Communitales\Component\CommandBus\CommandBusAwareInterface;
use Communitales\Component\CommandBus\CommandBusAwareTrait;

final class CustomerService implements CommandBusAwareInterface
{
    use CommandBusAwareTrait;
}
```

**After:**

```php
use Communitales\Component\CommandBus\CommandBusInterface;

final class CustomerService
{
    public function __construct(private readonly CommandBusInterface $commandBus) {}
}
```

## Register one handler per command

Remove `CommandHandlerTrait` and `canHandle()`. Split handlers for multiple commands into separate services. The bus now resolves the exact command class; duplicate mappings fail container compilation.

**Before:**

```php
use App\Domain\Command\Customer\CreateCustomerCommand;
use Communitales\Component\CommandBus\Command\CommandInterface;
use Communitales\Component\CommandBus\Handler\CommandHandlerInterface;
use Communitales\Component\CommandBus\Handler\CommandHandlerTrait;
use Communitales\Component\CommandBus\Handler\Result\CommandHandlerResultInterface;
use Communitales\Component\CommandBus\Handler\Result\SuccessResult;

final class CreateCustomerCommandHandler implements CommandHandlerInterface
{
    use CommandHandlerTrait;

    public function canHandle(CommandInterface $command): bool
    {
        return $command instanceof CreateCustomerCommand;
    }

    private function createCustomer(CreateCustomerCommand $command): CommandHandlerResultInterface
    {
        return new SuccessResult();
    }
}
```

**After:**

```php
use App\Domain\Command\Customer\CreateCustomerCommand;
use Communitales\Component\CommandBus\Attribute\AsCommandHandler;
use Communitales\Component\CommandBus\Command\CommandInterface;
use Communitales\Component\CommandBus\Handler\CommandHandlerInterface;
use Communitales\Component\CommandBus\Handler\Result\CommandResult;
use Communitales\Component\CommandBus\Handler\Result\CommandResultInterface;

/**
 * @implements CommandHandlerInterface<CreateCustomerCommand> 
 */
#[AsCommandHandler(CreateCustomerCommand::class)]
final class CreateCustomerCommandHandler implements CommandHandlerInterface
{
    /** @param CreateCustomerCommand $command */
    public function handle(CommandInterface $command): CommandResultInterface
    {
        return CommandResult::success();
    }
}
```

## Handle results and errors

**Before:**

```php
use Communitales\Component\CommandBus\CanNotDispatchCommandException;
use Communitales\Component\CommandBus\Handler\Result\SuccessResult;

try {
    $result = $commandBus->dispatch($command);
    if ($result instanceof SuccessResult) {
        // Success
    }
} catch (CanNotDispatchCommandException $exception) {
    // No handler
}
```

**After:**

```php
use Communitales\Component\CommandBus\Handler\Result\CommandResultStatus;

$result = $commandBus->dispatch($command);
if ($result->getStatus() === CommandResultStatus::Success) {
    // Success
} elseif ($result->getStatus() === CommandResultStatus::Error) {
    // Error
} elseif ($result->getStatus() === CommandResultStatus::Failed) {
    // Missing handler or technical failure
}
```

Replace `CommandHandlerResultInterface` with `CommandResultInterface`. Return `CommandResult::success()`, `::error()` or `::failed()` instead of the old result classes. Database and fatal errors both have status `Failed`; their status messages can differ. `CommandResultException` now carries a `CommandResultInterface`. Review custom result implementations and status-message creation against the new interfaces and status-bus API.

For direct bus construction, replace the iterable with a `ServiceCollectionInterface` indexed by command class. `addCommandHandler()` was removed; pass the optional logger and status bus to the constructor instead of using the old aware setters.
