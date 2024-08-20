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
     * - Filters out users if `UserTransfer.quicksightUser.role` is not set.
     * - Filters out users with already persisted Quicksight user.
     * - Filters out users with statuses not applicable for registering a Quicksight user.
     * - Uses {@link \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig::getUserStatusesApplicableForQuicksightUserRegistration()} to get a list of user statuses applicable for registering a Quicksight user.
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
    public function createQuicksightUsersByUserCollectionResponse(
        UserCollectionResponseTransfer $userCollectionResponseTransfer
    ): UserCollectionResponseTransfer;

    /**
     * Specification:
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
    public function deleteQuicksightUsersByUserCollectionResponse(
        UserCollectionResponseTransfer $userCollectionResponseTransfer
    ): UserCollectionResponseTransfer;

    /**
     * Specification:
     * - Requires `AnalyticsRequestTransfer.user` to be set.
     * - Requires `AnalyticsRequestTransfer.user.idUser` to be set.
     * - If Quicksight user with the provided user ID does not exist in DB returns `AnalyticsCollectionTransfer` without any changes.
     * - Otherwise sends request to AWS API to generate an embed URL for a registered Quicksight user. For more information see {@link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_GenerateEmbedUrlForRegisteredUser.html}.
     * - Renders a Quicksight analytics template with the generated embed URL.
     * - Creates `AnalyticsTransfer` and populates `AnalyticsTransfer.content` with the rendered content.
     * - Adds the newly introduced `AnalyticsTransfer` to `AnalyticsCollectionTransfer.analyticsList`.
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
    ): AnalyticsCollectionTransfer;

    /**
     * Specification:
     * - Sends request to AWS API to get list of registered Quicksight users. For more information see {@link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_ListUsers.html}.
     * - Filters out Quicksight users with unsupported roles using {@link \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig::getQuicksightUserRoles()}.
     * - Fetches user transfers from persistence.
     * - Filters out user transfers with persisted Quicksight user.
     * - Matches registered on Quicksight side Quicksight users with users from persistence by username.
     * - Persists matched Quicksight users.
     * - Adds errors to `QuicksightUserCollectionResponseTransfer.errors` if any occurs.
     * - Returns `QuicksightUserCollectionResponseTransfer` with persisted Quicksight users and errors if any occurs.
     *
     * @api
     *
     * @return \Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer
     */
    public function createMatchedQuicksightUsers(): QuicksightUserCollectionResponseTransfer;

    /**
     * Specification:
     * - Sends request to AWS API to get list of registered Quicksight users. For more information see {@link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_ListUsers.html}.
     * - Filters out Quicksight users with unsupported roles using {@link \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig::getQuicksightUserRoles()}.
     * - Fetches user transfers from persistence.
     * - Finds registered on Quicksight side Quicksight users not matched with users from persistence.
     * - Sends request to AWS API to delete Quicksight users. For more information see {@link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_DeleteUserByPrincipalId.html}.
     * - Adds errors to `QuicksightUserCollectionResponseTransfer.errors` if any occurs.
     * - Returns `QuicksightUserCollectionResponseTransfer` with deleted Quicksight users and errors if any occurs.
     *
     * @api
     *
     * @return \Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer
     */
    public function deleteNotMatchedQuicksightUsers(): QuicksightUserCollectionResponseTransfer;

    /**
     * Specification:
     * - TODO
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobCriteriaTransfer $quicksightAssetBundleImportJobCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightAssetBundleImportJobCollectionTransfer
     */
    public function getQuicksightAssetBundleImportJobCollection(
        QuicksightAssetBundleImportJobCriteriaTransfer $quicksightAssetBundleImportJobCriteriaTransfer
    ): QuicksightAssetBundleImportJobCollectionTransfer;
}
