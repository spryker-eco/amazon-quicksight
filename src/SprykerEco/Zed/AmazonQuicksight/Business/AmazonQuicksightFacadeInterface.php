<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business;

use Generated\Shared\Transfer\AnalyticsEmbedUrlRequestTransfer;
use Generated\Shared\Transfer\AnalyticsEmbedUrlResponseTransfer;
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

    /**
     * Specification:
     * - Requires `AnalyticsEmbedUrlRequestTransfer.user` and `AnalyticsEmbedUrlRequestTransfer.user.idUser` to be set.
     * - Returns true if Quicksight user with the provided user ID exists in DB.
     * - Returns false otherwise.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AnalyticsEmbedUrlRequestTransfer $analyticsEmbedUrlRequestTransfer
     *
     * @return bool
     */
    public function isQuicksightAnalyticsEmbedUrlProviderApplicable(
        AnalyticsEmbedUrlRequestTransfer $analyticsEmbedUrlRequestTransfer
    ): bool;

    /**
     * Specification:
     * - Requires `AnalyticsEmbedUrlRequestTransfer.user`, `AnalyticsEmbedUrlRequestTransfer.user.quicksightUser`
     * and `AnalyticsEmbedUrlRequestTransfer.user.quicksightUser.arn` to be set.
     * - Sends request to AWS API to generate an embed URL for a registered Quicksight user. For more information see {@link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_GenerateEmbedUrlForRegisteredUser.html}.
     * - Adds errors to `AnalyticsEmbedUrlResponseTransfer.errors` if Quicksight embed URL generation failed.
     * - Populates `AnalyticsEmbedUrlResponseTransfer.embedUrl.url` with the generated embed URL.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AnalyticsEmbedUrlRequestTransfer $analyticsEmbedUrlRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AnalyticsEmbedUrlResponseTransfer
     */
    public function getAnalyticsEmbedUrl(
        AnalyticsEmbedUrlRequestTransfer $analyticsEmbedUrlRequestTransfer
    ): AnalyticsEmbedUrlResponseTransfer;
}
