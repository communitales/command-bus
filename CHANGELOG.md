# CHANGELOG

## 4.0.0

### Breaking changes

* Update `communitales/log` from 4.0.0 to 5.0.0.
* Require an `ExceptionLoggerInterface` when constructing the command bus.

### Changed

* Fall back to PHP's `error_log` if the exception logger fails.
* Document transaction boundaries and retry considerations.

## 3.0.0

### Breaking changes

* Add `#[AsCommandHandler]` and automatic Symfony handler discovery.
* Resolve handlers lazily from a service collection indexed by command class.
* Reject invalid and duplicate command-handler mappings during container compilation.
* Replace runtime `canHandle()` routing with one handler per command.
* Make `CommandHandlerInterface` generic for PHPStan and remove the convention-based handler trait.
* Replace the result class hierarchy with `CommandResult`, `CommandResultInterface` and
  `CommandResultStatus` (`Success`, `Error`, `Failed`).
* Guarantee that `CommandBusInterface::dispatch()` returns a result for missing handlers and all
  caught technical errors instead of throwing an exception.
* Update logging and status publishing to the current Communitales component contracts.
* Remove `CommandBusAwareInterface` and `CommandBusAwareTrait`

## 2.2.0

* Change: The Method `getStatusMessage()` is back in `CommandHandlerResultInterface`. This provides a better error
  handling.

## 2.1.0

* Change: Exceptions in CommandBus Constructor are not caught anymore.

## 2.0.0

### Breaking changes

* Change: Upgrade to PHP 8.3
* Change: Update to communitales/status-bus 2.0
* Change: `CommandHandlerTrait` now provides a `handle` method which calls command specific handle methods via a naming
  convention

## 1.2.0

* Change: Upgrade to PHP 8.2 and improve code quality

## 1.1.3

* Changed: Updated composer dependencies

## 1.1.2

* Fixed: Update Interface according to implementation

## 1.1.1

* Changed: DisplayStatusMessage as Option for dispatch

## 1.1.0

* Fixed: Also catch Doctrine\DBAL\Exception as DatabaseErrorResult
* Changed: PHP 8.0 is now minimum PHP Version

## 1.0.2

* Fixed: Don't catch Exception in Constructor of CommandBus as Logger is not set yet

## 1.0.1

* Changed: Upgrade StatusBus to 1.0.2

## 1.0.0

* Initial version
