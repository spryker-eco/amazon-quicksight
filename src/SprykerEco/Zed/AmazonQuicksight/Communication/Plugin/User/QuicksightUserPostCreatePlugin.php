<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Communication\Plugin\User;

use Generated\Shared\Transfer\UserCollectionResponseTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\UserExtension\Dependency\Plugin\UserPostCreatePluginInterface;

/**
 * @method \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig getConfig()
 * @method \SprykerEco\Zed\AmazonQuicksight\Business\AmazonQuicksightFacadeInterface getFacade()
 * @method \SprykerEco\Zed\AmazonQuicksight\Communication\AmazonQuicksightCommunicationFactory getFactory()
 */
class QuicksightUserPostCreatePlugin extends AbstractPlugin implements UserPostCreatePluginInterface
{
    /**
     * {@inheritDoc}
     * - Expects `UserCollectionResponseTransfer.users.quicksightUser.role` to be set.
     * - Filters out users if `UserTransfer.quicksightUser.role` is not set.
     * - Filters out users with already persisted Quicksight user.
     * - Filters out users with statuses not applicable for registering a Quicksight user.
     * - Uses {@link \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig::getUserStatusesApplicableForQuicksightUserRegistration()} to get a list of user statuses applicable for registering a Quicksight user.
     * - Sends request to AWS API to register Quicksight users. For more information see {@link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_RegisterUser.html}.
     * - Adds errors to `UserCollectionResponseTransfer.errors` if Quicksight user registration failed.
     * - Persists successfully registered Quicksight users to persistence.
     * - Returns `UserCollectionResponseTransfer` with updated `UserTransfers`.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UserCollectionResponseTransfer $userCollectionResponseTransfer
     *
     * @return \Generated\Shared\Transfer\UserCollectionResponseTransfer
     */
    public function postCreate(UserCollectionResponseTransfer $userCollectionResponseTransfer): UserCollectionResponseTransfer
    {
        return $this->getFacade()->createQuicksightUsersByUserCollectionResponse($userCollectionResponseTransfer);
    }
}
