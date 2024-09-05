<?php

/**
 * MIT License
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
use Spryker\Shared\Kernel\Transfer\Exception\NullValueException;
use Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightDependencyProvider;
use SprykerEco\Zed\AmazonQuicksight\Business\AmazonQuicksightBusinessFactory;
use SprykerEco\Zed\AmazonQuicksight\Business\AmazonQuicksightFacadeInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\FileContentLoader\AssetBundleImportFileContentLoader;
use SprykerEco\Zed\AmazonQuicksight\Business\FileContentLoader\AssetBundleImportFileContentLoaderInterface;
use SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManager;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepository;
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
     * @var string
     */
    protected const USER_STATUS_ACTIVE = 'active';

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
     * @dataProvider throwsExceptionWhenRequiredPropertiesAreNotSetDataProvider
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

        // Act
        $this->tester->getFacade()->enableAnalytics($enableQuicksightAnalyticsRequestTransfer);
    }

    /**
     * @return void
     */
    public function testThrowsNullValueExceptionWhenRequiredPropertiesAreNotSet(): void
    {
        // Arrange
        $enableQuicksightAnalyticsRequestTransfer = (new EnableQuicksightAnalyticsRequestTransfer())
            ->setUser(new UserTransfer())
            ->setAssetBundleImportJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID);

        // Assert
        $this->expectException(NullValueException::class);

        // Act
        $this->tester->getFacade()->enableAnalytics($enableQuicksightAnalyticsRequestTransfer);
    }

    /**
     * @dataProvider returnsResponseWithErrorWhenValidationFailsDataProvider
     *
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer
     *
     * @return void
     */
    public function testReturnsResponseWithErrorWhenValidationFails(
        QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer
    ): void {
        // Arrange
        $this->tester->haveQuicksightAssetBundleImportJob($quicksightAssetBundleImportJobTransfer->toArray());
        $enableQuicksightAnalyticsRequestTransfer = (new EnableQuicksightAnalyticsRequestTransfer())
            ->setUser(new UserTransfer())
            ->setAssetBundleImportJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID);

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
    public function testReturnsResponseWithErrorWhenUserCreationFails(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUser();
        $enableQuicksightAnalyticsRequestTransfer = (new EnableQuicksightAnalyticsRequestTransfer())
            ->setUser($userTransfer->setStatus(static::USER_STATUS_ACTIVE))
            ->setAssetBundleImportJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID);
        $this->tester->setDependency(
            AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT,
            $this->tester->getAwsQuicksightClientMockWithErrorResponse(
                $this->tester->getQuicksightExceptionMock(static::ERROR_MESSAGE_TEST),
                'registerUser',
            ),
        );

        // Act
        $enableQuicksightAnalyticsResponseTransfer = $this->tester
            ->getFacade()
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
    public function testReturnsResponseWithErrorWhenUserUpdateFails(): void
    {
        // Arrange
        $enableQuicksightAnalyticsRequestTransfer = $this->createEnableQuicksightAnalyticsRequestTransferWithUser(static::QUICKSIGHT_USER_ROLE_READER);
        $this->tester->setDependency(
            AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT,
            $this->tester->getAwsQuicksightClientMockWithErrorResponse(
                $this->tester->getQuicksightExceptionMock(static::ERROR_MESSAGE_TEST),
                'updateUser',
            ),
        );

        // Act
        $enableQuicksightAnalyticsResponseTransfer = $this->tester
            ->getFacade()
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
        $enableQuicksightAnalyticsResponseTransfer = $this->getFacadeWithMocks()
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
        $enableQuicksightAnalyticsResponseTransfer = $this->getFacadeWithMocks()
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
        $enableQuicksightAnalyticsResponseTransfer = $this->getFacadeWithMocks()
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
     * @return void
     */
    public function testCreatesNewQuicksightAuthorUser(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUser();
        $enableQuicksightAnalyticsRequestTransfer = (new EnableQuicksightAnalyticsRequestTransfer())
            ->setUser($userTransfer->setStatus(static::USER_STATUS_ACTIVE))
            ->setAssetBundleImportJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID);
        $awsQuicksightClientMock = $this->getAwsQuicksightClientMockWithExpectation(new Result(), 'startAssetBundleImportJob');
        $principalId = '123456789012';
        $arn = 'arn:aws:quicksight:eu-central-1:123456789012:user/default/' . $userTransfer->getUsername();
        $awsQuicksightClientMock->expects($this->once())->method('registerUser')->willReturn(new Result([
            'RequestId' => time(),
            'User' => [
                'Arn' => $arn,
                'PrincipalId' => $principalId,
                'Role' => static::QUICKSIGHT_USER_ROLE_AUTHOR,
            ],
        ]));
        $this->tester->setDependency(AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT, $awsQuicksightClientMock);

        // Act
        $enableQuicksightAnalyticsResponseTransfer = $this->getFacadeWithMocks()
            ->enableAnalytics($enableQuicksightAnalyticsRequestTransfer);

        // Assert
        $quicksightUserEntity = $this->tester->findQuicksightUserByIdUser($userTransfer->getIdUserOrFail());
        $this->assertNotNull($quicksightUserEntity);
        $this->assertSame(static::QUICKSIGHT_USER_ROLE_AUTHOR, $quicksightUserEntity->getRole());
        $this->assertSame($principalId, $quicksightUserEntity->getPrincipalId());
        $this->assertSame($arn, $quicksightUserEntity->getArn());
        $this->assertCount(0, $enableQuicksightAnalyticsResponseTransfer->getErrors());
        $this->assertNotNull($this->tester->findQuicksightAssetBundleImportJobQueryByJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID));
    }

    /**
     * @return void
     */
    public function testUpdatesQuicksightReaderUserToAuthor(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUser();
        $userTransfer->setQuicksightUser($this->tester->haveQuicksightUser($userTransfer, [
            QuicksightUserTransfer::ROLE => static::QUICKSIGHT_USER_ROLE_READER,
        ]));
        $enableQuicksightAnalyticsRequestTransfer = (new EnableQuicksightAnalyticsRequestTransfer())
            ->setUser($userTransfer->setStatus(static::USER_STATUS_ACTIVE))
            ->setAssetBundleImportJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID);

        $awsQuicksightClientMock = $this->getAwsQuicksightClientMockWithExpectation(new Result(), 'startAssetBundleImportJob');
        $principalId = '123456789012';
        $arn = 'arn:aws:quicksight:eu-central-1:123456789012:user/default/' . $userTransfer->getUsername();
        $awsQuicksightClientMock->expects($this->once())->method('updateUser')->willReturn(new Result([
            'RequestId' => time(),
            'User' => [
                'Arn' => $arn,
                'PrincipalId' => $principalId,
                'Role' => static::QUICKSIGHT_USER_ROLE_AUTHOR,
            ],
        ]));
        $this->tester->setDependency(AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT, $awsQuicksightClientMock);

        // Act
        $enableQuicksightAnalyticsResponseTransfer = $this->getFacadeWithMocks()
            ->enableAnalytics($enableQuicksightAnalyticsRequestTransfer);

        // Assert
        $quicksightUserEntity = $this->tester->findQuicksightUserByIdUser($userTransfer->getIdUserOrFail());
        $this->assertNotNull($quicksightUserEntity);
        $this->assertSame(static::QUICKSIGHT_USER_ROLE_AUTHOR, $quicksightUserEntity->getRole());
        $this->assertSame($principalId, $quicksightUserEntity->getPrincipalId());
        $this->assertSame($arn, $quicksightUserEntity->getArn());
        $this->assertCount(0, $enableQuicksightAnalyticsResponseTransfer->getErrors());
        $this->assertNotNull($this->tester->findQuicksightAssetBundleImportJobQueryByJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID));
    }

    /**
     * @return array<string, list<\Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer>>
     */
    protected function throwsExceptionWhenRequiredPropertiesAreNotSetDataProvider(): array
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
     * @return array<string, list<\Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer>>
     */
    protected function returnsResponseWithErrorWhenValidationFailsDataProvider(): array
    {
        return [
            'Asset bundle is initialized' => [
                (new QuicksightAssetBundleImportJobTransfer())
                    ->setIsInitialized(true)
                    ->setJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID)
                    ->setStatus(static::ASSET_BUNDLE_IMPORT_JOB_STATUS_SUCCESSFUL),
            ],
            'Asset bundle initialization is in progress' => [
                (new QuicksightAssetBundleImportJobTransfer())
                    ->setIsInitialized(false)
                    ->setJobId(static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID)
                    ->setStatus(static::ASSET_BUNDLE_IMPORT_JOB_STATUS_IN_PROGRESS),
            ],
        ];
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\AmazonQuicksightFacadeInterface
     */
    protected function getFacadeWithMocks(): AmazonQuicksightFacadeInterface
    {
        $amazonQuicksightBusinessFactoryMock = $this->createAmazonQuicksightBusinessFactoryMock();

        return $this->tester->getFacade()->setFactory($amazonQuicksightBusinessFactoryMock);
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\AmazonQuicksightBusinessFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function createAmazonQuicksightBusinessFactoryMock(): AmazonQuicksightBusinessFactory
    {
        $amazonQuicksightBusinessFactoryMock = $this->getMockBuilder(AmazonQuicksightBusinessFactory::class)
            ->onlyMethods(['createAssetBundleImportFileContentLoader', 'resolveDependencyProvider'])
            ->getMock();
        $amazonQuicksightBusinessFactoryMock->method('createAssetBundleImportFileContentLoader')
            ->willReturn($this->createAssetBundleImportFileContentLoaderMock());
        $amazonQuicksightBusinessFactoryMock->method('resolveDependencyProvider')
            ->willReturn(new AmazonQuicksightDependencyProvider());
        $amazonQuicksightBusinessFactoryMock->setConfig(new AmazonQuicksightConfig());
        $amazonQuicksightBusinessFactoryMock->setRepository(new AmazonQuicksightRepository());
        $amazonQuicksightBusinessFactoryMock->setEntityManager(new AmazonQuicksightEntityManager());

        return $amazonQuicksightBusinessFactoryMock;
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\FileContentLoader\AssetBundleImportFileContentLoaderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function createAssetBundleImportFileContentLoaderMock(): AssetBundleImportFileContentLoaderInterface
    {
        $assetBundleEnablerMock = $this->getMockBuilder(AssetBundleImportFileContentLoader::class)
            ->disableOriginalConstructor()
            ->getMock();
        $assetBundleEnablerMock->method('getAssetBundleImportFileContent')->willReturn('content');

        return $assetBundleEnablerMock;
    }

    /**
     * @param string $quicksightUserRole
     *
     * @return \Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer
     */
    protected function createEnableQuicksightAnalyticsRequestTransferWithUser(
        string $quicksightUserRole = self::QUICKSIGHT_USER_ROLE_AUTHOR
    ): EnableQuicksightAnalyticsRequestTransfer {
        $userTransfer = $this->tester->haveUserWithNotPersistedQuicksightUserRole($quicksightUserRole);

        return (new EnableQuicksightAnalyticsRequestTransfer())
            ->setUser($userTransfer->setStatus(static::USER_STATUS_ACTIVE))
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
