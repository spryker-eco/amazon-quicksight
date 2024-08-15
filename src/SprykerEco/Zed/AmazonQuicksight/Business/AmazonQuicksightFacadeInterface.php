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
