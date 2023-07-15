<?php

declare(strict_types=1);

/*
 * This file is part of the Composer package "eliashaeussler/typo3-codeception-helper".
 *
 * Copyright (C) 2023 Elias Häußler <elias@haeussler.dev>
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

use function sprintf;

/**
 * Backend.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
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

    public function login(string $username, string $password): void
    {
        /** @var Module\WebDriver $I */
        $I = $this->getModule('WebDriver');

        $I->amOnPage('/typo3/');
        $I->waitForElementVisible(Enums\Selectors::BackendLoginUsernameField->value);
        $I->waitForElementVisible(Enums\Selectors::BackendLoginPasswordField->value);
        $I->fillField(Enums\Selectors::BackendLoginUsernameField->value, $username);
        $I->fillField(Enums\Selectors::BackendLoginPasswordField->value, $password);
        $I->click(Enums\Selectors::BackendLoginSubmitButton->value);
        $I->waitForElementNotVisible(Enums\Selectors::BackendLoginForm->value);
        $I->seeCookie('be_typo_user');
    }

    /**
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

    public function openModule(string $identifier): void
    {
        /** @var Module\WebDriver $I */
        $I = $this->getModule('WebDriver');

        $I->waitForElementClickable($identifier, 5);
        $I->click($identifier);
        $I->switchToIFrame(Enums\Selectors::BackendContentFrame->value);
    }
}
