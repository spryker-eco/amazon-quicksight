<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Updater;

use Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer;

interface QuicksightAssetBundleImportJobUpdaterInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer
     */
    public function resetDefaultQuicksightAssetBundleImportJob(
        QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer
    ): QuicksightAssetBundleImportJobTransfer;
}
