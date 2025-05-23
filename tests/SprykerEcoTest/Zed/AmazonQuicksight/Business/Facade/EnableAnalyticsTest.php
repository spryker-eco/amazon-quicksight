<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\AmazonQuicksight\Business\Facade;

use Aws\Result;
use Aws\ResultInterface;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer;
use Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer;
use Generated\Shared\Transfer\QuicksightUserTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightDependencyProvider;
use SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface;
use SprykerEcoTest\Zed\AmazonQuicksight\AmazonQuicksightBusinessTester;

class EnableAnalyticsTest extends Unit
{
    /**
     * @uses \SprykerEco\Zed\AmazonQuicksight\Business\Validator\QuicksightAnalyticsRequestValidator::ERROR_MESSAGE_ENABLE_ANALYTICS_FAILED
     *
     * @var string
     */
    protected const ERROR_MESSAGE_ENABLE_ANALYTICS_FAILED = 'Failed to enable the analytics';

    /**
     * @uses \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID
     *
     * @var string
     */
    protected const DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID = 'defaultAssetBundleImportJobId';

    /**
     * @link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_DescribeAssetBundleImportJob.html#API_DescribeAssetBundleImportJob_ResponseSyntax
     *
     * @var string
     */
    protected const ASSET_BUNDLE_IMPORT_JOB_STATUS_SUCCESSFUL = 'SUCCESSFUL';

    /**
     * @link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_DescribeAssetBundleImportJob.html#API_DescribeAssetBundleImportJob_ResponseSyntax
     *
     * @var string
     */
    protected const ASSET_BUNDLE_IMPORT_JOB_STATUS_IN_PROGRESS = 'IN_PROGRESS';

    /**
     * @link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_DescribeAssetBundleImportJob.html#API_DescribeAssetBundleImportJob_ResponseSyntax
     *
     * @var string
     */
    protected const ASSET_BUNDLE_IMPORT_JOB_STATUS_FAILED_ROLLBACK_COMPLETED = 'FAILED_ROLLBACK_COMPLETED';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_TEST = 'test error message';

    /**
     * @uses \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig::QUICKSIGHT_USER_ROLE_READER
     *
     * @var string
     */
    protected const QUICKSIGHT_USER_ROLE_READER = 'READER';

    /**
     * @uses \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig::QUICKSIGHT_USER_ROLE_AUTHOR
     *
     * @var string
     */
    protected const QUICKSIGHT_USER_ROLE_AUTHOR = 'AUTHOR';

    /**
     * @uses \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig::DEFAULT_NEW_ASSET_BUNDLE_IMPORT_JOB_STATUS
     *
     * @var string
     */
    protected const DEFAULT_NEW_ASSET_BUNDLE_IMPORT_JOB_STATUS = 'QUEUED_FOR_IMMEDIATE_EXECUTION';

    /**
     * @var \SprykerEcoTest\Zed\AmazonQuicksight\AmazonQuicksightBusinessTester
     */
    protected AmazonQuicksightBusinessTester $tester;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->tester->ensureQuicksightAssetBundleImportJobTableIsEmpty();
        $this->tester->ensureQuicksightUserTableIsEmpty();
    }

    /**
     * @dataProvider throwsRequiredTransferPropertyExceptionWhenRequiredPropertiesAreNotSetDataProvider
     *
     * @param \Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer
     *
     * @return void
     */
    public function testThrowsRequiredTransferPropertyExceptionWhenRequiredPropertiesAreNotSet(
        EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer
    ): void {
        // Assert
        $this->expectException(RequiredTransferPropertyException::class);
        $this->tester->setDependency(
            AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT,
            $this->tester->getAwsQuicksightClientMock(),
        );

        // Act
        $this->tester->getFacade()->enableAnalytics($enableQuicksightAnalyticsRequestTransfer);
    }

    /**
     * @dataProvider returnsResponseWithErrorWhenValidationFailsDataProvider
     *
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer
     * @param \Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer
     *
     * @return void
     */
    public function testReturnsResponseWithErrorWhenValidationFails(
        QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer,
        EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer
    ): void {
        // Arrange
        $this->tester->haveQuicksightAssetBundleImportJob($quicksightAssetBundleImportJobTransfer->toArray());
        $this->tester->setDependency(
            AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT,
            $this->tester->getAwsQuicksightClientMock(),
        );

        // Act
        $enableQuicksightAnalyticsResponseTransfer = $this->tester
            ->getFacade()
            ->enableAnalytics($enableQuicksightAnalyticsRequestTransfer);

        // Assert
        $this->assertCount(1, $enableQuicksightAnalyticsResponseTransfer->getErrors());
        $this->assertSame(
            static::ERROR_MESSAGE_ENABLE_ANALYTICS_FAILED,
            $enableQuicksightAnalyticsResponseTransfer->getErrors()->getIterator()->current()->getMessage(),
        );
    }

    /**
     * @return void
     */
    public function testReturnsResponseWithErrorWhenDataSetsDeleteFails(): void
    {
        // Arrange
        $enableQuicksightAnalyticsRequestTransfer = $this->createEnableQuicksightAnalyticsRequestTransferWithUser();
        $awsQuicksightClientMock = $this->tester->getAwsQuicksightClientMockWithErrorResponse(
            $this->tester->getQuicksightExceptionMock(static::ERROR_MESSAGE_TEST),
            'deleteDataSet',
        );
        $awsQuicksightClientMock->expects($this->never())->method('startAssetBundleImportJob');
        $this->tester->setDependency(
            AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT,
            $awsQuicksightClientMock,
        );

        // Act
        $enableQuicksightAnalyticsResponseTransfer = $this->tester
            ->getFacadeWithMocks()
            ->enableAnalytics($enableQuicksightAnalyticsRequestTransfer);

        // Assert
        $this->assertCount(1, $enableQuicksightAnalyticsResponseTransfer->getErrors());
        $this->assertSame(
            static::ERROR_MESSAGE_TEST,
            $enableQuicksightAnalyticsResponseTransfer->getErrors()->getIterator()->current()->getMessage(),
        );
    }

    /**
     * @return void
     */
    public function testReturnsResponseWithErrorWhenStartAssetBundleImportJobFails(): void
    {
        // Arrange
        $enableQuicksightAnalyticsRequestTransfer = $this->createEnableQuicksightAnalyticsRequestTransferWithUser();
        $this->tester->setDependency(
            AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT,
            $this->tester->getAwsQuicksightClientMockWithErrorResponse(
                $this->tester->getQuicksightExceptionMock(static::ERROR_MESSAGE_TEST),
                'startAssetBundleImportJob',
            ),
        );

        // Act
        $enableQuicksightAnalyticsResponseTransfer = $this->tester
            ->getFacadeWithMocks()
            ->enableAnalytics($enableQuicksightAnalyticsRequestTransfer);

        // Assert
        $this->assertCount(1, $enableQuicksightAnalyticsResponseTransfer->getErrors());
        $this->assertSame(
            static::ERROR_MESSAGE_TEST,
            $enableQuicksightAnalyticsResponseTransfer->getErrors()->getIterator()->current()->getMessage(),
        );
    }

    /**
     * @return void
     */
    public function testCreatesNewAssetBundleImportJob(): void
    {
        // Arrange
        $enableQuicksightAnalyticsRequestTransfer = $this->createEnableQuicksightAnalyticsRequestTransferWithUser();
        $this->tester->setDependency(
            AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT,
            $this->getAwsQuicksightClientMockWithExpectation(new Result(), 'startAssetBundleImportJob'),
        );

        // Act
        $enableQuicksightAnalyticsResponseTransfer = $this->tester
            ->getFacadeWithMocks()
            ->enableAnalytics($enableQuicksightAnalyticsRequestTransfer);

        // Assert
        $this->assertCount(0, $enableQuicksightAnalyticsResponseTransfer->getErrors());
        $this->assertNotNull($this->tester->findQuicksightAssetBundleImportJobQueryByJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID));
    }

    /**
     * @return void
     */
    public function testResetsNotFinishedAssetBundleImportJob(): void
    {
        // Arrange
        $this->tester->haveQuicksightAssetBundleImportJob([
            QuicksightAssetBundleImportJobTransfer::IS_INITIALIZED => false,
            QuicksightAssetBundleImportJobTransfer::STATUS => static::ASSET_BUNDLE_IMPORT_JOB_STATUS_FAILED_ROLLBACK_COMPLETED,
            QuicksightAssetBundleImportJobTransfer::JOB_ID => static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID,
        ]);
        $enableQuicksightAnalyticsRequestTransfer = $this->createEnableQuicksightAnalyticsRequestTransferWithUser();
        $this->tester->setDependency(
            AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT,
            $this->getAwsQuicksightClientMockWithExpectation(new Result(), 'startAssetBundleImportJob'),
        );

        // Act
        $enableQuicksightAnalyticsResponseTransfer = $this->tester
            ->getFacadeWithMocks()
            ->enableAnalytics($enableQuicksightAnalyticsRequestTransfer);

        // Assert
        $this->assertCount(0, $enableQuicksightAnalyticsResponseTransfer->getErrors());
        $quicksightAssetBundleImportJobEntity = $this->tester->findQuicksightAssetBundleImportJobQueryByJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID);
        $this->assertNotNull($quicksightAssetBundleImportJobEntity);
        $this->assertSame(
            static::DEFAULT_NEW_ASSET_BUNDLE_IMPORT_JOB_STATUS,
            $quicksightAssetBundleImportJobEntity->getStatus(),
        );
        $this->assertSame(
            static::DEFAULT_NEW_ASSET_BUNDLE_IMPORT_JOB_STATUS,
            $quicksightAssetBundleImportJobEntity->getStatus(),
        );
    }

    /**
     * @return array<string, list<\Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer>>
     */
    protected function throwsRequiredTransferPropertyExceptionWhenRequiredPropertiesAreNotSetDataProvider(): array
    {
        return [
            'When Asset bundle import job ID is not set' => [
                (new EnableQuicksightAnalyticsRequestTransfer())->setUser((new UserTransfer())),
            ],
            'When User is not set' => [
                (new EnableQuicksightAnalyticsRequestTransfer())->setAssetBundleImportJobId('testId'),
            ],
        ];
    }

    /**
     * @return array<string, list<mixed>>
     */
    protected function returnsResponseWithErrorWhenValidationFailsDataProvider(): array
    {
        return [
            'Asset bundle is initialized' => [
                (new QuicksightAssetBundleImportJobTransfer())
                    ->setIsInitialized(true)
                    ->setJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID)
                    ->setStatus(static::ASSET_BUNDLE_IMPORT_JOB_STATUS_SUCCESSFUL),
                (new EnableQuicksightAnalyticsRequestTransfer())
                    ->setAssetBundleImportJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID)
                    ->setUser((new UserTransfer())->setQuicksightUser(
                        (new QuicksightUserTransfer())->setRole(static::QUICKSIGHT_USER_ROLE_AUTHOR),
                    )),
            ],
            'Asset bundle initialization is in progress' => [
                (new QuicksightAssetBundleImportJobTransfer())
                    ->setIsInitialized(false)
                    ->setJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID)
                    ->setStatus(static::ASSET_BUNDLE_IMPORT_JOB_STATUS_IN_PROGRESS),
                (new EnableQuicksightAnalyticsRequestTransfer())
                    ->setAssetBundleImportJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID)
                    ->setUser((new UserTransfer())->setQuicksightUser(
                        (new QuicksightUserTransfer())->setRole(static::QUICKSIGHT_USER_ROLE_AUTHOR),
                    )),
            ],
            'Quicksight user does not exist' => [
                (new QuicksightAssetBundleImportJobTransfer())
                    ->setIsInitialized(false)
                    ->setJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID)
                    ->setStatus(static::ASSET_BUNDLE_IMPORT_JOB_STATUS_FAILED_ROLLBACK_COMPLETED),
                (new EnableQuicksightAnalyticsRequestTransfer())
                    ->setAssetBundleImportJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID)
                    ->setUser(new UserTransfer()),
            ],
            'Quicksight user is not Author' => [
                (new QuicksightAssetBundleImportJobTransfer())
                    ->setIsInitialized(false)
                    ->setJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID)
                    ->setStatus(static::ASSET_BUNDLE_IMPORT_JOB_STATUS_FAILED_ROLLBACK_COMPLETED),
                (new EnableQuicksightAnalyticsRequestTransfer())
                    ->setAssetBundleImportJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID)
                    ->setUser((new UserTransfer())->setQuicksightUser(
                        (new QuicksightUserTransfer())->setRole(static::QUICKSIGHT_USER_ROLE_READER),
                    )),
            ],
        ];
    }

    /**
     * @param string $quicksightUserRole
     *
     * @return \Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer
     */
    protected function createEnableQuicksightAnalyticsRequestTransferWithUser(
        string $quicksightUserRole = self::QUICKSIGHT_USER_ROLE_AUTHOR
    ): EnableQuicksightAnalyticsRequestTransfer {
        $userTransfer = $this->tester->haveUser();
        $userTransfer->setQuicksightUser($this->tester->haveQuicksightUser($userTransfer, [
            QuicksightUserTransfer::ROLE => $quicksightUserRole,
        ]));

        return (new EnableQuicksightAnalyticsRequestTransfer())
            ->setUser($userTransfer)
            ->setAssetBundleImportJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID);
    }

    /**
     * @param \Aws\ResultInterface $result
     * @param string $methodName
     *
     * @return \SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getAwsQuicksightClientMockWithExpectation(
        ResultInterface $result,
        string $methodName
    ): AmazonQuicksightToAwsQuicksightClientInterface {
        $awsQuicksightClientMock = $this->getMockBuilder(AmazonQuicksightToAwsQuicksightClientInterface::class)
            ->getMock();
        $awsQuicksightClientMock->expects($this->once())->method($methodName)->willReturn($result);

        return $awsQuicksightClientMock;
    }
}
