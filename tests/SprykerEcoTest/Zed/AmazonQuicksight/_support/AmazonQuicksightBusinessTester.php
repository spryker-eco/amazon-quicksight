<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\AmazonQuicksight;

use Aws\QuickSight\Exception\QuickSightException;
use Aws\ResultInterface;
use Codeception\Actor;
use Codeception\Stub;
use Generated\Shared\DataBuilder\QuicksightUserBuilder;
use Generated\Shared\Transfer\AnalyticsRequestTransfer;
use Generated\Shared\Transfer\QuicksightUserTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightUser;
use Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightUserQuery;
use SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface;
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
     *
     * @return \Generated\Shared\Transfer\QuicksightUserTransfer
     */
    public function haveQuicksightUser(UserTransfer $userTransfer): QuicksightUserTransfer
    {
        $quicksightUserTransfer = (new QuicksightUserBuilder())->build();
        $quicksightUserTransfer->setFkUser($userTransfer->getIdUserOrFail());
        $quicksightUserEntity = (new SpyQuicksightUser())
            ->fromArray($quicksightUserTransfer->toArray());
        $quicksightUserEntity->save();

        $quicksightUserTransfer->setFkUser($quicksightUserEntity->getFkUser());

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
            ]))->build(),
        ]);
    }

    /**
     * @param \Aws\ResultInterface $result
     * @param string $methodName
     *
     * @return \SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface|\PHPUnit\Framework\MockObject\Stub
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
     * @return \SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface|\PHPUnit\Framework\MockObject\Stub
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
     * @return \Aws\QuickSight\Exception\QuickSightException|\PHPUnit\Framework\MockObject\Stub
     */
    public function getQuicksightExceptionMock(string $message): QuickSightException
    {
        $quicksightExceptionStub = Stub::makeEmpty(QuickSightException::class);
        $quicksightExceptionStub->method('getAwsErrorMessage')->willReturn($message);

        return $quicksightExceptionStub;
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface|\PHPUnit\Framework\MockObject\Stub
     */
    public function getAwsQuicksightClientMock(): AmazonQuicksightToAwsQuicksightClientInterface
    {
        return Stub::makeEmpty(AmazonQuicksightToAwsQuicksightClientInterface::class);
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface|\PHPUnit\Framework\MockObject\Stub
     */
    public function getAmazonQuicksightRepositoryMock(): AmazonQuicksightRepositoryInterface
    {
        $amazonQuicksightRepositoryStub = Stub::makeEmpty(AmazonQuicksightRepositoryInterface::class);
        $amazonQuicksightRepositoryStub->method('getQuicksightUsersByUserIds')
            ->willReturn([new QuicksightUserTransfer()]);

        return $amazonQuicksightRepositoryStub;
    }

    /**
     * @return \Generated\Shared\Transfer\AnalyticsRequestTransfer
     */
    public function haveAnalyticsRequestWithUser(): AnalyticsRequestTransfer
    {
        $userTransfer = $this->haveUser();
        $quicksightUserTransfer = $this->haveQuicksightUser($userTransfer);
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
     * @return \Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightUserQuery
     */
    protected function getQuicksightUserQuery(): SpyQuicksightUserQuery
    {
        return SpyQuicksightUserQuery::create();
    }
}
