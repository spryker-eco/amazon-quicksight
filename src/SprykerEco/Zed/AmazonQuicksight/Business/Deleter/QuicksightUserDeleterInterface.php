<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Deleter;

use Generated\Shared\Transfer\QuicksightUserCollectionDeleteCriteriaTransfer;
use Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer;

interface QuicksightUserDeleterInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuicksightUserCollectionDeleteCriteriaTransfer $quicksightUserCollectionDeleteCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer
     */
    public function deleteQuicksightUserCollection(
        QuicksightUserCollectionDeleteCriteriaTransfer $quicksightUserCollectionDeleteCriteriaTransfer
    ): QuicksightUserCollectionResponseTransfer;
}
