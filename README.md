<div align="center">

# TYPO3 Codeception Helper

[![Coverage](https://img.shields.io/coverallsCoverage/github/eliashaeussler/typo3-codeception-helper?logo=coveralls)](https://coveralls.io/github/eliashaeussler/typo3-codeception-helper)
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

### `ApplicationEntrypointModifier` extension

> Source: [`Codeception\Extension\ApplicationEntrypointModifier`](src/Codeception/Extension/ApplicationEntrypointModifier.php)

A Codeception extension that aims to ease the integration effort
when collecting code coverage with [`codeception/c3`](https://github.com/Codeception/c3).
It replaces an existing entrypoint (e.g. `typo3/index.php`) with
a modified entrypoint that includes the distributed `c3.php` file.

Enable this extension in your `codeception.yml` file:

```yaml
# codeception.yml

extensions:
  enabled:
    - EliasHaeussler\Typo3CodeceptionHelper\Codeception\Extension\ApplicationEntrypointModifier:
        entrypoints:
          - web-dir: .Build/web
            main-entrypoint: index.php
            app-entrypoint: app.php
          - web-dir: .Build/web/typo3
            main-entrypoint: index.php
            app-entrypoint: app.php
```

For each entrypoint, the following config must be provided:

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

### `Backend` module

> Source: [`Codeception\Module\Backend`](src/Codeception/Module/Backend.php)

A Codeception module that allows to perform actions within TYPO3
backend. It can for example be used to log into the TYPO3 backend.

> [!NOTE]
> This module requires the [`WebDriver`](https://codeception.com/docs/modules/WebDriver)
> module to be installed and enabled.

Enable this module in your `codeception.yml` file:

```yaml
# codeception.yml

suites:
  Acceptance:
    actor: AcceptanceTester
    modules:
      enabled:
        - EliasHaeussler\Typo3CodeceptionHelper\Codeception\Module\Backend
```

#### Available methods

**`login($username, $password): void`**

Perform backend login for the given user. The user is identified
by the given username and is authenticated by the given password.

Example:

```php
$I->login('admin', 'password');
```

**`loginAs($username): void`**

Perform backend login for the given user. The user is identified
by the given username which must be configured in the codeception
module config (see [Configure backend users](#configure-backend-users)).

Example:

```php
$I->loginAs('admin');
```

**`openModule($identifier): void`**

Open a backend module by clicking on the module link. The module
link is identified by a given node identifier. Note that the
identifier differs between TYPO3 versions (see example below).

Example:

```php
// TYPO3 11
$I->openModule('#web_list');

// TYPO3 12
$I->openModule('[data-modulemenu-identifier="web_list"]');
```

**`scrollToElementInModule($identifier, $offsetX = 0, $offsetY = 0): void`**

Scroll to a given element, identified by the given node identifier,
inside the current backend module. This circumvents issues with
Codeception's native `WebDriver::scrollTo()` method which does not
support scrolling inside frames.

Example:

```php
$I->openModule('[data-modulemenu-identifier="web_list"]');
$I->scrollToElementInModule('tr[data-table="tt_content"]');
```

#### Configure backend users

> [!NOTE]
> Backend users are not automatically created by this module.
> You need to take care of that by your own, e.g. by
> [importing static database fixtures](https://codeception.com/docs/modules/Db#SQL-data-dump)
> before tests are executed.

In order to use the `loginAs()` method, existing backend users
must be configured in the module config section:

```diff
 suites:
   Acceptance:
     actor: AcceptanceTester
     modules:
       enabled:
-        - EliasHaeussler\Typo3CodeceptionHelper\Codeception\Module\Backend
+        - EliasHaeussler\Typo3CodeceptionHelper\Codeception\Module\Backend:
+            userCredentials:
+              admin: password
+              editor: password
```

## 🧑‍💻 Contributing

Please have a look at [`CONTRIBUTING.md`](CONTRIBUTING.md).

## ⭐ License

This project is licensed under [GNU General Public License 2.0 (or later)](LICENSE).
