<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Creator;

use Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer;
use Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer;

interface QuicksightAssetBundleImportJobCreatorInterface
{
    /**
     * @param \Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer
     */
    public function createDefaultAssetBundleImportJobByEnableQuicksightAnalyticsRequest(
        EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer
    ): QuicksightAssetBundleImportJobTransfer;
}
