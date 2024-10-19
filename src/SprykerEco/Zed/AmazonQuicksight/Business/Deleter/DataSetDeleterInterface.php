<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Deleter;

use Generated\Shared\Transfer\QuicksightDeleteAssetBundleDataSetsResponseTransfer;

interface DataSetDeleterInterface
{
    /**
     * @return \Generated\Shared\Transfer\QuicksightDeleteAssetBundleDataSetsResponseTransfer
     */
    public function deleteAssetBundleDataSets(): QuicksightDeleteAssetBundleDataSetsResponseTransfer;
}
