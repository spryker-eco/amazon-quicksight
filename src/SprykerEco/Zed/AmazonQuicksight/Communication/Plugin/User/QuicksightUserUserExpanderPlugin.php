<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Communication\Plugin\User;

use Generated\Shared\Transfer\UserCollectionTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\UserExtension\Dependency\Plugin\UserExpanderPluginInterface;

/**
 * @method \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig getConfig()
 * @method \SprykerEco\Zed\AmazonQuicksight\Business\AmazonQuicksightFacadeInterface getFacade()
 * @method \SprykerEco\Zed\AmazonQuicksight\Communication\AmazonQuicksightCommunicationFactory getFactory()
 */
class QuicksightUserUserExpanderPlugin extends AbstractPlugin implements UserExpanderPluginInterface
{
    /**
     * {@inheritDoc}
     * - Iterates over `UserCollectionTransfer.users`.
     * - Requires `UserTransfer.idUser` for each user in collection to be set.
     * - Finds Quicksight users by `UserTransfer.idUser` in DB.
     * - Populates `UserTransfer.quicksightUser` in collection with found Quicksight users.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UserCollectionTransfer $userCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\UserCollectionTransfer
     */
    public function expand(UserCollectionTransfer $userCollectionTransfer): UserCollectionTransfer
    {
        return $this->getFacade()->expandUserCollectionWithQuicksightUser($userCollectionTransfer);
    }
}
