<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Reader;

use Generated\Shared\Transfer\QuicksightUserCollectionTransfer;

interface QuicksightUserReaderInterface
{
    /**
     * @param array<int|string, \Generated\Shared\Transfer\UserTransfer> $userTransfers
     *
     * @return \Generated\Shared\Transfer\QuicksightUserCollectionTransfer
     */
    public function getQuicksightUserCollectionByUserTransfers(array $userTransfers): QuicksightUserCollectionTransfer;
}
