# Migrating from 3.x to 4.0

For the complete setup and usage example, see the [README](README.md).

## Update dependencies

**Before — `composer.json`:**

```json
{
    "require": {
        "communitales/command-bus": "^3.0",
        "communitales/log": "^4.0"
    }
}
```

**After:**

```json
{
    "require": {
        "communitales/command-bus": "^4.0",
        "communitales/log": "^5.0"
    }
}
```

Only add the explicit `communitales/log` constraint if your application depends on it directly. Update those direct uses for version 5. The minimum `symfony/service-contracts` version is now 3.8.

## Configure the exception logger

The logger is now required. If an `ExceptionLoggerInterface` service is already registered for autowiring, no bus configuration change is needed. Otherwise, for example, with a PSR logger registered as `Psr\Log\LoggerInterface`:

**Before — no logger required:**

```yaml
# config/services.yaml
services:
    _defaults:
        autowire: true
        autoconfigure: true
```

**After — register the logger and its interface alias:**

```yaml
# config/services.yaml
services:
    _defaults:
        autowire: true
        autoconfigure: true

    Communitales\Component\Log\ExceptionLogger: ~
    Communitales\Component\Log\ExceptionLoggerInterface: '@Communitales\Component\Log\ExceptionLogger'
```

Merge these definitions into your existing configuration. `ExceptionLogger` needs a `Psr\Log\LoggerInterface` service; configure one if your application does not already provide it. A missing exception logger prevents container compilation.

## Direct construction

**Before:**

```php
use Communitales\Component\CommandBus\CommandBus;

$commandBus = new CommandBus($handlers);
```

**After:**

```php
use Communitales\Component\CommandBus\CommandBus;
use Communitales\Component\Log\ExceptionLogger;

$exceptionLogger = new ExceptionLogger($psrLogger);
$commandBus = new CommandBus($handlers, $exceptionLogger);
```

Here `$handlers` is the existing service collection indexed by command class and `$psrLogger` implements `Psr\Log\LoggerInterface`. The optional status bus remains the third argument.

If the exception logger throws, the bus attempts to write the exception class names to PHP's `error_log`. This fallback is best effort and excludes exception messages and stack traces.
