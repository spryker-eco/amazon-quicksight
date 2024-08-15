<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business;

use Generated\Shared\Transfer\AnalyticsCollectionTransfer;
use Generated\Shared\Transfer\AnalyticsRequestTransfer;
use Generated\Shared\Transfer\QuicksightAssetBundleImportJobCollectionTransfer;
use Generated\Shared\Transfer\QuicksightAssetBundleImportJobCriteriaTransfer;
use Generated\Shared\Transfer\UserCollectionResponseTransfer;
use Generated\Shared\Transfer\UserCollectionTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \SprykerEco\Zed\AmazonQuicksight\Business\AmazonQuicksightBusinessFactory getFactory()
 * @method \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface getRepository()
 * @method \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface getEntityManager()
 */
class AmazonQuicksightFacade extends AbstractFacade implements AmazonQuicksightFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UserCollectionTransfer $userCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\UserCollectionTransfer
     */
    public function expandUserCollectionWithQuicksightUsers(
        UserCollectionTransfer $userCollectionTransfer
    ): UserCollectionTransfer {
        return $this->getFactory()->createUserExpander()->expandUserCollectionWithQuicksightUsers($userCollectionTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UserCollectionResponseTransfer $userCollectionResponseTransfer
     *
     * @return \Generated\Shared\Transfer\UserCollectionResponseTransfer
     */
    public function createQuicksightUsersForUserCollectionResponse(
        UserCollectionResponseTransfer $userCollectionResponseTransfer
    ): UserCollectionResponseTransfer {
        return $this->getFactory()
            ->createQuicksightUserCreator()
            ->createQuicksightUsersForUserCollectionResponse($userCollectionResponseTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AnalyticsRequestTransfer $analyticsRequestTransfer
     * @param \Generated\Shared\Transfer\AnalyticsCollectionTransfer $analyticsCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\AnalyticsCollectionTransfer
     */
    public function expandAnalyticsCollectionWithQuicksightAnalytics(
        AnalyticsRequestTransfer $analyticsRequestTransfer,
        AnalyticsCollectionTransfer $analyticsCollectionTransfer
    ): AnalyticsCollectionTransfer {
        return $this->getFactory()
            ->createAnalyticsExpander()
            ->expandAnalyticsCollectionWithQuicksightAnalytics($analyticsRequestTransfer, $analyticsCollectionTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobCriteriaTransfer $quicksightAssetBundleImportJobCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightAssetBundleImportJobCollectionTransfer
     */
    public function getQuicksightAssetBundleImportJobCollection(
        QuicksightAssetBundleImportJobCriteriaTransfer $quicksightAssetBundleImportJobCriteriaTransfer
    ): QuicksightAssetBundleImportJobCollectionTransfer {
        return $this->getRepository()
            ->getQuicksightAssetBundleImportJobCollection($quicksightAssetBundleImportJobCriteriaTransfer);
    }
}
