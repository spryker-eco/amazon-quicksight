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

    /**
     * Specification:
     * - Expects `UserCollectionResponseTransfer.users.quicksightUser.role` to be set.
     * - Does nothing if `UserTransfer.quicksightUser.role` is not set.
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
    public function createQuicksightUsersForUserCollectionResponse(
        UserCollectionResponseTransfer $userCollectionResponseTransfer
    ): UserCollectionResponseTransfer;

    /**
     * Specification:
     * - Expects `QuicksightUserCollectionDeleteCriteriaTransfer.quicksightUserIds` to be not empty.
     * - Does nothing if `QuicksightUserCollectionDeleteCriteriaTransfer.quicksightUserIds` is empty.
     * - Fetches quicksight users from persistence by provided `QuicksightUserCollectionDeleteCriteriaTransfer.quicksightUserIds`.
     * - Does nothing if no quicksight users are found for provided `QuicksightUserCollectionDeleteCriteriaTransfer.quicksightUserIds`.
     * - Sends request to AWS API to delete Quicksight users. For more information see {@link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_DeleteUser.html}.
     * - If AWS API call returns error it is added to `QuicksightUserCollectionResponseTransfer.errors` with `idQuicksightUser` as entity identifier.
     * - If AWS API call returns error, quicksight user will not be deleted from persistence.
     * - Deletes persistence quicksight users that were successfully deleted from Quicksight.
     * - Adds quicksight users to `QuicksightUserCollectionResponseTransfer.quicksightUsers`.
     * - Returns `QuicksightUserCollectionResponseTransfer` with quicksight users and errors if any occurs.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuicksightUserCollectionDeleteCriteriaTransfer $quicksightUserCollectionDeleteCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer
     */
    public function deleteQuicksightUserCollection(
        QuicksightUserCollectionDeleteCriteriaTransfer $quicksightUserCollectionDeleteCriteriaTransfer
    ): QuicksightUserCollectionResponseTransfer;
}
