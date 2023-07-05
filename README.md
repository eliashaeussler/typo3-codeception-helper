<div align="center">

# TYPO3 Codeception Helper

[![Coverage](https://img.shields.io/codecov/c/github/eliashaeussler/typo3-codeception-helper?logo=codecov&token=4BM8pdRAmL)](https://codecov.io/gh/eliashaeussler/typo3-codeception-helper)
[![Maintainability](https://img.shields.io/codeclimate/maintainability/eliashaeussler/typo3-codeception-helper?logo=codeclimate)](https://codeclimate.com/github/eliashaeussler/typo3-codeception-helper/maintainability)
[![CGL](https://img.shields.io/github/actions/workflow/status/eliashaeussler/typo3-codeception-helper/cgl.yaml?label=cgl&logo=github)](https://github.com/eliashaeussler/typo3-codeception-helper/actions/workflows/cgl.yaml)
[![Tests](https://img.shields.io/github/actions/workflow/status/eliashaeussler/typo3-codeception-helper/tests.yaml?label=tests&logo=github)](https://github.com/eliashaeussler/typo3-codeception-helper/actions/workflows/tests.yaml)
[![Supported PHP Versions](https://img.shields.io/packagist/dependency-v/eliashaeussler/typo3-codeception-helper/php?logo=php)](https://packagist.org/packages/eliashaeussler/typo3-codeception-helper)

</div>

This library provides some helper functions for [Codeception](https://codeception.com/)
tests within TYPO3 extensions. In addition, an application entrypoint
modifier extension for coverage collection within acceptance tests
is distributed.

## 🔥 Installation

[![Packagist](https://img.shields.io/packagist/v/eliashaeussler/typo3-codeception-helper?label=version&logo=packagist)](https://packagist.org/packages/eliashaeussler/typo3-codeception-helper)
[![Packagist Downloads](https://img.shields.io/packagist/dt/eliashaeussler/typo3-codeception-helper?color=brightgreen)](https://packagist.org/packages/eliashaeussler/typo3-codeception-helper)

```bash
composer require --dev eliashaeussler/typo3-codeception-helper
```

## ⚡ Usage

### Application entrypoint modifier extension

> Source: [`Codeception\Extension\ApplicationEntrypointModifier`](src/Codeception/Extension/ApplicationEntrypointModifier.php)

A Codeception extension that aims to ease the integration effort
when collecting code coverage with [`codeception/c3`](https://github.com/Codeception/c3).
It replaces an existing entrypoint (e.g. `typo3/index.php`) with
a modified entrypoint that includes the distributed `c3.php` file.

Enable this extension in your `codeception.yml`:

```yaml
# codeception.yml

extensions:
  enabled:
    - EliasHaeussler\Typo3CodeceptionHelper\Codeception\Extension\ApplicationEntrypointModifier:
        web-dir: .Build/web
        main-entrypoint: index.php
        app-entrypoint: app.php
```

The following config must be provided:

| Config name       | Description                                                                    | Default value |
|-------------------|--------------------------------------------------------------------------------|---------------|
| `web-dir`         | Relative path from project root to directory that contains the main entrypoint | –             |
| `main-entrypoint` | Name of the entrypoint to replace (the file being accessed by the web server)  | `index.php`   |
| `app-entrypoint`  | Name of the original relocated entrypoint (the renamed main entrypoint)        | `app.php`     |

#### Example

Given the following directory structure:

```
.Build
└── web
    └── index.php   # main entrypoint provided by framework/application
```

Once the extension is enabled and properly configured, the following
directory structure exists after the test suite is started:

```
.Build
└── web
    ├── app.php     # contains the original contents from index.php
    └── index.php   # generated entrypoint that includes c3.php and app.php
```

## 🧑‍💻 Contributing

Please have a look at [`CONTRIBUTING.md`](CONTRIBUTING.md).

## ⭐ License

This project is licensed under [GNU General Public License 2.0 (or later)](LICENSE).
