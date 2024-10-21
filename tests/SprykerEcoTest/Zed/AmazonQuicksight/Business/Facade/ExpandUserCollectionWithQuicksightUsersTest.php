<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\AmazonQuicksight\Business\Facade;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\UserCollectionTransfer;
use SprykerEcoTest\Zed\AmazonQuicksight\AmazonQuicksightBusinessTester;

class ExpandUserCollectionWithQuicksightUsersTest extends Unit
{
    /**
     * @var \SprykerEcoTest\Zed\AmazonQuicksight\AmazonQuicksightBusinessTester
     */
    protected AmazonQuicksightBusinessTester $tester;

    /**
     * @return void
     */
    public function testDoesNotExpandCollectionWhenQuicksightUserNotFound(): void
    {
        // Arrange
        $userTransfer1 = $this->tester->haveUser();
        $userTransfer2 = $this->tester->haveUser();
        $this->tester->haveQuicksightUser($userTransfer2);
        $userCollectionTransfer = (new UserCollectionTransfer())->addUser($userTransfer1);

        // Act
        $this->tester->getFacade()->expandUserCollectionWithQuicksightUsers($userCollectionTransfer);

        // Assert
        $this->assertNull($userCollectionTransfer->getUsers()->offsetGet(0)->getQuicksightUser());
    }

    /**
     * @return void
     */
    public function testExpandsAllUsersInCollectionWhenQuicksightUsersFound(): void
    {
        // Arrange
        $userTransfer1 = $this->tester->haveUser();
        $userTransfer2 = $this->tester->haveUser();
        $quicksightUserTransfer1 = $this->tester->haveQuicksightUser($userTransfer1);
        $quicksightUserTransfer2 = $this->tester->haveQuicksightUser($userTransfer2);
        $userCollectionTransfer = (new UserCollectionTransfer())
            ->addUser($userTransfer1)
            ->addUser($userTransfer2);

        // Act
        $this->tester->getFacade()->expandUserCollectionWithQuicksightUsers($userCollectionTransfer);

        // Assert
        $this->assertSame(
            $quicksightUserTransfer1->getFkUser(),
            $userCollectionTransfer->getUsers()->offsetGet(0)->getQuicksightUser()->getFkUser(),
        );
        $this->assertSame(
            $quicksightUserTransfer2->getFkUser(),
            $userCollectionTransfer->getUsers()->offsetGet(1)->getQuicksightUser()->getFkUser(),
        );
    }

    /**
     * @return void
     */
    public function testExpandsOneUserInCollectionWhenOneQuicksightUserFound(): void
    {
        // Arrange
        $userTransfer1 = $this->tester->haveUser();
        $userTransfer2 = $this->tester->haveUser();
        $quicksightUserTransfer = $this->tester->haveQuicksightUser($userTransfer2);
        $userCollectionTransfer = (new UserCollectionTransfer())
            ->addUser($userTransfer1)
            ->addUser($userTransfer2);

        // Act
        $this->tester->getFacade()->expandUserCollectionWithQuicksightUsers($userCollectionTransfer);

        // Assert
        $this->assertNull($userCollectionTransfer->getUsers()->offsetGet(0)->getQuicksightUser());
        $this->assertSame(
            $quicksightUserTransfer->getFkUser(),
            $userCollectionTransfer->getUsers()->offsetGet(1)->getQuicksightUser()->getFkUser(),
        );
    }
}
