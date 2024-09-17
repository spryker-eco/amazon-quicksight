<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Synchronizer;

use Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer;

interface QuicksightAssetBundleImportJobSynchronizerInterface
{
    /**
     * @return \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer|null
     */
    public function findSyncedDefaultQuicksightAssetBundleImportJob(): ?QuicksightAssetBundleImportJobTransfer;
}
