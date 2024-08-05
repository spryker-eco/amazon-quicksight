<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business;

use Generated\Shared\Transfer\UserCollectionResponseTransfer;

interface AmazonQuicksightFacadeInterface
{
    /**
     * Specification:
     * - Expects `UserCollectionResponseTransfer.users.quicksightUser.role` to be set.
     * - Sends request to AWS API to register Quicksight users. For more information see {@link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_RegisterUser.html}.
     * - Adds errors to `UserCollectionResponseTransfer.errors` if Quicksight user registration failed.
     * - Persists successfully registered Quicksight users in the database.
     * - Returns `UserCollectionResponseTransfer` with updated `UserTransfers`.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UserCollectionResponseTransfer $userCollectionResponseTransfer
     *
     * @return \Generated\Shared\Transfer\UserCollectionResponseTransfer
     */
    public function createQuicksightUsersForUserTransfers(
        UserCollectionResponseTransfer $userCollectionResponseTransfer
    ): UserCollectionResponseTransfer;
}
