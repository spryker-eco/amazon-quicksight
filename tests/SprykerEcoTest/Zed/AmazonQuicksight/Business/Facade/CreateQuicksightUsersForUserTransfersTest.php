<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\AmazonQuicksight\Business\Facade;

use Aws\QuickSight\Exception\QuickSightException;
use Aws\Result;
use Aws\ResultInterface;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\UserCollectionResponseTransfer;
use Generated\Shared\Transfer\UserTransfer;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightDependencyProvider;
use SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface;
use SprykerEcoTest\Zed\AmazonQuicksight\AmazonQuicksightBusinessTester;

class CreateQuicksightUsersForUserTransfersTest extends Unit
{
    /**
     * @uses \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig::QUICKSIGHT_USER_ROLE_READER
     *
     * @var string
     */
    protected const QUICKSIGHT_USER_ROLE_READER = 'READER';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_QUICKSIGHT_USER_REGISTER_FAILURE = 'An internal failure occurred.';

    /**
     * @uses \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClient::ERROR_MESSAGE_USER_REGISTRATION_FAILED
     *
     * @var string
     */
    protected const ERROR_MESSAGE_USER_REGISTRATION_FAILED = 'Failed to register Quicksight user.';

    /**
     * @var \SprykerEcoTest\Zed\AmazonQuicksight\AmazonQuicksightBusinessTester
     */
    protected AmazonQuicksightBusinessTester $tester;

    /**
     * @return void
     */
    public function testShouldPersistQuicksightUserWhenUserSuccessfullyRegisteredInQuicksight(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUserWithNotPersistedQuicksightUserRole(static::QUICKSIGHT_USER_ROLE_READER);

        $awsQuicksightClientMock = $this->getAwsQuicksightClientMock();
        $awsQuicksightClientMock->method('registerUser')
            ->willReturn($this->createRegisterUserSuccessfulResponse($userTransfer));
        $this->tester->setDependency(
            AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT,
            $awsQuicksightClientMock,
        );

        $userCollectionResponseTransfer = (new UserCollectionResponseTransfer())->addUser($userTransfer);

        // Act
        $userCollectionResponseTransfer = $this->tester->getFacade()
            ->createQuicksightUsersForUserTransfers($userCollectionResponseTransfer);

        // Assert
        $this->assertCount(0, $userCollectionResponseTransfer->getErrors());
        $this->assertCount(1, $userCollectionResponseTransfer->getUsers());

        /** @var \Generated\Shared\Transfer\UserTransfer $userTransfer */
        $userTransfer = $userCollectionResponseTransfer->getUsers()->getIterator()->current();
        $this->assertNotNull($userTransfer->getQuicksightUser());
        $this->assertNotNull($userTransfer->getQuicksightUserOrFail()->getIdQuicksightUser());
        $this->assertNotNull($userTransfer->getQuicksightUserOrFail()->getArn());
        $this->assertNotNull($userTransfer->getQuicksightUserOrFail()->getPrincipalId());
    }

    /**
     * @return void
     */
    public function testShouldReturnErrorWhenUserQuicksightClientThrowsException(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUserWithNotPersistedQuicksightUserRole(static::QUICKSIGHT_USER_ROLE_READER);

        $awsQuicksightClientMock = $this->getAwsQuicksightClientMock();
        $awsQuicksightClientMock->method('registerUser')
            ->willThrowException($this->getQuicksightExceptionMock(static::ERROR_MESSAGE_QUICKSIGHT_USER_REGISTER_FAILURE));
        $this->tester->setDependency(
            AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT,
            $awsQuicksightClientMock,
        );

        $userCollectionResponseTransfer = (new UserCollectionResponseTransfer())->addUser($userTransfer);

        // Act
        $userCollectionResponseTransfer = $this->tester->getFacade()
            ->createQuicksightUsersForUserTransfers($userCollectionResponseTransfer);

        // Assert
        $this->assertCount(1, $userCollectionResponseTransfer->getErrors());
        $this->assertCount(1, $userCollectionResponseTransfer->getUsers());

        /** @var \Generated\Shared\Transfer\ErrorTransfer $errorTransfer */
        $errorTransfer = $userCollectionResponseTransfer->getErrors()->getIterator()->current();
        $this->assertSame(static::ERROR_MESSAGE_QUICKSIGHT_USER_REGISTER_FAILURE, $errorTransfer->getMessage());
        $this->assertSame((string)$userCollectionResponseTransfer->getUsers()->getIterator()->key(), $errorTransfer->getEntityIdentifier());

        /** @var \Generated\Shared\Transfer\UserTransfer $userTransfer */
        $userTransfer = $userCollectionResponseTransfer->getUsers()->getIterator()->current();
        $this->assertNotNull($userTransfer->getQuicksightUser());
        $this->assertNull($userTransfer->getQuicksightUserOrFail()->getIdQuicksightUser());
        $this->assertNull($userTransfer->getQuicksightUserOrFail()->getArn());
        $this->assertNull($userTransfer->getQuicksightUserOrFail()->getPrincipalId());
    }

    /**
     * @return void
     */
    public function testShouldReturnErrorWhenQuicksightUserRegisterResponseDoesNotContainUserKey(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUserWithNotPersistedQuicksightUserRole(static::QUICKSIGHT_USER_ROLE_READER);

        $awsQuicksightClientMock = $this->getAwsQuicksightClientMock();
        $awsQuicksightClientMock->method('registerUser')
            ->willReturn(new Result());
        $this->tester->setDependency(
            AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT,
            $awsQuicksightClientMock,
        );

        $userCollectionResponseTransfer = (new UserCollectionResponseTransfer())->addUser($userTransfer);

        // Act
        $userCollectionResponseTransfer = $this->tester->getFacade()
            ->createQuicksightUsersForUserTransfers($userCollectionResponseTransfer);

        // Assert
        $this->assertCount(1, $userCollectionResponseTransfer->getErrors());
        $this->assertCount(1, $userCollectionResponseTransfer->getUsers());

        /** @var \Generated\Shared\Transfer\ErrorTransfer $errorTransfer */
        $errorTransfer = $userCollectionResponseTransfer->getErrors()->getIterator()->current();
        $this->assertSame(static::ERROR_MESSAGE_USER_REGISTRATION_FAILED, $errorTransfer->getMessage());
        $this->assertSame((string)$userCollectionResponseTransfer->getUsers()->getIterator()->key(), $errorTransfer->getEntityIdentifier());

        /** @var \Generated\Shared\Transfer\UserTransfer $userTransfer */
        $userTransfer = $userCollectionResponseTransfer->getUsers()->getIterator()->current();
        $this->assertNotNull($userTransfer->getQuicksightUser());
        $this->assertNull($userTransfer->getQuicksightUserOrFail()->getIdQuicksightUser());
        $this->assertNull($userTransfer->getQuicksightUserOrFail()->getArn());
        $this->assertNull($userTransfer->getQuicksightUserOrFail()->getPrincipalId());
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getAwsQuicksightClientMock(): AmazonQuicksightToAwsQuicksightClientInterface
    {
        return $this->getMockBuilder(AmazonQuicksightToAwsQuicksightClientInterface::class)->getMock();
    }

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     *
     * @return \Aws\ResultInterface
     */
    protected function createRegisterUserSuccessfulResponse(UserTransfer $userTransfer): ResultInterface
    {
        $responseData = [
            'RequestId' => time(),
            'User' => [
                'Arn' => 'arn:aws:quicksight:eu-central-1:123456789012:user/default/' . $userTransfer->getUsername(),
                'PrincipalId' => '123456789012',
                'Role' => $userTransfer->getQuicksightUserOrFail()->getRoleOrFail(),
            ],
        ];

        return new Result($responseData);
    }

    /**
     * @param string $message
     *
     * @return \Aws\QuickSight\Exception\QuickSightException|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getQuicksightExceptionMock(string $message): QuickSightException
    {
        $quicksightExceptionMock = $this->getMockBuilder(QuickSightException::class)
            ->disableOriginalConstructor()
            ->getMock();
        $quicksightExceptionMock->method('getAwsErrorMessage')->willReturn($message);

        return $quicksightExceptionMock;
    }
}