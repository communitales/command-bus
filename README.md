# Communitales Command Bus Component

Decouple applications with a command bus.

## Setup

```
composer require communitales/command-bus
```

Setup for Symfony in `services.yaml`:

```
services:

    _defaults:
        bind:
            iterable $commandHandlers: !tagged_iterator communitales.command_handler

    _instanceof:
        Communitales\Component\CommandBus\CommandBusAwareInterface:
            calls:
                - [setCommandBus, ['@App\Component\Cqrs\CommandBus']]

        Communitales\Component\CommandBus\Handler\CommandHandlerInterface:
            tags: ['communitales.command_handler']

```

## Usage

Example of a command:

```

namespace App\Domain\Command\Customer;

use Communitales\Component\CommandBus\Command\CommandInterface;
use App\Entity\Customer;

/**
 * Class CreateCustomerCommand
 */
class CreateCustomerCommand implements CommandInterface
{

    /**
     * @var Customer
     */
    private Customer $customer;

    /**
     * @param Customer $customer
     */
    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
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
use Doctrine\ORM\ORMException;
use function sprintf;

/**
 * Class CreateCustomerCommandHandler
 */
class CustomerCommandHandler implements CommandHandlerInterface
{

    use CommandHandlerTrait;

    /**
     * @var CustomerRepository
     */
    private CustomerRepository $customerRepository;

    /**
     * @param CustomerRepository      $customerRepository
     */
    public function __construct(CustomerRepository $customerRepository) {
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param CommandInterface $command
     *
     * @return bool
     */
    public function canHandle(CommandInterface $command): bool
    {
        return $command instanceof CreateCustomerCommand
            || $command instanceof UpdateCustomerCommand
            || $command instanceof DeleteCustomerCommand;
    }

    /**
     * @param CommandInterface $command
     *
     * @return CommandHandlerResultInterface
     * @throws ORMException
     */
    public function handle(CommandInterface $command): CommandHandlerResultInterface
    {
        if ($command instanceof CreateCustomerCommand) {
            return $this->createCustomer($command);
        }
        if ($command instanceof UpdateCustomerCommand) {
            return $this->updateCustomer($command);
        }
        if ($command instanceof DeleteCustomerCommand) {
            return $this->deleteCustomer($command);
        }

        return $this->canNotHandle($command);
    }

    /**
     * @param CreateCustomerCommand $command
     *
     * @return CommandHandlerResultInterface
     * @throws ORMException
     */
    private function createCustomer(CreateCustomerCommand $command): CommandHandlerResultInterface
    {
        $customer = $command->getCustomer();

        $this->customerRepository->save($customer);

        return new SuccessResult(
            StatusMessage::createSuccessMessage('domain_customer.result_created', ['name' => $customer->getName()])
        );
    }

    /**
     * @param UpdateCustomerCommand $command
     *
     * @return CommandHandlerResultInterface
     * @throws ORMException
     */
    private function updateCustomer(UpdateCustomerCommand $command): CommandHandlerResultInterface
    {
        // ...
    }

    /**
     * @param DeleteCustomerCommand $command
     *
     * @return CommandHandlerResultInterface
     * @throws ORMException
     */
    private function deleteCustomer(DeleteCustomerCommand $command): CommandHandlerResultInterface
    {
        // ...
    }
}

```
