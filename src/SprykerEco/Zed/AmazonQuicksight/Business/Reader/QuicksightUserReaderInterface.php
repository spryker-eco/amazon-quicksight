<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Reader;

use Generated\Shared\Transfer\QuicksightUserCollectionTransfer;

interface QuicksightUserReaderInterface
{
    /**
     * @param list<int> $quicksightUserIds
     *
     * @return \Generated\Shared\Transfer\QuicksightUserCollectionTransfer
     */
    public function getQuicksightUserCollectionByQuicksightUserIds(array $quicksightUserIds): QuicksightUserCollectionTransfer;
}
