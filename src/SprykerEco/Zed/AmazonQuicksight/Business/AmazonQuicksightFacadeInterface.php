<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business;

use Generated\Shared\Transfer\UserCollectionTransfer;

interface AmazonQuicksightFacadeInterface
{
    /**
     * Specification:
     * - Requires `UserTransfer.idUser` for each user in `UserCollectionTransfer` to be set.
     * - Iterates over `UserCollectionTransfer.users`.
     * - Finds Quicksight users by `UserTransfer.idUser` in DB.
     * - Populates `UserTransfer.quicksightUser` in collection with found Quicksight users.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\UserCollectionTransfer $userCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\UserCollectionTransfer
     */
    public function expandUserCollectionWithQuicksightUsers(
        UserCollectionTransfer $userCollectionTransfer
    ): UserCollectionTransfer;
}
