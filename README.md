# [<img src="logo.png" alt="quark-server" width="200">](https://github.com/fm-ph/quark-server)

[![build status][travis-image]][travis-url]
[![packagist version][packagist-image]][packagist-url]
[![php-version][php-version-image]][php-version-url]
[![composer.lock][composer-lock-image]][packagist-url]

Server part of `quark` framework handling mainly the __routing__ and __templating__. 

## Features

- __Routing__ : Methods, regex parameters, redirect, locale.
- __Templating__ : Twig (layouts, cache, customs extensions, filters, globals...).
- __Locale__ : IP address detection (Geocoder with providers chain), fallback to prefered browser locale.
- __User agent__ : Browser, engine, operating system, device, bot and old browser detection.
- __Manifest__ : Assets hash, environment.
- __Configuration__ : Supports PHP, INI, XML, JSON, and YAML file formats.
- __Others__ : Error handler, function helpers.

## Installation

Get [Composer](https://getcomposer.org/download/) and run :

```sh
composer require fm_ph/quark
```

___Note___ : You need at least __PHP 7.x__ (CLI) installed on your machine to use this package (verify it with `php -v`).

For __OS X__ users, you can easily update it on [https://php-osx.liip.ch/](https://php-osx.liip.ch/).

## Usage

### Basic

Get an `Application` singleton instance and render matched route template.

```php
<?php

define('BASE_PATH', __DIR__);

// Require Composer autoloader
require BASE_PATH . '/vendor/autoload.php';

// Get Application instance and init
$app = Quark\Application::getInstance();
$app->init();

// Render
echo $app->render();
```

### Custom configuration

Init `Application` with custom configuration.

```php
<?php

// ...

// Custom configuration
$config = [
  'locale' => [
    'code' => 'fr',
    'redirectIfOne' => true
  ]
]

// Get Application instance
$app = Quark\Application::getInstance();

// Init with custom configuration array
$app->init($config);
```

## Configuration

All configuration properties that can be passed to `init()` method on an `Application` instance.

### Old browser

| Property              | Type      | Description                                                    | Default                          |
| --------------------- | --------- | -------------------------------------------------------------- | -------------------------------- |
| old_browser           | `array`   | List of old browsers.                                          | See [Config.php](src/Config.php) |

### Locale

| Property              | Type      | Description                                                    | Default     |
| --------------------- | --------- | -------------------------------------------------------------- | ----------- |
| locale.code           | `string`  | Locale code fallback.                                          | `en`        |
| locale.country        | `string`  | Locale country fallback.                                       |             |
| locale.redirectIfOne  | `boolean` | Router redirect to include locale in URL if only one is found. | `false`     |

### Paths

| Property              | Type     | Description                                                                  | Default                  |
| --------------------- | -------- | ---------------------------------------------------------------------------- | ------------------------ |
| paths.locales         | `string` | Path to locale files. `locale` variable is replaced with the current locale. | `locales/{{locale}}.yml` |
| paths.routes          | `string` | Route file path.                                                             | `routes.yml`             |
| paths.manifest        | `string` | Manifest file path.                                                          | `manifest.json`          |

### Twig

| Property                 | Type      | Description                                                                       | Default            |
| ------------------------ | --------- | --------------------------------------------------------------------------------- | ------------------ |
| twig.layouts             | `array`   | Twig layouts name.                                                                | See below          |
| twig.layouts.default     | `string`  | Twig default layout name to be rendered.                                          | `default`          |
| twig.layouts.old_browser | `string`  | Twig old browser layout name.                                                     | `old`              |
| twig.extension           | `string`  | Twig template file extension.                                                     | `.twig`            |
| twig.cache               | `string`  | Twig cache path.                                                                  | `cache`            |
| twig.extraData           | `any`     | Twig extra data merged with template data.                                        | `[]`               |
| twig.paths.views         | `string`  | Views folder path.                                                                | `views`            |
| twig.paths.layouts       | `string`  | Layouts folder path.                                                              | `views/layouts`    |
| twig.paths.pages         | `string`  | Pages folder path.                                                                | `views/pages`      |
| twig.paths.components    | `string`  | Components folder path.                                                           | `views/components` |
| twig.extensions          | `array`   | Twig extensions (manifest and html compress extensions are activated by default). | `[]`               |
| twig.filters             | `array`   | Twig filters.                                                                     | `[]`               |
| twig.globals             | `array`   | Twig globals.                                                                     | `[]`               |
| twig.functions           | `array`   | Twig functions                                                                    | `[]`               |
| twig.tests               | `array`   | Twig tests.                                                                       | `[]`               |

## API

See [https://fm-ph.github.io/quark-server/](https://fm-ph.github.io/quark-server/)

## Testing

Install [PHPUnit](https://phpunit.de/) globally :

```sh
composer global require phpunit/phpunit
```

And run the tests with :

```sh
phpunit
```

## License

MIT [License](LICENSE.md) © [Patrick Heng](http://hengpatrick.fr/) [Fabien Motte](http://fabienmotte.com/) 

[travis-image]: https://img.shields.io/travis/fm-ph/quark-server/master.svg?style=flat-square
[travis-url]: http://travis-ci.org/fm-ph/quark-server
[packagist-image]: https://img.shields.io/packagist/v/fm_ph/quark.svg?style=flat-square
[packagist-url]: https://packagist.org/packages/fm_ph/quark
[php-version-image]: https://img.shields.io/badge/php-%3E%3D%207.0-8892BF.svg?style=flat-square
[php-version-url]: https://php.net
[composer-lock-image]: https://img.shields.io/badge/.lock-commited-e10079.svg?style=flat-square
