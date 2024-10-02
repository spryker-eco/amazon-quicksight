<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\AmazonQuicksight\Business\Facade;

use Aws\Result;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer;
use Generated\Shared\Transfer\QuicksightUserTransfer;
use Generated\Shared\Transfer\ResetQuicksightAnalyticsRequestTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightDependencyProvider;
use SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface;
use SprykerEcoTest\Zed\AmazonQuicksight\AmazonQuicksightBusinessTester;

class ResetAnalyticsTest extends Unit
{
    /**
     * @uses \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig::QUICKSIGHT_USER_ROLE_AUTHOR
     *
     * @var string
     */
    protected const QUICKSIGHT_USER_ROLE_AUTHOR = 'AUTHOR';

    /**
     * @uses \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig::QUICKSIGHT_USER_ROLE_READER
     *
     * @var string
     */
    protected const QUICKSIGHT_USER_ROLE_READER = 'READER';

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
     * @uses \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig::DEFAULT_NEW_ASSET_BUNDLE_IMPORT_JOB_STATUS
     *
     * @var string
     */
    protected const DEFAULT_NEW_ASSET_BUNDLE_IMPORT_JOB_STATUS = 'QUEUED_FOR_IMMEDIATE_EXECUTION';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_TEST = 'test error message';

    /**
     * @uses \SprykerEco\Zed\AmazonQuicksight\Business\Validator\QuicksightAnalyticsRequestValidator::ERROR_MESSAGE_RESET_ANALYTICS_FAILED
     *
     * @var string
     */
    protected const ERROR_MESSAGE_RESET_ANALYTICS_FAILED = 'Failed to reset the analytics';

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
     * @param \Generated\Shared\Transfer\ResetQuicksightAnalyticsRequestTransfer $resetQuicksightAnalyticsRequestTransfer
     *
     * @return void
     */
    public function testThrowsRequiredTransferPropertyExceptionWhenRequiredPropertiesAreNotSet(
        ResetQuicksightAnalyticsRequestTransfer $resetQuicksightAnalyticsRequestTransfer,
    ): void {
        // Assert
        $this->expectException(RequiredTransferPropertyException::class);

        // Act
        $this->tester->getFacade()->resetAnalytics($resetQuicksightAnalyticsRequestTransfer);
    }

    /**
     * @dataProvider returnsResponseWithErrorWhenValidationFailsDataProvider
     *
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer|null $quicksightAssetBundleImportJobTransfer
     * @param \Generated\Shared\Transfer\ResetQuicksightAnalyticsRequestTransfer $resetQuicksightAnalyticsRequestTransfer
     *
     * @return void
     */
    public function testReturnsResponseWithErrorWhenValidationFails(
        ?QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer,
        ResetQuicksightAnalyticsRequestTransfer $resetQuicksightAnalyticsRequestTransfer,
    ): void {
        // Arrange
        if ($quicksightAssetBundleImportJobTransfer) {
            $quicksightAssetBundleImportJobTransfer->setJobIdOrFail(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID);
            $this->tester->haveQuicksightAssetBundleImportJob($quicksightAssetBundleImportJobTransfer->modifiedToArray());
        }
        $resetQuicksightAnalyticsRequestTransfer->setAssetBundleImportJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID);

        // Act
        $resetQuicksightAnalyticsResponseTransfer = $this->tester
            ->getFacade()
            ->resetAnalytics($resetQuicksightAnalyticsRequestTransfer);

        // Assert
        $this->assertCount(1, $resetQuicksightAnalyticsResponseTransfer->getErrors());
        $this->assertSame(
            static::ERROR_MESSAGE_RESET_ANALYTICS_FAILED,
            $resetQuicksightAnalyticsResponseTransfer->getErrors()->getIterator()->current()->getMessage(),
        );
    }

    /**
     * @return void
     */
    public function testReturnsResponseWithErrorWhenStartAssetBundleImportJobFails(): void
    {
        // Arrange
        $this->haveSuccessfulQuicksightAssetBundleImportJob();
        $resetQuicksightAnalyticsRequestTransfer = $this->createResetQuicksightAnalyticsRequestTransfer();
        $this->tester->setDependency(
            AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT,
            $this->tester->getAwsQuicksightClientMockWithErrorResponse(
                $this->tester->getQuicksightExceptionMock(static::ERROR_MESSAGE_TEST),
                'startAssetBundleImportJob',
            ),
        );

        // Act
        $resetQuicksightAnalyticsResponseTransfer = $this->tester
            ->getFacadeWithMocks()
            ->resetAnalytics($resetQuicksightAnalyticsRequestTransfer);

        // Assert
        $this->assertCount(1, $resetQuicksightAnalyticsResponseTransfer->getErrors());
        $this->assertSame(
            static::ERROR_MESSAGE_TEST,
            $resetQuicksightAnalyticsResponseTransfer->getErrors()->getIterator()->current()->getMessage(),
        );
    }

    /**
     * @return void
     */
    public function testResetsAssetBundleImportJob(): void
    {
        // Arrange
        $this->haveSuccessfulQuicksightAssetBundleImportJob();
        $resetQuicksightAnalyticsRequestTransfer = $this->createResetQuicksightAnalyticsRequestTransfer();
        $awsQuicksightClientMock = $this->getMockBuilder(AmazonQuicksightToAwsQuicksightClientInterface::class)
            ->getMock();
        $awsQuicksightClientMock->expects($this->once())->method('startAssetBundleImportJob')->willReturn(new Result());
        $this->tester->setDependency(AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT, $awsQuicksightClientMock);

        // Act
        $resetQuicksightAnalyticsResponseTransfer = $this->tester
            ->getFacadeWithMocks()
            ->resetAnalytics($resetQuicksightAnalyticsRequestTransfer);

        // Assert
        $this->assertCount(0, $resetQuicksightAnalyticsResponseTransfer->getErrors());
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
                (new ResetQuicksightAnalyticsRequestTransfer())->setUser((new UserTransfer())),
            ],
            'When User is not set' => [
                (new ResetQuicksightAnalyticsRequestTransfer())->setAssetBundleImportJobId('testId'),
            ],
        ];
    }

    /**
     * @return array<string, list<mixed>>
     */
    protected function returnsResponseWithErrorWhenValidationFailsDataProvider(): array
    {
        return [
            'When asset bundle import job does not exist' => [
                null,
                (new ResetQuicksightAnalyticsRequestTransfer())->setUser((new UserTransfer())->setQuicksightUser(
                    (new QuicksightUserTransfer())->setRole(static::QUICKSIGHT_USER_ROLE_AUTHOR),
                )),
            ],
            'When asset bundle import is not initialized' => [
                (new QuicksightAssetBundleImportJobTransfer())
                    ->setIsInitialized(false)
                    ->setStatus(static::ASSET_BUNDLE_IMPORT_JOB_STATUS_SUCCESSFUL),
                (new ResetQuicksightAnalyticsRequestTransfer())->setUser((new UserTransfer())->setQuicksightUser(
                    (new QuicksightUserTransfer())->setRole(static::QUICKSIGHT_USER_ROLE_AUTHOR),
                )),
            ],
            'When asset bundle import initialization is in progress' => [
                (new QuicksightAssetBundleImportJobTransfer())
                    ->setIsInitialized(true)
                    ->setStatus(static::ASSET_BUNDLE_IMPORT_JOB_STATUS_IN_PROGRESS),
                (new ResetQuicksightAnalyticsRequestTransfer())->setUser((new UserTransfer())->setQuicksightUser(
                    (new QuicksightUserTransfer())->setRole(static::QUICKSIGHT_USER_ROLE_AUTHOR),
                )),
            ],
            'When Quicksight user does not exist' => [
                (new QuicksightAssetBundleImportJobTransfer())
                    ->setIsInitialized(true)
                    ->setStatus(static::ASSET_BUNDLE_IMPORT_JOB_STATUS_SUCCESSFUL),
                (new ResetQuicksightAnalyticsRequestTransfer())->setUser(new UserTransfer()),
            ],
            'When Quicksight user is not Author' => [
                (new QuicksightAssetBundleImportJobTransfer())
                    ->setIsInitialized(true)
                    ->setStatus(static::ASSET_BUNDLE_IMPORT_JOB_STATUS_IN_PROGRESS),
                (new ResetQuicksightAnalyticsRequestTransfer())->setUser((new UserTransfer())->setQuicksightUser(
                    (new QuicksightUserTransfer())->setRole(static::QUICKSIGHT_USER_ROLE_READER),
                )),
            ],
        ];
    }

    /**
     * @return void
     */
    protected function haveSuccessfulQuicksightAssetBundleImportJob(): void
    {
        $this->tester->haveQuicksightAssetBundleImportJob([
            QuicksightAssetBundleImportJobTransfer::IS_INITIALIZED => true,
            QuicksightAssetBundleImportJobTransfer::STATUS => static::ASSET_BUNDLE_IMPORT_JOB_STATUS_SUCCESSFUL,
            QuicksightAssetBundleImportJobTransfer::JOB_ID => static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID,
        ]);
    }

    /**
     * @return \Generated\Shared\Transfer\ResetQuicksightAnalyticsRequestTransfer
     */
    protected function createResetQuicksightAnalyticsRequestTransfer(): ResetQuicksightAnalyticsRequestTransfer
    {
        return (new ResetQuicksightAnalyticsRequestTransfer())
            ->setAssetBundleImportJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID)
            ->setUser((new UserTransfer())->setQuicksightUser(
                (new QuicksightUserTransfer())->setRole(static::QUICKSIGHT_USER_ROLE_AUTHOR)->setArn('testArn'),
            ));
    }
}
