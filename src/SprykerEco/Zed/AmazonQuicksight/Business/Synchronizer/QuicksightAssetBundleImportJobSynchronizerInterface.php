<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
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
