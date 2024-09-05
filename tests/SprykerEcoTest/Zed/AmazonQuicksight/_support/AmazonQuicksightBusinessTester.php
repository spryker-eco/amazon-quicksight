<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\AmazonQuicksight;

use ArrayObject;
use Aws\QuickSight\Exception\QuickSightException;
use Aws\ResultInterface;
use Codeception\Actor;
use Codeception\Stub;
use Generated\Shared\DataBuilder\QuicksightAssetBundleImportJobBuilder;
use Generated\Shared\DataBuilder\QuicksightUserBuilder;
use Generated\Shared\Transfer\AnalyticsRequestTransfer;
use Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer;
use Generated\Shared\Transfer\QuicksightUserTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightAssetBundleImportJob;
use Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightAssetBundleImportJobQuery;
use Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightUser;
use Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightUserQuery;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightDependencyProvider;
use SprykerEco\Zed\AmazonQuicksight\Business\AmazonQuicksightBusinessFactory;
use SprykerEco\Zed\AmazonQuicksight\Business\AmazonQuicksightFacadeInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\FileContentLoader\AssetBundleImportFileContentLoader;
use SprykerEco\Zed\AmazonQuicksight\Business\FileContentLoader\AssetBundleImportFileContentLoaderInterface;
use SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManager;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepository;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface;

/**
 * Inherited Methods
 *
 * @method void wantTo($text)
 * @method void wantToTest($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 * @method \SprykerEco\Zed\AmazonQuicksight\Business\AmazonQuicksightFacadeInterface getFacade(?string $moduleName = NULL)
 *
 * @SuppressWarnings(PHPMD)
 */
class AmazonQuicksightBusinessTester extends Actor
{
    use _generated\AmazonQuicksightBusinessTesterActions;

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     * @param list<mixed> $seedData
     *
     * @return \Generated\Shared\Transfer\QuicksightUserTransfer
     */
    public function haveQuicksightUser(UserTransfer $userTransfer, array $seedData = []): QuicksightUserTransfer
    {
        $quicksightUserTransfer = (new QuicksightUserBuilder($seedData))->build();
        $quicksightUserTransfer->setFkUser($userTransfer->getIdUserOrFail());
        $quicksightUserEntity = (new SpyQuicksightUser())
            ->fromArray($quicksightUserTransfer->toArray());
        $quicksightUserEntity->save();

        $quicksightUserTransfer->setFkUser($quicksightUserEntity->getFkUser());
        $quicksightUserTransfer->setIdQuicksightUser($quicksightUserEntity->getIdQuicksightUser());

        return $quicksightUserTransfer;
    }

    /**
     * @param string $quicksightUserRole
     *
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    public function haveUserWithNotPersistedQuicksightUserRole(string $quicksightUserRole): UserTransfer
    {
        return $this->haveUser([
            UserTransfer::QUICKSIGHT_USER => (new QuicksightUserBuilder([
                QuicksightUserTransfer::ROLE => $quicksightUserRole,
                QuicksightUserTransfer::ARN => null,
                QuicksightUserTransfer::PRINCIPAL_ID => null,
                QuicksightUserTransfer::UUID => null,
            ]))->build(),
        ]);
    }

    /**
     * @param \Aws\ResultInterface $result
     * @param string $methodName
     *
     * @return \SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    public function getAwsQuicksightClientMockWithSuccessfulResponse(
        ResultInterface $result,
        string $methodName
    ): AmazonQuicksightToAwsQuicksightClientInterface {
        $awsQuicksightClientMock = $this->getAwsQuicksightClientMock();
        $awsQuicksightClientMock->method($methodName)->willReturn($result);

        return $awsQuicksightClientMock;
    }

    /**
     * @param \Aws\QuickSight\Exception\QuickSightException $quickSightException
     * @param string $methodName
     *
     * @return \SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    public function getAwsQuicksightClientMockWithErrorResponse(
        QuickSightException $quickSightException,
        string $methodName
    ): AmazonQuicksightToAwsQuicksightClientInterface {
        $awsQuicksightClientMock = $this->getAwsQuicksightClientMock();
        $awsQuicksightClientMock->method($methodName)->willThrowException($quickSightException);

        return $awsQuicksightClientMock;
    }

    /**
     * @param string $message
     *
     * @return \Aws\QuickSight\Exception\QuickSightException|\PHPUnit\Framework\MockObject\MockObject
     */
    public function getQuicksightExceptionMock(string $message): QuickSightException
    {
        $quicksightExceptionStub = Stub::makeEmpty(QuickSightException::class);
        $quicksightExceptionStub->method('getAwsErrorMessage')->willReturn($message);

        return $quicksightExceptionStub;
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    public function getAwsQuicksightClientMock(): AmazonQuicksightToAwsQuicksightClientInterface
    {
        return Stub::makeEmpty(AmazonQuicksightToAwsQuicksightClientInterface::class);
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    public function getAmazonQuicksightRepositoryMock(): AmazonQuicksightRepositoryInterface
    {
        $amazonQuicksightRepositoryStub = Stub::makeEmpty(AmazonQuicksightRepositoryInterface::class);
        $amazonQuicksightRepositoryStub->method('getQuicksightUsersByUserIds')
            ->willReturn([(new QuicksightUserTransfer())->setArn('arn')]);

        return $amazonQuicksightRepositoryStub;
    }

    /**
     * @return \Generated\Shared\Transfer\AnalyticsRequestTransfer
     */
    public function haveAnalyticsRequestWithUser(): AnalyticsRequestTransfer
    {
        $userTransfer = $this->haveUser();

        return (new AnalyticsRequestTransfer())->setUser($userTransfer);
    }

    /**
     * @param string|null $quicksightUserRole
     *
     * @return \Generated\Shared\Transfer\AnalyticsRequestTransfer
     */
    public function haveAnalyticsRequestWithQuicksightUser(?string $quicksightUserRole = null): AnalyticsRequestTransfer
    {
        $userTransfer = $this->haveUser();
        $quicksightUserSeedData = $quicksightUserRole ? ['role' => $quicksightUserRole] : [];
        $quicksightUserTransfer = $this->haveQuicksightUser($userTransfer, $quicksightUserSeedData);
        $userTransfer->setQuicksightUser($quicksightUserTransfer);

        return (new AnalyticsRequestTransfer())->setUser($userTransfer);
    }

    /**
     * @param int $idUser
     *
     * @return \Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightUser|null
     */
    public function findQuicksightUserByIdUser(int $idUser): ?SpyQuicksightUser
    {
        return $this->getQuicksightUserQuery()
            ->filterByFkUser($idUser)
            ->findOne();
    }

    /**
     * @param string $arn
     *
     * @return \Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightUser|null
     */
    public function findQuicksightUserByArn(string $arn): ?SpyQuicksightUser
    {
        return $this->getQuicksightUserQuery()
            ->filterByArn($arn)
            ->findOne();
    }

    /**
     * @return void
     */
    public function ensureQuicksightUserTableIsEmpty(): void
    {
        $this->ensureDatabaseTableIsEmpty($this->getQuicksightUserQuery());
    }

    /**
     * @return void
     */
    public function ensureQuicksightAssetBundleImportJobTableIsEmpty(): void
    {
        $this->ensureDatabaseTableIsEmpty($this->getQuicksightUserQuery());
    }

    /**
     * @param array<string, mixed> $seedData
     * @param list<\Generated\Shared\Transfer\ErrorTransfer> $errorTransfers
     *
     * @return \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer
     */
    public function haveQuicksightAssetBundleImportJob(array $seedData, array $errorTransfers = []): QuicksightAssetBundleImportJobTransfer
    {
        $quicksightAssetBundleImportJobTransfer = (new QuicksightAssetBundleImportJobBuilder($seedData))->build();
        $quicksightAssetBundleImportJobTransfer->setErrors(new ArrayObject($errorTransfers));
        $quicksightAssetBundleImportJobData = $quicksightAssetBundleImportJobTransfer->toArray();
        $quicksightAssetBundleImportJobData[QuicksightAssetBundleImportJobTransfer::ERRORS] = json_encode(
            $quicksightAssetBundleImportJobData[QuicksightAssetBundleImportJobTransfer::ERRORS],
        );
        $quicksightAssetBundleImportJobEntity = (new SpyQuicksightAssetBundleImportJob())->fromArray($quicksightAssetBundleImportJobData);
        $quicksightAssetBundleImportJobEntity->save();

        return $quicksightAssetBundleImportJobTransfer;
    }

    /**
     * @param string $jobId
     *
     * @return \Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightAssetBundleImportJob|null
     */
    public function findQuicksightAssetBundleImportJobQueryByJobId(string $jobId): ?SpyQuicksightAssetBundleImportJob
    {
        return $this->getQuicksightAssetBundleImportJobQuery()->filterByJobId($jobId)->findOne();
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\AmazonQuicksightFacadeInterface
     */
    public function getFacadeWithMocks(): AmazonQuicksightFacadeInterface
    {
        $amazonQuicksightBusinessFactoryMock = $this->createAmazonQuicksightBusinessFactoryMock();

        return $this->getFacade()->setFactory($amazonQuicksightBusinessFactoryMock);
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\AmazonQuicksightBusinessFactory
     */
    protected function createAmazonQuicksightBusinessFactoryMock(): AmazonQuicksightBusinessFactory
    {
        $amazonQuicksightBusinessFactoryStub = Stub::make(AmazonQuicksightBusinessFactory::class, [
            'createAssetBundleImportFileContentLoader' => $this->createAssetBundleImportFileContentLoaderMock(),
            'resolveDependencyProvider' => new AmazonQuicksightDependencyProvider(),
            'config' => new AmazonQuicksightConfig(),
            'repository' => new AmazonQuicksightRepository(),
            'entityManager' => new AmazonQuicksightEntityManager(),
        ]);

        return $amazonQuicksightBusinessFactoryStub;
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\FileContentLoader\AssetBundleImportFileContentLoaderInterface
     */
    protected function createAssetBundleImportFileContentLoaderMock(): AssetBundleImportFileContentLoaderInterface
    {
        return Stub::makeEmpty(AssetBundleImportFileContentLoader::class, [
            'getAssetBundleImportFileContent' => 'content',
        ]);
    }

    /**
     * @return \Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightUserQuery
     */
    protected function getQuicksightUserQuery(): SpyQuicksightUserQuery
    {
        return SpyQuicksightUserQuery::create();
    }

    /**
     * @return \Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightAssetBundleImportJobQuery
     */
    protected function getQuicksightAssetBundleImportJobQuery(): SpyQuicksightAssetBundleImportJobQuery
    {
        return SpyQuicksightAssetBundleImportJobQuery::create();
    }
}
