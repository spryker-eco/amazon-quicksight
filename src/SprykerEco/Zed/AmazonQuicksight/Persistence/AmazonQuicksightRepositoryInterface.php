<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Persistence;

use Generated\Shared\Transfer\QuicksightAssetBundleImportJobCollectionTransfer;
use Generated\Shared\Transfer\QuicksightAssetBundleImportJobCriteriaTransfer;

interface AmazonQuicksightRepositoryInterface
{
    /**
     * @param list<int> $userIds
     *
     * @return list<\Generated\Shared\Transfer\QuicksightUserTransfer>
     */
    public function getQuicksightUsersByUserIds(array $userIds): array;

    /**
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobCriteriaTransfer $quicksightAssetBundleImportJobCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightAssetBundleImportJobCollectionTransfer
     */
    public function getQuicksightAssetBundleImportJobCollection(
        QuicksightAssetBundleImportJobCriteriaTransfer $quicksightAssetBundleImportJobCriteriaTransfer
    ): QuicksightAssetBundleImportJobCollectionTransfer;
}
