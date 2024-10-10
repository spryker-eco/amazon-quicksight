<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Communication\Plugin\User;

use Generated\Shared\Transfer\UserCollectionResponseTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\UserExtension\Dependency\Plugin\UserPostUpdatePluginInterface;

/**
 * @method \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig getConfig()
 * @method \SprykerEco\Zed\AmazonQuicksight\Business\AmazonQuicksightFacadeInterface getFacade()
 * @method \SprykerEco\Zed\AmazonQuicksight\Communication\AmazonQuicksightCommunicationFactory getFactory()
 */
class DeleteQuicksightUserPostUpdatePlugin extends AbstractPlugin implements UserPostUpdatePluginInterface
{
    /**
     * {@inheritDoc}
     * - Filters out users with statuses not applicable for deleting a Quicksight user.
     * - Uses {@link \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig::getUserStatusesApplicableForQuicksightUserDeletion()} to get a list of user statuses applicable for deleting a Quicksight user.
     * - Filters out users without persisted Quicksight user.
     * - Sends request to AWS API to delete Quicksight users. For more information see {@link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_DeleteUser.html}.
     * - If the AWS API call returns an error, the Quicksight user will not be deleted from persistence.
     * - Deletes from persistence Quicksight users that were successfully deleted from Quicksight.
     * - Adds errors to `UserCollectionResponseTransfer.errors` if any occurs.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UserCollectionResponseTransfer $userCollectionResponseTransfer
     *
     * @return \Generated\Shared\Transfer\UserCollectionResponseTransfer
     */
    public function postUpdate(UserCollectionResponseTransfer $userCollectionResponseTransfer): UserCollectionResponseTransfer
    {
        return $this->getFacade()->deleteQuicksightUsersByUserCollectionResponse($userCollectionResponseTransfer);
    }
}
