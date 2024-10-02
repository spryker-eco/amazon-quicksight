<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Validator;

use Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer;
use Generated\Shared\Transfer\EnableQuicksightAnalyticsResponseTransfer;
use Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer;
use Generated\Shared\Transfer\QuicksightUserTransfer;
use Generated\Shared\Transfer\ResetQuicksightAnalyticsRequestTransfer;
use Generated\Shared\Transfer\ResetQuicksightAnalyticsResponseTransfer;

interface QuicksightAnalyticsRequestValidatorInterface
{
    /**
     * @param \Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer
     * @param \Generated\Shared\Transfer\EnableQuicksightAnalyticsResponseTransfer $enableQuicksightAnalyticsResponseTransfer
     *
     * @return \Generated\Shared\Transfer\EnableQuicksightAnalyticsResponseTransfer
     */
    public function validateEnableQuicksightAnalyticsRequest(
        EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer,
        EnableQuicksightAnalyticsResponseTransfer $enableQuicksightAnalyticsResponseTransfer,
    ): EnableQuicksightAnalyticsResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\ResetQuicksightAnalyticsRequestTransfer $resetQuicksightAnalyticsRequestTransfer
     * @param \Generated\Shared\Transfer\ResetQuicksightAnalyticsResponseTransfer $resetQuicksightAnalyticsResponseTransfer
     *
     * @return \Generated\Shared\Transfer\ResetQuicksightAnalyticsResponseTransfer
     */
    public function validateResetQuicksightAnalyticsRequest(
        ResetQuicksightAnalyticsRequestTransfer $resetQuicksightAnalyticsRequestTransfer,
        ResetQuicksightAnalyticsResponseTransfer $resetQuicksightAnalyticsResponseTransfer,
    ): ResetQuicksightAnalyticsResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer|null $quicksightAssetBundleImportJobTransfer
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer|null $quicksightUserTransfer
     *
     * @return bool
     */
    public function isResetAnalyticsEnabled(
        ?QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer,
        ?QuicksightUserTransfer $quicksightUserTransfer,
    ): bool;

    /**
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer|null $quicksightAssetBundleImportJobTransfer
     *
     * @return bool
     */
    public function isEnableAnalyticsEnabled(
        ?QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer,
    ): bool;

    /**
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer|null $quicksightAssetBundleImportJobTransfer
     *
     * @return bool
     */
    public function isAssetBundleSuccessfullyInitialized(
        ?QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer,
    ): bool;

    /**
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer|null $quicksightAssetBundleImportJobTransfer
     *
     * @return bool
     */
    public function isAssetBundleInitializationInProgress(
        ?QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer,
    ): bool;

    /**
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer|null $quicksightUserTransfer
     *
     * @return bool
     */
    public function isQuicksightUserRoleAvailable(?QuicksightUserTransfer $quicksightUserTransfer): bool;
}
