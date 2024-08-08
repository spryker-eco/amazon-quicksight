<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business;

use Generated\Shared\Transfer\QuicksightUserCollectionDeleteCriteriaTransfer;
use Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer;
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
        return $this->getFactory()->createUserExpander()->expandUserCollectionWithQuicksightUser($userCollectionTransfer);
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
     * @param \Generated\Shared\Transfer\QuicksightUserCollectionDeleteCriteriaTransfer $quicksightUserCollectionDeleteCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer
     */
    public function deleteQuicksightUserCollection(
        QuicksightUserCollectionDeleteCriteriaTransfer $quicksightUserCollectionDeleteCriteriaTransfer
    ): QuicksightUserCollectionResponseTransfer {
        return $this->getFactory()
            ->createQuicksightUserDeleter()
            ->deleteQuicksightUserCollection($quicksightUserCollectionDeleteCriteriaTransfer);
    }
}
