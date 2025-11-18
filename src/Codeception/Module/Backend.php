<?php

declare(strict_types=1);

/*
 * This file is part of the Composer package "eliashaeussler/typo3-codeception-helper".
 *
 * Copyright (C) 2023-2025 Elias Häußler <elias@haeussler.dev>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace EliasHaeussler\Typo3CodeceptionHelper\Codeception\Module;

use Codeception\Module;
use EliasHaeussler\Typo3CodeceptionHelper\Enums;
use Facebook\WebDriver;

use function reset;
use function sprintf;

/**
 * Backend.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 *
 * @method never fail(string $message = '')
 */
final class Backend extends Module
{
    /**
     * @var array{userCredentials: array<string, string>}
     */
    protected array $config = [
        'userCredentials' => [
            'admin' => 'password',
        ],
    ];

    /**
     * Perform backend login for the given user. The user is identified
     * by the given username and is authenticated by the given password.
     *
     * Example
     * =======
     *
     * $I->login('admin', 'password');
     */
    public function login(string $username, string $password): void
    {
        $I = $this->getWebDriver();

        $I->amOnPage('/typo3/');
        $I->waitForElementVisible(Enums\Selectors::BackendLoginUsernameField->value);
        $I->waitForElementVisible(Enums\Selectors::BackendLoginPasswordField->value);
        $I->fillField(Enums\Selectors::BackendLoginUsernameField->value, $username);
        $I->fillField(Enums\Selectors::BackendLoginPasswordField->value, $password);
        $I->click(Enums\Selectors::BackendLoginSubmitButton->value);
        $I->waitForElementNotVisible(Enums\Selectors::BackendLoginForm->value);
        $I->waitForElementNotVisible(Enums\Selectors::BackendProgressBar->value);
        $I->seeCookie('be_typo_user');
    }

    /**
     * Perform backend login for the given user. The user is identified
     * by the given username which must be configured in the codeception
     * module config.
     *
     * Example
     * =======
     *
     * $I->loginAs('admin');
     *
     * @param non-empty-string $username
     */
    public function loginAs(string $username): void
    {
        if (!is_string($this->config['userCredentials'][$username] ?? null)) {
            $this->fail(
                sprintf('A user with username "%s" is not configured.', $username),
            );
        }

        $this->login($username, $this->config['userCredentials'][$username]);
    }

    /**
     * Open a backend module by clicking on the module link. The module
     * link is identified by a given node identifier. Note that the
     * identifier differs between TYPO3 versions (see example below).
     *
     * Example
     * =======
     *
     * TYPO3 11
     * --------
     * $I->openModule('#web_list');
     *
     * TYPO3 12
     * --------
     * $I->openModule('[data-modulemenu-identifier="web_list"]');
     */
    public function openModule(string $identifier): void
    {
        $I = $this->getWebDriver();

        $I->waitForElementClickable($identifier, 5);
        $I->click($identifier);
        $I->switchToIFrame(Enums\Selectors::BackendContentFrame->value);
    }

    public function scrollToElementInModule(string $identifier, int $offsetX = 0, int $offsetY = 0): void
    {
        $I = $this->getWebDriver();

        /** @var WebDriver\Remote\RemoteWebElement[] $elements */
        $elements = $I->_findElements($identifier);
        $element = reset($elements);

        if (false === $element) {
            $this->fail(
                sprintf('Element "%s" not found.', $identifier),
            );
        }

        // Make sure we're in content frame
        $I->switchToFrame();
        $I->switchToFrame(Enums\Selectors::BackendContentFrame->value);

        $x = $element->getLocation()->getX() + $offsetX;
        $y = $element->getLocation()->getY() + $offsetY;

        $I->executeJS(<<<JS
document.scrollingElement.scrollLeft = {$x};
document.scrollingElement.scrollTop = {$y};
JS
        );

        // Wait for scrolling to finish
        $I->wait(1);
    }

    private function getWebDriver(): Module\WebDriver
    {
        if (!$this->hasModule('WebDriver')) {
            $this->fail('WebDriver module is not enabled.');
        }

        /** @var Module\WebDriver $webDriver */
        $webDriver = $this->getModule('WebDriver');

        return $webDriver;
    }
}
