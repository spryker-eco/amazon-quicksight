<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\AmazonQuicksight\Business\Facade;

use Aws\Result;
use Aws\ResultInterface;
use Codeception\Test\Unit;
use Generated\Shared\DataBuilder\QuicksightUserBuilder;
use Generated\Shared\Transfer\QuicksightUserTransfer;
use Generated\Shared\Transfer\UserTransfer;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightDependencyProvider;
use SprykerEcoTest\Zed\AmazonQuicksight\AmazonQuicksightBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerEcoTest
 * @group Zed
 * @group AmazonQuicksight
 * @group Business
 * @group Facade
 * @group DeleteRegisteredQuicksightUsersNotMatchedWithExistingUsersTest
 * Add your own group annotations below this line
 */
class DeleteRegisteredQuicksightUsersNotMatchedWithExistingUsersTest extends Unit
{
    /**
     * @uses \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClient::RESPONSE_KEY_USER_LIST
     *
     * @var string
     */
    protected const RESPONSE_KEY_USER_LIST = 'UserList';

    /**
     * @uses \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig::QUICKSIGHT_USER_ROLE_READER
     *
     * @var string
     */
    protected const QUICKSIGHT_USER_ROLE_READER = 'READER';

    /**
     * @var string
     */
    protected const QUICKSIGHT_USER_ROLE_ADMIN = 'ADMIN';

    /**
     * @uses \Orm\Zed\User\Persistence\Map\SpyUserTableMap::COL_STATUS_DELETED
     *
     * @var string
     */
    protected const USER_STATUS_DELETED = 'deleted';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_QUICKSIGHT_API_FAILURE = 'An internal failure occurred.';

    /**
     * @uses \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClient::ERROR_MESSAGE_USERS_LIST_RETRIEVE_FAILED
     *
     * @var string
     */
    protected const ERROR_MESSAGE_USERS_LIST_RETRIEVE_FAILED = 'Failed to retrieve users list.';

    /**
     * @var \SprykerEcoTest\Zed\AmazonQuicksight\AmazonQuicksightBusinessTester
     */
    protected AmazonQuicksightBusinessTester $tester;

    /**
     * @return void
     */
    public function testShouldDeleteRegisteredQuicksightUsersNotMatchedWithPersistedUsers(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUser();
        $matchingQuicksightUserTransfer = (new QuicksightUserBuilder([
            QuicksightUserTransfer::USER_NAME => $userTransfer->getUsernameOrFail(),
            QuicksightUserTransfer::ROLE => static::QUICKSIGHT_USER_ROLE_READER,
        ]))->build();
        $notMatchingQuicksightUserTransfer = (new QuicksightUserBuilder([
            QuicksightUserTransfer::USER_NAME => 'non-existing-user@spryker.com',
            QuicksightUserTransfer::ROLE => static::QUICKSIGHT_USER_ROLE_READER,
        ]))->build();

        $quicksightClientMock = $this->tester->getAwsQuicksightClientMockWithSuccessfulResponse(
            $this->createListUsersSuccessfulResponse([
                $matchingQuicksightUserTransfer,
                $notMatchingQuicksightUserTransfer,
            ]),
            'listUsers',
        );
        $quicksightClientMock->expects($this->once())
            ->method('deleteUserByPrincipalId')
            ->willReturn(new Result());
        $this->tester->setDependency(AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT, $quicksightClientMock);

        // Act
        $quicksightUserCollectionResponseTransfer = $this->tester->getFacade()->deleteRegisteredQuicksightUsersNotMatchedWithExistingUsers();

        // Assert
        $this->assertCount(0, $quicksightUserCollectionResponseTransfer->getErrors());
        $this->assertCount(1, $quicksightUserCollectionResponseTransfer->getQuicksightUsers());

        $this->assertSame(
            $notMatchingQuicksightUserTransfer->getUserNameOrFail(),
            $quicksightUserCollectionResponseTransfer->getQuicksightUsers()->getIterator()->current()->getUserNameOrFail(),
        );
    }

    /**
     * @return void
     */
    public function testShouldDeleteRegisteredQuicksightUserWhenMatchedUserHasDeletedStatus(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUser([UserTransfer::STATUS => static::USER_STATUS_DELETED]);
        $quicksightUserTransfer = (new QuicksightUserBuilder([
            QuicksightUserTransfer::USER_NAME => $userTransfer->getUsernameOrFail(),
            QuicksightUserTransfer::ROLE => static::QUICKSIGHT_USER_ROLE_READER,
        ]))->build();

        $quicksightClientMock = $this->tester->getAwsQuicksightClientMockWithSuccessfulResponse(
            $this->createListUsersSuccessfulResponse([$quicksightUserTransfer]),
            'listUsers',
        );
        $quicksightClientMock->expects($this->once())
            ->method('deleteUserByPrincipalId')
            ->willReturn(new Result());
        $this->tester->setDependency(AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT, $quicksightClientMock);

        // Act
        $quicksightUserCollectionResponseTransfer = $this->tester->getFacade()->deleteRegisteredQuicksightUsersNotMatchedWithExistingUsers();

        // Assert
        $this->assertCount(0, $quicksightUserCollectionResponseTransfer->getErrors());
        $this->assertCount(1, $quicksightUserCollectionResponseTransfer->getQuicksightUsers());
        $this->assertSame(
            $quicksightUserTransfer->getUserNameOrFail(),
            $quicksightUserCollectionResponseTransfer->getQuicksightUsers()->getIterator()->current()->getUserNameOrFail(),
        );
    }

    /**
     * @return void
     */
    public function testShouldDoNothingWhenEmptyUserListIsReturned(): void
    {
        // Arrange
        $this->tester->haveUser();

        $quicksightClientMock = $this->tester->getAwsQuicksightClientMockWithSuccessfulResponse(
            $this->createListUsersSuccessfulResponse([]),
            'listUsers',
        );
        $quicksightClientMock->expects($this->never())
            ->method('deleteUserByPrincipalId')
            ->willReturn(new Result());
        $this->tester->setDependency(AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT, $quicksightClientMock);

        // Act
        $quicksightUserCollectionResponseTransfer = $this->tester->getFacade()->deleteRegisteredQuicksightUsersNotMatchedWithExistingUsers();

        // Assert
        $this->assertCount(0, $quicksightUserCollectionResponseTransfer->getErrors());
        $this->assertCount(0, $quicksightUserCollectionResponseTransfer->getQuicksightUsers());
    }

    /**
     * @return void
     */
    public function testShouldDoNothingWhenMatchedUserHavePersistedQuicksightUser(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUser();
        $quicksightUserTransfer = $this->tester->haveQuicksightUser($userTransfer, [
            QuicksightUserTransfer::USER_NAME => $userTransfer->getUsernameOrFail(),
            QuicksightUserTransfer::ROLE => static::QUICKSIGHT_USER_ROLE_READER,
        ]);

        $quicksightClientMock = $this->tester->getAwsQuicksightClientMockWithSuccessfulResponse(
            $this->createListUsersSuccessfulResponse([$quicksightUserTransfer]),
            'listUsers',
        );
        $quicksightClientMock->expects($this->never())
            ->method('deleteUserByPrincipalId')
            ->willReturn(new Result());
        $this->tester->setDependency(AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT, $quicksightClientMock);

        // Act
        $quicksightUserCollectionResponseTransfer = $this->tester->getFacade()->deleteRegisteredQuicksightUsersNotMatchedWithExistingUsers();

        // Assert
        $this->assertCount(0, $quicksightUserCollectionResponseTransfer->getErrors());
        $this->assertCount(0, $quicksightUserCollectionResponseTransfer->getQuicksightUsers());
    }

    /**
     * @return void
     */
    public function testShouldReturnEmptyCollectionWhenRegisteredQuicksightUserHasUnsupportedRole(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUser();
        $quicksightUserTransfer = (new QuicksightUserBuilder([
            QuicksightUserTransfer::USER_NAME => 'non-existing-user@spryker.com',
            QuicksightUserTransfer::ROLE => static::QUICKSIGHT_USER_ROLE_ADMIN,
        ]))->build();

        $quicksightClientMock = $this->tester->getAwsQuicksightClientMockWithSuccessfulResponse(
            $this->createListUsersSuccessfulResponse([$quicksightUserTransfer]),
            'listUsers',
        );
        $quicksightClientMock->expects($this->never())
            ->method('deleteUserByPrincipalId')
            ->willReturn(new Result());
        $this->tester->setDependency(AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT, $quicksightClientMock);

        // Act
        $quicksightUserCollectionResponseTransfer = $this->tester->getFacade()->deleteRegisteredQuicksightUsersNotMatchedWithExistingUsers();

        // Assert
        $this->assertCount(0, $quicksightUserCollectionResponseTransfer->getErrors());
        $this->assertCount(0, $quicksightUserCollectionResponseTransfer->getQuicksightUsers());
    }

    /**
     * @return void
     */
    public function testShouldReturnErrorWhenQuicksightClientMethodListUsersThrowsException(): void
    {
        // Arrange
        $this->tester->haveUser();
        $this->tester->setDependency(
            AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT,
            $this->tester->getAwsQuicksightClientMockWithErrorResponse(
                $this->tester->getQuicksightExceptionMock(static::ERROR_MESSAGE_QUICKSIGHT_API_FAILURE),
                'listUsers',
            ),
        );

        // Act
        $quicksightUserCollectionResponseTransfer = $this->tester->getFacade()->deleteRegisteredQuicksightUsersNotMatchedWithExistingUsers();

        // Assert
        $this->assertCount(1, $quicksightUserCollectionResponseTransfer->getErrors());
        $this->assertCount(0, $quicksightUserCollectionResponseTransfer->getQuicksightUsers());

        $this->assertSame(
            static::ERROR_MESSAGE_QUICKSIGHT_API_FAILURE,
            $quicksightUserCollectionResponseTransfer->getErrors()->getIterator()->current()->getMessage(),
        );
    }

    /**
     * @return void
     */
    public function testShouldReturnErrorWhenQuicksightClientMethodDeleteUserByPrincipalIdThrowsException(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUser();
        $quicksightUserTransfer = (new QuicksightUserBuilder([
            QuicksightUserTransfer::USER_NAME => 'non-existing-user@spryker.com',
            QuicksightUserTransfer::ROLE => static::QUICKSIGHT_USER_ROLE_READER,
        ]))->build();

        $quicksightClientMock = $this->tester->getAwsQuicksightClientMockWithSuccessfulResponse(
            $this->createListUsersSuccessfulResponse([$quicksightUserTransfer]),
            'listUsers',
        );
        $quicksightClientMock->method('deleteUserByPrincipalId')
            ->willThrowException($this->tester->getQuicksightExceptionMock(static::ERROR_MESSAGE_QUICKSIGHT_API_FAILURE));
        $this->tester->setDependency(AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT, $quicksightClientMock);

        // Act
        $quicksightUserCollectionResponseTransfer = $this->tester->getFacade()->deleteRegisteredQuicksightUsersNotMatchedWithExistingUsers();

        // Assert
        $this->assertCount(1, $quicksightUserCollectionResponseTransfer->getErrors());
        $this->assertCount(0, $quicksightUserCollectionResponseTransfer->getQuicksightUsers());

        $this->assertSame(
            static::ERROR_MESSAGE_QUICKSIGHT_API_FAILURE,
            $quicksightUserCollectionResponseTransfer->getErrors()->getIterator()->current()->getMessage(),
        );
    }

    /**
     * @return void
     */
    public function testShouldReturnErrorWhenQuicksightListUsersResponseDoesNotContainUserListKey(): void
    {
        // Arrange
        $this->tester->haveUser();
        $this->tester->setDependency(
            AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT,
            $this->tester->getAwsQuicksightClientMockWithSuccessfulResponse(
                new Result([]),
                'listUsers',
            ),
        );

        // Act
        $quicksightUserCollectionResponseTransfer = $this->tester->getFacade()->deleteRegisteredQuicksightUsersNotMatchedWithExistingUsers();

        // Assert
        $this->assertCount(1, $quicksightUserCollectionResponseTransfer->getErrors());
        $this->assertCount(0, $quicksightUserCollectionResponseTransfer->getQuicksightUsers());

        $this->assertSame(
            static::ERROR_MESSAGE_USERS_LIST_RETRIEVE_FAILED,
            $quicksightUserCollectionResponseTransfer->getErrors()->getIterator()->current()->getMessage(),
        );
    }

    /**
     * @param list<\Generated\Shared\Transfer\QuicksightUserTransfer> $quicksightUserTransfers
     *
     * @return \Aws\ResultInterface
     */
    protected function createListUsersSuccessfulResponse(array $quicksightUserTransfers): ResultInterface
    {
        $quicksightUserList = [];
        foreach ($quicksightUserTransfers as $quicksightUserTransfer) {
            $quicksightUserList[] = [
                'Arn' => $quicksightUserTransfer->getArnOrFail(),
                'PrincipalId' => $quicksightUserTransfer->getPrincipalIdOrFail(),
                'UserName' => $quicksightUserTransfer->getUserNameOrFail(),
                'Role' => $quicksightUserTransfer->getRoleOrFail(),
            ];
        }

        return new Result([
            static::RESPONSE_KEY_USER_LIST => $quicksightUserList,
        ]);
    }
}
