# Communitales Command Bus Component

Decouple applications with a synchronous command bus.


## Setup

```
composer require communitales/command-bus
```

Setup for Symfony in `services.yaml`:

```
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


## Usage

Example of a command:

```

namespace App\Domain\Command\Customer;

use Communitales\Component\CommandBus\Command\CommandInterface;
use App\Entity\Customer;

readonly class CreateCustomerCommand implements CommandInterface
{
    public function __construct(public Customer $customer)
    {
    }
}

```

Example of a command handler:


```

namespace App\Domain\Handler\Customer;

use App\Domain\Command\Customer\CreateCustomerCommand;
use App\Domain\Command\Customer\DeleteCustomerCommand;
use App\Domain\Command\Customer\UpdateCustomerCommand;
use App\Repository\CustomerRepository;
use Communitales\Component\CommandBus\Command\CommandInterface;
use Communitales\Component\CommandBus\Handler\CommandHandlerInterface;
use Communitales\Component\CommandBus\Handler\CommandHandlerTrait;
use Communitales\Component\CommandBus\Handler\Result\CommandHandlerResultInterface;
use Communitales\Component\CommandBus\Handler\Result\SuccessResult;
use Communitales\Component\StatusBus\StatusMessage;
use Override;
use Symfony\Component\Translation\TranslatableMessage;

use function sprintf;

class CustomerCommandHandler implements CommandHandlerInterface
{
    use CommandHandlerTrait;

    public function __construct(private readonly CustomerRepository $customerRepository) {
    }

    #[Override]
    public function canHandle(CommandInterface $command): bool
    {
        return $command instanceof CreateCustomerCommand
            || $command instanceof UpdateCustomerCommand
            || $command instanceof DeleteCustomerCommand;
    }

    private function createCustomer(CreateCustomerCommand $command): CommandHandlerResultInterface
    {
        $customer = $command->customer;

        $this->customerRepository->save($customer);

        return new SuccessResult(
            StatusMessage::createSuccessMessage(
                new TranslatableMessage(
                    'domain_customer.result_created', ['name' => $customer->getName()]
                )
            )
        );
    }

    private function updateCustomer(UpdateCustomerCommand $command): CommandHandlerResultInterface
    {
        // ...
    }

    private function deleteCustomer(DeleteCustomerCommand $command): CommandHandlerResultInterface
    {
        // ...
    }
}

```
