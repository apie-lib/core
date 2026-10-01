<img src="https://raw.githubusercontent.com/apie-lib/apie-lib-monorepo/main/docs/apie-logo.svg" width="100px" align="left" />
<h1>core</h1>






 [![Latest Stable Version](https://poser.pugx.org/apie/core/v)](https://packagist.org/packages/apie/core) [![Total Downloads](https://poser.pugx.org/apie/core/downloads)](https://packagist.org/packages/apie/core) [![Latest Unstable Version](https://poser.pugx.org/apie/core/v/unstable)](https://packagist.org/packages/apie/core) [![License](https://poser.pugx.org/apie/core/license)](https://packagist.org/packages/apie/core) [![PHP Composer](https://apie-lib.github.io/projectCoverage/coverage-core.svg)](https://apie-lib.github.io/projectCoverage/core/index.html)  

[![PHP Composer](https://github.com/apie-lib/core/actions/workflows/php.yml/badge.svg?event=push)](https://github.com/apie-lib/core/actions/workflows/php.yml)

This package is part of the [Apie](https://github.com/apie-lib) library.
The code is maintained in a monorepo, so PR's need to be sent to the [monorepo](https://github.com/apie-lib/apie-lib-monorepo/pulls)

## Documentation
The core contains the attributes, value-object traits, interfaces, contexts, and
reflection helpers shared by the Apie packages.

### Standalone usage
Install it with:
```bash
composer require apie/core
```

Use the interfaces and attributes when defining your own domain objects. For example,
an identifier can implement `Apie\Core\ValueObjects\Interfaces\IdentifierInterface`
and a constrained string can use one of the `Apie\Core\ValueObjects` traits. The core
is framework-free and is normally installed indirectly by higher-level packages.

### Symfony integration
Via `apie/apie-bundle`, `core.yaml` is loaded automatically and registers the
`Apie\Core\BoundedContext\BoundedContextHashmap`, the datalayer chain (`apie.datalayer`),
`Apie\Core\Translator\ApieTranslator`, `Apie\Core\FileStorage\ChainedFileStorage` and the
`Apie\Core\Indexing\Indexer`. Bounded contexts and file storage locations are configured
through the `apie.bounded_contexts`, `apie.scan_bounded_contexts`, `apie.datalayers` and
`apie.storage` keys in `config/packages/apie.yaml`.

### Laravel integration
Via `apie/laravel-apie`, the generated `Apie\Core\CoreServiceProvider` registers the same
bounded context, datalayer, translator, file storage and indexer services in the Laravel
container, using the equivalent Apie configuration.
