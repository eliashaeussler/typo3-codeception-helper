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

namespace EliasHaeussler\Typo3CodeceptionHelper\Codeception\Helper;

use Codeception\Actor;
use Codeception\Module;
use Exception;
use TYPO3\TestingFramework;

/**
 * AbstractBackend.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 *
 * @template TTester of Actor
 */
abstract class AbstractBackend
{
    /**
     * @phpstan-param TTester $tester
     */
    public function __construct(
        protected readonly Actor $tester,
        protected readonly TestingFramework\Core\Acceptance\Helper\AbstractModalDialog $modalDialog,
    ) {
    }

    public function login(string $username = 'admin', string $password = 'password'): void
    {
        /** @var Module\WebDriver $I */
        $I = $this->tester;

        $I->amOnPage('/typo3/');
        $I->waitForElementVisible('#t3-username');
        $I->waitForElementVisible('#t3-password');
        $I->fillField('#t3-username', $username);
        $I->fillField('#t3-password', $password);
        $I->click('#t3-login-submit');
        $I->dontSeeElement('#typo3-login-form');

        try {
            $this->modalDialog->clickButtonInDialog('[name=ok]');
        } catch (Exception) {
            // If dialog is not present, that's fine...
        }
    }

    /**
     * @throws Exception
     */
    public function openModule(string $identifier): void
    {
        /** @var Module\WebDriver $I */
        $I = $this->tester;

        $I->waitForElementClickable($identifier, 5);
        $I->click($identifier);
        $I->switchToIFrame('#typo3-contentIframe');
    }
}
