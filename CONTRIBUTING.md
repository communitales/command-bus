# Contributing

Bug reports and pull requests are welcome. For security vulnerabilities, follow the [security policy](SECURITY.md)
instead of opening a public issue.

For a bug report, include the package and PHP versions, steps to reproduce the problem, and the expected and actual
behavior. For substantial changes, consider opening an issue first to discuss the approach.

To work on the project, install PHP and Composer, then run:

```sh
composer install
composer test
composer phpstan
```

Please add or update tests for behavior changes and describe the change and its validation in the pull request. CI also
checks formatting, Rector, compatibility, and coverage; see the [workflow](.github/workflows/reusable-codequality.yml)
for details. Contributions are made under the project's [MIT license](LICENSE).
