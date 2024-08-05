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
    public function expandUserCollectionWithQuicksightUser(
        UserCollectionTransfer $userCollectionTransfer
    ): UserCollectionTransfer;
}
