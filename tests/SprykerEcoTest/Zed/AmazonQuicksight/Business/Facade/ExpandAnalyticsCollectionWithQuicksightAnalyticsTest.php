<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\AmazonQuicksight\Business\Facade;

use ArrayObject;
use Aws\Result;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\AnalyticsCollectionTransfer;
use Generated\Shared\Transfer\AnalyticsRequestTransfer;
use Generated\Shared\Transfer\ErrorTransfer;
use Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer;
use Generated\Shared\Transfer\QuicksightEmbedUrlTransfer;
use Generated\Shared\Transfer\QuicksightGenerateEmbedUrlResponseTransfer;
use Generated\Shared\Transfer\QuicksightUserTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Spryker\Shared\Kernel\Transfer\Exception\NullValueException;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightDependencyProvider;
use SprykerEcoTest\Zed\AmazonQuicksight\AmazonQuicksightBusinessTester;
use Twig\Environment;

class ExpandAnalyticsCollectionWithQuicksightAnalyticsTest extends Unit
{
    /**
     * @uses \Spryker\Zed\Twig\Communication\Plugin\Application\TwigApplicationPlugin::SERVICE_TWIG
     *
     * @var string
     */
    protected const SERVICE_TWIG = 'twig';

    /**
     * @uses \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AssetBundleAmazonQuicksightApiClient::ERROR_MESSAGE_DESCRIBE_ASSET_BUNDLE_IMPORT_JOB_FAILED
     *
     * @var string
     */
    protected const ERROR_MESSAGE_DESCRIBE_ASSET_BUNDLE_IMPORT_JOB_FAILED = 'Failed to sync asset bundle import job list.';

    /**
     * @uses \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\UserAmazonQuicksightApiClient::ERROR_MESSAGE_EMBED_URL_GENERATION_FAILED
     *
     * @var string
     */
    protected const ERROR_MESSAGE_EMBED_URL_GENERATION_FAILED = 'Failed to generate embed URL.';

    /**
     * @uses \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\UserAmazonQuicksightApiClient::RESPONSE_KEY_EMBED_URL
     *
     * @var string
     */
    protected const RESPONSE_KEY_EMBED_URL = 'EmbedUrl';

    /**
     * @uses \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AssetBundleAmazonQuicksightApiClient::RESPONSE_KEY_JOB_STATUS
     *
     * @var string
     */
    protected const RESPONSE_KEY_JOB_STATUS = 'JobStatus';

    /**
     * @var string
     */
    protected const EMBED_URL_TEST = 'test-embed-url';

    /**
     * @uses \SprykerEco\Zed\AmazonQuicksight\Business\Expander\AnalyticsExpander::TEMPLATE_PATH_QUICKSIGHT_ANALYTICS
     *
     * @var string
     */
    protected const TEMPLATE_PATH_QUICKSIGHT_ANALYTICS = '@AmazonQuicksight/_partials/quicksight-analytics.twig';

    /**
     * @uses \SprykerEco\Zed\AmazonQuicksight\Business\Expander\AnalyticsExpander::TEMPLATE_PATH_QUICKSIGHT_ANALYTICS_ACTIONS
     *
     * @var string
     */
    protected const TEMPLATE_PATH_QUICKSIGHT_ANALYTICS_ACTIONS = '@AmazonQuicksight/_partials/quicksight-analytics-actions.twig';

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
     * @return void
     */
    public function testExpandsCollectionWhenQuicksightUserDoesNotExist(): void
    {
        // Arrange
        $this->tester->getContainer()->set(static::SERVICE_TWIG, $this->getTwigMock(
            false,
            false,
            false,
            new QuicksightGenerateEmbedUrlResponseTransfer(),
        ));
        $analyticsRequestTransfer = $this->tester->haveAnalyticsRequestWithUser();

        // Act
        $analyticsCollectionTransfer = $this->tester
            ->getFacade()
            ->expandAnalyticsCollectionWithQuicksightAnalytics($analyticsRequestTransfer, new AnalyticsCollectionTransfer());

        // Assert
        $this->assertCount(1, $analyticsCollectionTransfer->getAnalyticsList());
    }

    /**
     * @return void
     */
    public function testExpandsCollectionWhenQuicksightUserExistsWithoutRole(): void
    {
        // Arrange
        $this->tester->getContainer()->set(static::SERVICE_TWIG, $this->getTwigMock(
            false,
            false,
            false,
            new QuicksightGenerateEmbedUrlResponseTransfer(),
        ));
        $analyticsRequestTransfer = $this->tester->haveAnalyticsRequestWithQuicksightUser();

        // Act
        $analyticsCollectionTransfer = $this->tester
            ->getFacade()
            ->expandAnalyticsCollectionWithQuicksightAnalytics($analyticsRequestTransfer, new AnalyticsCollectionTransfer());

        // Assert
        $this->assertCount(1, $analyticsCollectionTransfer->getAnalyticsList());
    }

    /**
     * @dataProvider expandsCollectionWhenQuicksightUserExistsWithRoleDataProvider
     *
     * @param string $role
     *
     * @return void
     */
    public function testExpandsCollectionWhenQuicksightUserExistsWithRole(string $role): void
    {
        // Arrange
        $this->tester->getContainer()->set(static::SERVICE_TWIG, $this->getTwigMock(
            false,
            false,
            true,
            new QuicksightGenerateEmbedUrlResponseTransfer(),
        ));
        $analyticsRequestTransfer = $this->tester->haveAnalyticsRequestWithQuicksightUser($role);

        // Act
        $analyticsCollectionTransfer = $this->tester
            ->getFacade()
            ->expandAnalyticsCollectionWithQuicksightAnalytics($analyticsRequestTransfer, new AnalyticsCollectionTransfer());

        // Assert
        $this->assertCount(1, $analyticsCollectionTransfer->getAnalyticsList());
    }

    /**
     * @dataProvider expandsCollectionWhenQuicksightAssetBundleImportJobIsInProgressGeneratesEmbedUrlDataProvider
     *
     * @param bool $isInitializedInitially
     * @param bool $isInitializedAfterSync
     * @param bool $isInitializationInProgress
     * @param string $jobStatusAfterSync
     * @param \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlResponseTransfer $quicksightGenerateEmbedUrlResponseTransfer
     *
     * @return void
     */
    public function testExpandsCollectionWhenQuicksightAssetBundleImportJobIsInProgressSyncsData(
        bool $isInitializedInitially,
        bool $isInitializedAfterSync,
        bool $isInitializationInProgress,
        string $jobStatusAfterSync,
        QuicksightGenerateEmbedUrlResponseTransfer $quicksightGenerateEmbedUrlResponseTransfer
    ): void {
        // Arrange
        $this->tester->getContainer()->set(static::SERVICE_TWIG, $this->getTwigMock(
            $isInitializedAfterSync,
            $isInitializationInProgress,
            true,
            $quicksightGenerateEmbedUrlResponseTransfer,
            (new QuicksightAssetBundleImportJobTransfer())
                ->setIsInitialized($isInitializedAfterSync)
                ->setJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID)
                ->setStatus($jobStatusAfterSync)
                ->setErrors(new ArrayObject()),
        ));
        $awsQuicksightClientMock = $this->tester->getAwsQuicksightClientMockWithSuccessfulResponse(
            new Result(['RequestId' => time(), static::RESPONSE_KEY_JOB_STATUS => $jobStatusAfterSync]),
            'describeAssetBundleImportJob',
        );
        $awsQuicksightClientMock->method('generateEmbedUrlForRegisteredUser')
            ->willReturn(new Result(['RequestId' => time(), static::RESPONSE_KEY_EMBED_URL => static::EMBED_URL_TEST]));
        $this->tester->setDependency(AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT, $awsQuicksightClientMock);
        $analyticsRequestTransfer = $this->tester->haveAnalyticsRequestWithQuicksightUser(static::QUICKSIGHT_USER_ROLE_AUTHOR);
        $this->haveQuicksightAssetBundleImportJob($isInitializedInitially);

        // Act
        $analyticsCollectionTransfer = $this->tester
            ->getFacade()
            ->expandAnalyticsCollectionWithQuicksightAnalytics($analyticsRequestTransfer, new AnalyticsCollectionTransfer());

        // Assert
        $this->assertCount(1, $analyticsCollectionTransfer->getAnalyticsList());
        $quicksightAssetBundleImportJobEntity = $this->tester
            ->findQuicksightAssetBundleImportJobQueryByJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID);
        $this->assertNotNull($quicksightAssetBundleImportJobEntity);
        $this->assertEquals(
            $jobStatusAfterSync,
            $quicksightAssetBundleImportJobEntity->getStatus(),
        );
    }

    /**
     * @dataProvider expandsCollectionWhenDescribeAssetBundleImportJobApiRequestFailsDataProvider
     *
     * @param bool $throwsException
     *
     * @return void
     */
    public function testExpandsCollectionWhenDescribeAssetBundleImportJobApiRequestFails(bool $throwsException): void
    {
        // Arrange
        $this->tester->getContainer()->set(static::SERVICE_TWIG, $this->getTwigMock(
            false,
            true,
            true,
            new QuicksightGenerateEmbedUrlResponseTransfer(),
            (new QuicksightAssetBundleImportJobTransfer())
                ->setIsInitialized(false)
                ->setJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID)
                ->setStatus(static::ASSET_BUNDLE_IMPORT_JOB_STATUS_IN_PROGRESS)
                ->addError((new ErrorTransfer())
                    ->setEntityIdentifier(null)
                    ->setParameters(null)
                    ->setMessage(static::ERROR_MESSAGE_DESCRIBE_ASSET_BUNDLE_IMPORT_JOB_FAILED)),
        ));
        $awsQuicksightClientMock = $throwsException
            ? $this->tester->getAwsQuicksightClientMockWithErrorResponse(
                $this->tester->getQuicksightExceptionMock(static::ERROR_MESSAGE_DESCRIBE_ASSET_BUNDLE_IMPORT_JOB_FAILED),
                'describeAssetBundleImportJob',
            )
            : $this->tester->getAwsQuicksightClientMockWithSuccessfulResponse(new Result([]), 'describeAssetBundleImportJob');
        $this->tester->setDependency(AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT, $awsQuicksightClientMock);
        $analyticsRequestTransfer = $this->tester->haveAnalyticsRequestWithQuicksightUser(static::QUICKSIGHT_USER_ROLE_AUTHOR);
        $this->haveQuicksightAssetBundleImportJob(false);

        // Act
        $analyticsCollectionTransfer = $this->tester
            ->getFacade()
            ->expandAnalyticsCollectionWithQuicksightAnalytics($analyticsRequestTransfer, new AnalyticsCollectionTransfer());

        // Assert
        $this->assertCount(1, $analyticsCollectionTransfer->getAnalyticsList());
    }

    /**
     * @dataProvider expandsCollectionWhenGenerateEmbedUrlApiRequestFailsDataProvider
     *
     * @param bool $throwsException
     *
     * @return void
     */
    public function testExpandsCollectionWhenGenerateEmbedUrlApiRequestFails(bool $throwsException): void
    {
        // Arrange
        $this->tester->getContainer()->set(static::SERVICE_TWIG, $this->getTwigMock(
            true,
            false,
            true,
            (new QuicksightGenerateEmbedUrlResponseTransfer())
                ->addError((new ErrorTransfer())->setMessage(static::ERROR_MESSAGE_EMBED_URL_GENERATION_FAILED)),
            (new QuicksightAssetBundleImportJobTransfer())
                ->setIsInitialized(true)
                ->setJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID)
                ->setStatus(static::ASSET_BUNDLE_IMPORT_JOB_STATUS_SUCCESSFUL)
                ->setErrors(new ArrayObject()),
        ));
        $awsQuicksightClientMock = $this->tester->getAwsQuicksightClientMockWithSuccessfulResponse(
            new Result(['RequestId' => time(), static::RESPONSE_KEY_JOB_STATUS => static::ASSET_BUNDLE_IMPORT_JOB_STATUS_SUCCESSFUL]),
            'describeAssetBundleImportJob',
        );
        $throwsException
            ? $awsQuicksightClientMock->method('generateEmbedUrlForRegisteredUser')
                ->willThrowException($this->tester->getQuicksightExceptionMock(static::ERROR_MESSAGE_EMBED_URL_GENERATION_FAILED))
            : $awsQuicksightClientMock->method('generateEmbedUrlForRegisteredUser')->willReturn(new Result([]));

        $this->tester->setDependency(AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT, $awsQuicksightClientMock);
        $analyticsRequestTransfer = $this->tester->haveAnalyticsRequestWithQuicksightUser(static::QUICKSIGHT_USER_ROLE_AUTHOR);
        $this->haveQuicksightAssetBundleImportJob(false);

        // Act
        $analyticsCollectionTransfer = $this->tester
            ->getFacade()
            ->expandAnalyticsCollectionWithQuicksightAnalytics($analyticsRequestTransfer, new AnalyticsCollectionTransfer());

        // Assert
        $this->assertCount(1, $analyticsCollectionTransfer->getAnalyticsList());
    }

    /**
     * @dataProvider expandsCollectionWhenQuicksightAssetBundleImportJobFinishedDataProvider
     *
     * @param bool $isInitializedInitially
     * @param string $initialJobStatus
     * @param \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlResponseTransfer $quicksightGenerateEmbedUrlResponseTransfer
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer
     * @param array $errorTransfers
     *
     * @return void
     */
    public function testExpandsCollectionWhenQuicksightAssetBundleImportJobFinished(
        bool $isInitializedInitially,
        string $initialJobStatus,
        QuicksightGenerateEmbedUrlResponseTransfer $quicksightGenerateEmbedUrlResponseTransfer,
        QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer,
        array $errorTransfers
    ): void {
        // Arrange
        $this->tester->getContainer()->set(static::SERVICE_TWIG, $this->getTwigMock(
            $isInitializedInitially,
            false,
            true,
            $quicksightGenerateEmbedUrlResponseTransfer,
            $quicksightAssetBundleImportJobTransfer,
        ));
        $awsQuicksightClientMock = $this->tester->getAwsQuicksightClientMockWithSuccessfulResponse(
            new Result(['RequestId' => time(), static::RESPONSE_KEY_EMBED_URL => static::EMBED_URL_TEST]),
            'generateEmbedUrlForRegisteredUser',
        );
        $this->tester->setDependency(AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT, $awsQuicksightClientMock);
        $analyticsRequestTransfer = $this->tester->haveAnalyticsRequestWithQuicksightUser(static::QUICKSIGHT_USER_ROLE_AUTHOR);
        $this->haveQuicksightAssetBundleImportJob($isInitializedInitially, $initialJobStatus, $errorTransfers);

        // Act
        $analyticsCollectionTransfer = $this->tester
            ->getFacade()
            ->expandAnalyticsCollectionWithQuicksightAnalytics($analyticsRequestTransfer, new AnalyticsCollectionTransfer());

        // Assert
        $this->assertCount(1, $analyticsCollectionTransfer->getAnalyticsList());
    }

    /**
     * @dataProvider throwsExceptionWhenRequiredPropertiesAreNotSetDataProvider
     *
     * @param \Generated\Shared\Transfer\AnalyticsRequestTransfer $analyticsRequestTransfer
     *
     * @return void
     */
    public function testThrowsExceptionWhenRequiredPropertiesAreNotSet(
        AnalyticsRequestTransfer $analyticsRequestTransfer
    ): void {
        // Arrange
        $this->tester->mockFactoryMethod('getRepository', $this->tester->getAmazonQuicksightRepositoryMock());

        // Assert
        $this->expectException(NullValueException::class);

        // Act
        $this->tester
            ->getFacade()
            ->expandAnalyticsCollectionWithQuicksightAnalytics($analyticsRequestTransfer, new AnalyticsCollectionTransfer());
    }

    /**
     * @return array<string, list<\Generated\Shared\Transfer\AnalyticsRequestTransfer>>
     */
    protected function throwsExceptionWhenRequiredPropertiesAreNotSetDataProvider(): array
    {
        return [
            'When user is not set' => [new AnalyticsRequestTransfer()],
            'When user ID is not set' => [
                (new AnalyticsRequestTransfer())
                    ->setUser((new UserTransfer())->setQuicksightUser((new QuicksightUserTransfer())->setArn('arn'))),
            ],
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    protected function expandsCollectionWhenQuicksightUserExistsWithRoleDataProvider(): array
    {
        return [
            'Author role' => [static::QUICKSIGHT_USER_ROLE_AUTHOR],
            'Reader role' => [static::QUICKSIGHT_USER_ROLE_READER],
        ];
    }

    /**
     * @return array<string, list<mixed>>
     */
    protected function expandsCollectionWhenQuicksightAssetBundleImportJobIsInProgressGeneratesEmbedUrlDataProvider(): array
    {
        return [
            'Enabling status is in progress' => [
                false,
                false,
                true,
                static::ASSET_BUNDLE_IMPORT_JOB_STATUS_IN_PROGRESS,
                new QuicksightGenerateEmbedUrlResponseTransfer(),
            ],
            'Enabling status is successful' => [
                false,
                true,
                false,
                static::ASSET_BUNDLE_IMPORT_JOB_STATUS_SUCCESSFUL,
                (new QuicksightGenerateEmbedUrlResponseTransfer())->setEmbedUrl((new QuicksightEmbedUrlTransfer())->setUrl(static::EMBED_URL_TEST)),
            ],
            'Reset status is progress' => [
                true,
                true,
                true,
                static::ASSET_BUNDLE_IMPORT_JOB_STATUS_IN_PROGRESS,
                (new QuicksightGenerateEmbedUrlResponseTransfer())->setEmbedUrl((new QuicksightEmbedUrlTransfer())->setUrl(static::EMBED_URL_TEST)),
            ],
            'Reset status is successful' => [
                true,
                true,
                false,
                static::ASSET_BUNDLE_IMPORT_JOB_STATUS_SUCCESSFUL,
                (new QuicksightGenerateEmbedUrlResponseTransfer())->setEmbedUrl((new QuicksightEmbedUrlTransfer())->setUrl(static::EMBED_URL_TEST)),
            ],
        ];
    }

    /**
     * @return array<string, list<bool>>
     */
    protected function expandsCollectionWhenDescribeAssetBundleImportJobApiRequestFailsDataProvider(): array
    {
        return [
            'When QuickSightException is throws' => [true],
            'When JobStatus response key is missing' => [false],
        ];
    }

    /**
     * @return array<string, list<bool>>
     */
    protected function expandsCollectionWhenGenerateEmbedUrlApiRequestFailsDataProvider(): array
    {
        return [
            'When QuickSightException is throws' => [true],
            'When EmbedUrl response key is missing' => [false],
        ];
    }

    /**
     * @return array<string, list<mixed>>
     */
    protected function expandsCollectionWhenQuicksightAssetBundleImportJobFinishedDataProvider(): array
    {
        return [
            'Job successfully finished' => [
                true,
                static::ASSET_BUNDLE_IMPORT_JOB_STATUS_SUCCESSFUL,
                (new QuicksightGenerateEmbedUrlResponseTransfer())->setEmbedUrl((new QuicksightEmbedUrlTransfer())->setUrl(static::EMBED_URL_TEST)),
                (new QuicksightAssetBundleImportJobTransfer())
                    ->setStatus(static::ASSET_BUNDLE_IMPORT_JOB_STATUS_SUCCESSFUL)
                    ->setJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID)
                    ->setIsInitialized(true)
                    ->setErrors(new ArrayObject()),
                [],
            ],
            'Reset job finished with errors' => [
                true,
                static::ASSET_BUNDLE_IMPORT_JOB_STATUS_FAILED_ROLLBACK_COMPLETED,
                (new QuicksightGenerateEmbedUrlResponseTransfer())->setEmbedUrl((new QuicksightEmbedUrlTransfer())->setUrl(static::EMBED_URL_TEST)),
                (new QuicksightAssetBundleImportJobTransfer())
                    ->setStatus(static::ASSET_BUNDLE_IMPORT_JOB_STATUS_FAILED_ROLLBACK_COMPLETED)
                    ->setJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID)
                    ->setIsInitialized(true)
                    ->setErrors(new ArrayObject([
                        (new ErrorTransfer())
                            ->setMessage(static::ERROR_MESSAGE_DESCRIBE_ASSET_BUNDLE_IMPORT_JOB_FAILED)
                            ->setEntityIdentifier(null)
                            ->setParameters(null),
                    ])),
                [(new ErrorTransfer())->setMessage(static::ERROR_MESSAGE_DESCRIBE_ASSET_BUNDLE_IMPORT_JOB_FAILED)],
            ],
            'Enable job finished with errors' => [
                false,
                static::ASSET_BUNDLE_IMPORT_JOB_STATUS_FAILED_ROLLBACK_COMPLETED,
                new QuicksightGenerateEmbedUrlResponseTransfer(),
                (new QuicksightAssetBundleImportJobTransfer())
                    ->setStatus(static::ASSET_BUNDLE_IMPORT_JOB_STATUS_FAILED_ROLLBACK_COMPLETED)
                    ->setJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID)
                    ->setIsInitialized(false)
                    ->setErrors(new ArrayObject([
                        (new ErrorTransfer())
                            ->setMessage(static::ERROR_MESSAGE_DESCRIBE_ASSET_BUNDLE_IMPORT_JOB_FAILED)
                            ->setEntityIdentifier(null)
                            ->setParameters(null),
                    ])),
                [(new ErrorTransfer())->setMessage(static::ERROR_MESSAGE_DESCRIBE_ASSET_BUNDLE_IMPORT_JOB_FAILED)],
            ],
        ];
    }

    /**
     * @param bool $isAssetBundleSuccessfullyInitialized
     * @param bool $isAssetBundleInitializationInProgress
     * @param bool $isQuicksightUserRoleAvailable
     * @param \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlResponseTransfer $quicksightGenerateEmbedUrlResponseTransfer
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer|null $quicksightAssetBundleImportJobTransfer
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\Twig\Environment
     */
    protected function getTwigMock(
        bool $isAssetBundleSuccessfullyInitialized,
        bool $isAssetBundleInitializationInProgress,
        bool $isQuicksightUserRoleAvailable,
        QuicksightGenerateEmbedUrlResponseTransfer $quicksightGenerateEmbedUrlResponseTransfer,
        ?QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer = null
    ): Environment {
        $twigMock = $this->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()
            ->getMock();
        $twigMock->expects($this->atLeast(1))
            ->method('render')
            ->withConsecutive(
                [
                    $this->equalTo(static::TEMPLATE_PATH_QUICKSIGHT_ANALYTICS),
                    $this->equalTo([
                        'isAssetBundleSuccessfullyInitialized' => $isAssetBundleSuccessfullyInitialized,
                        'isAssetBundleInitializationInProgress' => $isAssetBundleInitializationInProgress,
                        'isQuicksightUserRoleAvailable' => $isQuicksightUserRoleAvailable,
                        'quicksightGenerateEmbedUrlResponse' => $quicksightGenerateEmbedUrlResponseTransfer,
                        'quicksightAssetBundleImportJob' => $quicksightAssetBundleImportJobTransfer,
                    ]),
                ],
                [
                    $this->equalTo(static::TEMPLATE_PATH_QUICKSIGHT_ANALYTICS_ACTIONS),
                    $this->equalTo([]),
                ],
            );

        return $twigMock;
    }

    /**
     * @param bool $isInitializedInitially
     * @param string $status
     * @param array $errorTransfers
     *
     * @return void
     */
    protected function haveQuicksightAssetBundleImportJob(
        bool $isInitializedInitially,
        string $status = self::ASSET_BUNDLE_IMPORT_JOB_STATUS_IN_PROGRESS,
        array $errorTransfers = []
    ): void {
        $this->tester->haveQuicksightAssetBundleImportJob([
            QuicksightAssetBundleImportJobTransfer::IS_INITIALIZED => $isInitializedInitially,
            QuicksightAssetBundleImportJobTransfer::STATUS => $status,
            QuicksightAssetBundleImportJobTransfer::JOB_ID => static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID,
        ], $errorTransfers);
    }
}
