<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\AmazonQuicksight\Business\Facade;

use Aws\Result;
use Aws\ResultInterface;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\UserCollectionResponseTransfer;
use Generated\Shared\Transfer\UserTransfer;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightDependencyProvider;
use SprykerEco\Zed\AmazonQuicksight\Dependency\Facade\AmazonQuicksightToMessengerFacadeInterface;
use SprykerEcoTest\Zed\AmazonQuicksight\AmazonQuicksightBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerEcoTest
 * @group Zed
 * @group AmazonQuicksight
 * @group Business
 * @group Facade
 * @group DeleteQuicksightUsersByUserCollectionResponseTest
 * Add your own group annotations below this line
 */
class DeleteQuicksightUsersByUserCollectionResponseTest extends Unit
{
    /**
     * @uses \Orm\Zed\User\Persistence\Map\SpyUserTableMap::COL_STATUS_DELETED
     *
     * @var string
     */
    protected const USER_STATUS_DELETED = 'deleted';

    /**
     * @uses \Orm\Zed\User\Persistence\Map\SpyUserTableMap::COL_STATUS_ACTIVE
     *
     * @var string
     */
    protected const USER_STATUS_ACTIVE = 'active';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_QUICKSIGHT_USER_DELETE_FAILURE = 'An internal failure occurred.';

    /**
     * @var \SprykerEcoTest\Zed\AmazonQuicksight\AmazonQuicksightBusinessTester
     */
    protected AmazonQuicksightBusinessTester $tester;

    /**
     * @return void
     */
    public function testShouldDeleteQuicksightUserWhenQuicksightUserIsSuccessfullyDeletedFromQuicksight(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUser([UserTransfer::STATUS => static::USER_STATUS_DELETED]);
        $this->tester->haveQuicksightUser($userTransfer);

        $this->tester->setDependency(
            AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT,
            $this->tester->getAwsQuicksightClientMockWithSuccessfulResponse(
                $this->createDeleteUserSuccessfulResponse(),
                'deleteUser',
            ),
        );

        $userCollectionResponseTransfer = (new UserCollectionResponseTransfer())->addUser($userTransfer);

        // Act
        $userCollectionResponseTransfer = $this->tester->getFacade()
            ->deleteQuicksightUsersByUserCollectionResponse($userCollectionResponseTransfer);

        // Assert
        $this->assertCount(0, $userCollectionResponseTransfer->getErrors());
        $this->assertCount(1, $userCollectionResponseTransfer->getUsers());

        $quicksightUserEntity = $this->tester->findQuicksightUserByIdUser($userTransfer->getIdUserOrFail());
        $this->assertNull($quicksightUserEntity);
    }

    /**
     * @return void
     */
    public function testShouldReturnErrorWhenUserQuicksightClientThrowsException(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUser([UserTransfer::STATUS => static::USER_STATUS_DELETED]);
        $this->tester->haveQuicksightUser($userTransfer);

        $messengerFacadeMock = $this
            ->getMockBuilder(AmazonQuicksightToMessengerFacadeInterface::class)
            ->getMock();
        $this->tester->setDependency(AmazonQuicksightDependencyProvider::FACADE_MESSENGER, $messengerFacadeMock);
        $this->tester->setDependency(
            AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT,
            $this->tester->getAwsQuicksightClientMockWithErrorResponse(
                $this->tester->getQuicksightExceptionMock(static::ERROR_MESSAGE_QUICKSIGHT_USER_DELETE_FAILURE),
                'deleteUser',
            ),
        );

        $userCollectionResponseTransfer = (new UserCollectionResponseTransfer())->addUser($userTransfer);

        // Act
        $userCollectionResponseTransfer = $this->tester->getFacade()
            ->deleteQuicksightUsersByUserCollectionResponse($userCollectionResponseTransfer);

        // Assert
        $this->assertCount(1, $userCollectionResponseTransfer->getErrors());
        $this->assertCount(1, $userCollectionResponseTransfer->getUsers());

        /** @var \Generated\Shared\Transfer\ErrorTransfer $errorTransfer */
        $errorTransfer = $userCollectionResponseTransfer->getErrors()->getIterator()->current();
        $this->assertSame(static::ERROR_MESSAGE_QUICKSIGHT_USER_DELETE_FAILURE, $errorTransfer->getMessage());
        $this->assertSame((string)$userCollectionResponseTransfer->getUsers()->getIterator()->key(), $errorTransfer->getEntityIdentifier());

        $quicksightUserEntity = $this->tester->findQuicksightUserByIdUser($userTransfer->getIdUserOrFail());
        $this->assertNotNull($quicksightUserEntity);
    }

    /**
     * @return void
     */
    public function testShouldDoNothingWhenQuicksightUserDoesNotExistForProvidedForUser(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUser([UserTransfer::STATUS => static::USER_STATUS_DELETED]);

        $awsQuicksightClientMock = $this->tester->getAwsQuicksightClientMock();
        $awsQuicksightClientMock->expects($this->never())->method('deleteUser');
        $this->tester->setDependency(
            AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT,
            $awsQuicksightClientMock,
        );

        // Act
        $userCollectionResponseTransfer = (new UserCollectionResponseTransfer())->addUser($userTransfer);

        // Act
        $userCollectionResponseTransfer = $this->tester->getFacade()
            ->deleteQuicksightUsersByUserCollectionResponse($userCollectionResponseTransfer);

        // Assert
        $this->assertCount(0, $userCollectionResponseTransfer->getErrors());
        $this->assertCount(1, $userCollectionResponseTransfer->getUsers());
        $this->assertNull($userCollectionResponseTransfer->getUsers()->getIterator()->current()->getQuicksightUser());
    }

    /**
     * @return void
     */
    public function testShouldDoNothingWhenQuicksightUserStatusIsNotApplicableForRegisteringQuicksightUser(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUser([UserTransfer::STATUS => static::USER_STATUS_ACTIVE]);
        $this->tester->haveQuicksightUser($userTransfer);

        $awsQuicksightClientMock = $this->tester->getAwsQuicksightClientMock();
        $awsQuicksightClientMock->expects($this->never())->method('deleteUser');
        $this->tester->setDependency(
            AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT,
            $awsQuicksightClientMock,
        );

        // Act
        $userCollectionResponseTransfer = (new UserCollectionResponseTransfer())->addUser($userTransfer);

        // Act
        $userCollectionResponseTransfer = $this->tester->getFacade()
            ->deleteQuicksightUsersByUserCollectionResponse($userCollectionResponseTransfer);

        // Assert
        $this->assertCount(0, $userCollectionResponseTransfer->getErrors());
        $this->assertCount(1, $userCollectionResponseTransfer->getUsers());
        $this->assertNull($userCollectionResponseTransfer->getUsers()->getIterator()->current()->getQuicksightUser());
    }

    /**
     * @return \Aws\ResultInterface
     */
    protected function createDeleteUserSuccessfulResponse(): ResultInterface
    {
        return new Result(['RequestId' => time()]);
    }
}
