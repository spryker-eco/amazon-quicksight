<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business;

use Generated\Shared\Transfer\AnalyticsCollectionTransfer;
use Generated\Shared\Transfer\AnalyticsRequestTransfer;
use Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer;
use Generated\Shared\Transfer\EnableQuicksightAnalyticsResponseTransfer;
use Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer;
use Generated\Shared\Transfer\ResetQuicksightAnalyticsRequestTransfer;
use Generated\Shared\Transfer\ResetQuicksightAnalyticsResponseTransfer;
use Generated\Shared\Transfer\UserCollectionResponseTransfer;
use Generated\Shared\Transfer\UserCollectionTransfer;

interface AmazonQuicksightFacadeInterface
{
    /**
     * Specification:
     * - Requires `UserTransfer.idUser` for each user in `UserCollectionTransfer` to be set.
     * - Iterates over `UserCollectionTransfer.users`.
     * - Finds Quicksight users by `UserTransfer.idUser` in persistence.
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
     * - If the provided user is able to see the Analytics sends request to AWS API to generate an embed URL for a registered Quicksight user. For more information see {@link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_GenerateEmbedUrlForRegisteredUser.html}.
     * - Renders a Quicksight analytics template.
     * - Creates `AnalyticsTransfer` and populates `AnalyticsTransfer.content` with the rendered content.
     * - Adds the newly introduced `AnalyticsTransfer` to `AnalyticsCollectionTransfer.analyticsList`.
     * - Expands `AnalyticsCollectionTransfer.analyticsActions` with the synchronize Quicksight users action.
     * - If the provided user is allowed to reset the Analytics expands `AnalyticsCollectionTransfer.analyticsActions` with the reset action.
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
     * @deprecated Use {@link \SprykerEco\Zed\AmazonQuicksight\Business\AmazonQuicksightFacadeInterface::saveMatchedQuicksightUsers()} instead.
     *
     * @return \Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer
     */
    public function createMatchedQuicksightUsers(): QuicksightUserCollectionResponseTransfer;

    /**
     * Specification:
     * - Sends request to AWS API to get list of registered Quicksight users. For more information see {@link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_ListUsers.html}.
     * - Filters out Quicksight users with unsupported roles using {@link \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig::getQuicksightUserRoles()}.
     * - Fetches user transfers from persistence.
     * - Matches registered on Quicksight side Quicksight users with users from persistence by username.
     * - Persists matched Quicksight users.
     * - Adds errors to `QuicksightUserCollectionResponseTransfer.errors` if any occurs.
     * - Returns `QuicksightUserCollectionResponseTransfer` with persisted Quicksight users and errors if any occurs.
     *
     * @api
     *
     * @return \Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer
     */
    public function saveMatchedQuicksightUsers(): QuicksightUserCollectionResponseTransfer;

    /**
     * Specification:
     * - Requires `EnableQuicksightAnalyticsRequestTransfer.assetBundleImportJobId` to be set.
     * - Requires `EnableQuicksightAnalyticsRequestTransfer.user` to be set.
     * - Validates whether Analytics can be enabled and populates `EnableQuicksightAnalyticsResponseTransfer.errors` with errors encountered during the validation.
     * - If validation fails, Analytics will not be enabled and `EnableQuicksightAnalyticsResponseTransfer` will be returned.
     * - Starts the asset bundle import job for a file located at the path specified by {@link \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig::getAssetBundleImportFilePath()} to Quicksight.
     * - Throws {@link \SprykerEco\Zed\AmazonQuicksight\Business\Exception\AssetBundleImportFilePathNotDefinedException} if the path is not specified.
     * - Saves asset bundle import job to persistence and populates `EnableQuicksightAnalyticsResponseTransfer.quicksightAssetBundleImportJob` if the import job started successfully.
     * - Populates `EnableQuicksightAnalyticsResponseTransfer.errors` with errors encountered during the starting of the import job otherwise.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\EnableQuicksightAnalyticsResponseTransfer
     */
    public function enableAnalytics(
        EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer
    ): EnableQuicksightAnalyticsResponseTransfer;

    /**
     * Specification:
     * - Requires `ResetQuicksightAnalyticsRequestTransfer.assetBundleImportJobId` to be set.
     * - Requires `ResetQuicksightAnalyticsRequestTransfer.user` to be set.
     * - Validates whether Analytics can be reset and populates `ResetQuicksightAnalyticsResponseTransfer.errors` with errors encountered during the validation.
     * - If validation fails, Analytics will not be reset and `ResetQuicksightAnalyticsResponseTransfer` will be returned.
     * - Starts the asset bundle import job for a file located at the path specified by {@link \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig::getAssetBundleImportFilePath()} to Quicksight.
     * - Throws {@link \SprykerEco\Zed\AmazonQuicksight\Business\Exception\AssetBundleImportFilePathNotDefinedException} if the path is not specified.
     * - Updates asset bundle import job in persistence and populates `ResetQuicksightAnalyticsResponseTransfer.quicksightAssetBundleImportJob` if the import job started successfully.
     * - Populates `ResetQuicksightAnalyticsResponseTransfer.errors` with errors encountered during the starting of the import job otherwise.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ResetQuicksightAnalyticsRequestTransfer $resetQuicksightAnalyticsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ResetQuicksightAnalyticsResponseTransfer
     */
    public function resetAnalytics(
        ResetQuicksightAnalyticsRequestTransfer $resetQuicksightAnalyticsRequestTransfer
    ): ResetQuicksightAnalyticsResponseTransfer;
}
