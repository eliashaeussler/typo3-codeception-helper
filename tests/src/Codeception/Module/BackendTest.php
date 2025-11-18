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

namespace EliasHaeussler\Typo3CodeceptionHelper\Tests\Codeception\Module;

use Codeception\Lib;
use Codeception\Module;
use EliasHaeussler\Typo3CodeceptionHelper as Src;
use Facebook\WebDriver;
use PHPUnit\Framework;

/**
 * BackendTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class BackendTest extends Framework\TestCase
{
    private Module\WebDriver&Framework\MockObject\MockObject $webDriver;
    private Src\Codeception\Module\Backend $subject;

    protected function setUp(): void
    {
        $moduleContainer = new Lib\ModuleContainer(new Lib\Di(), []);

        $this->webDriver = $this->createMock(Module\WebDriver::class);
        $this->subject = new Src\Codeception\Module\Backend($moduleContainer);

        $moduleContainer->mock('WebDriver', $this->webDriver);
    }

    #[Framework\Attributes\Test]
    public function loginPerformsBackendLogin(): void
    {
        $this->webDriver->expects(self::once())->method('amOnPage');
        $this->webDriver->expects(self::exactly(2))->method('waitForElementVisible');
        $this->webDriver->expects(self::exactly(2))->method('fillField');
        $this->webDriver->expects(self::once())->method('click');
        $this->webDriver->expects(self::exactly(2))->method('waitForElementNotVisible');
        $this->webDriver->expects(self::once())->method('seeCookie');

        $this->subject->login('admin', 'password');
    }

    #[Framework\Attributes\Test]
    public function loginAsThrowsExceptionIfGivenUserIsNotConfigured(): void
    {
        $this->expectExceptionObject(
            /* @phpstan-ignore method.internalClass */
            new Framework\AssertionFailedError('A user with username "foo" is not configured.'),
        );

        $this->subject->loginAs('foo');
    }

    #[Framework\Attributes\Test]
    public function loginAsPerformsBackendLoginForGivenUser(): void
    {
        $this->webDriver->expects(self::once())->method('amOnPage');
        $this->webDriver->expects(self::exactly(2))->method('waitForElementVisible');
        $this->webDriver->expects(self::exactly(2))->method('fillField');
        $this->webDriver->expects(self::once())->method('click');
        $this->webDriver->expects(self::exactly(2))->method('waitForElementNotVisible');
        $this->webDriver->expects(self::once())->method('seeCookie');

        $this->subject->loginAs('admin');
    }

    #[Framework\Attributes\Test]
    public function openModuleOpensBackendModule(): void
    {
        $this->webDriver->expects(self::once())->method('waitForElementClickable');
        $this->webDriver->expects(self::once())->method('click');
        $this->webDriver->expects(self::once())->method('switchToIFrame');

        $this->subject->openModule('foo');
    }

    #[Framework\Attributes\Test]
    public function scrollToElementInModuleThrowsExceptionIfElementDoesNotExist(): void
    {
        $this->expectExceptionObject(
            /* @phpstan-ignore method.internalClass */
            new Framework\AssertionFailedError('Element "foo" not found.'),
        );

        $this->subject->scrollToElementInModule('foo');
    }

    #[Framework\Attributes\Test]
    public function scrollToElementInModuleExecutesJavaScriptToScrollModuleToElementPosition(): void
    {
        $remoteExecuteMethod = $this->createMock(WebDriver\Remote\RemoteExecuteMethod::class);
        $remoteExecuteMethod->method('execute')->willReturn([
            'x' => 10,
            'y' => 20,
        ]);

        $this->webDriver->method('_findElements')->willReturn([
            new WebDriver\Remote\RemoteWebElement($remoteExecuteMethod, 'foo'),
        ]);

        $this->webDriver->expects(self::once())->method('_findElements');
        $this->webDriver->expects(self::exactly(2))->method('switchToFrame');
        $this->webDriver->expects(self::once())->method('executeJS')->with(<<<JS
document.scrollingElement.scrollLeft = 10;
document.scrollingElement.scrollTop = 20;
JS
        );
        $this->webDriver->expects(self::once())->method('wait');

        $this->subject->scrollToElementInModule('foo');
    }

    #[Framework\Attributes\Test]
    public function scrollToElementInModuleExecutesJavaScriptToScrollModuleToElementPositionAndRespectsOffsets(): void
    {
        $remoteExecuteMethod = $this->createMock(WebDriver\Remote\RemoteExecuteMethod::class);
        $remoteExecuteMethod->method('execute')->willReturn([
            'x' => 10,
            'y' => 20,
        ]);

        $this->webDriver->method('_findElements')->willReturn([
            new WebDriver\Remote\RemoteWebElement($remoteExecuteMethod, 'foo'),
        ]);

        $this->webDriver->expects(self::once())->method('executeJS')->with(<<<JS
document.scrollingElement.scrollLeft = 15;
document.scrollingElement.scrollTop = 28;
JS
        );
        $this->webDriver->expects(self::once())->method('wait');

        $this->subject->scrollToElementInModule('foo', 5, 8);
    }
}
