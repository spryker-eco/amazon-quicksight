<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Persistence;

use Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer;
use Generated\Shared\Transfer\QuicksightUserCollectionTransfer;
use Generated\Shared\Transfer\QuicksightUserCriteriaTransfer;

interface AmazonQuicksightRepositoryInterface
{
    /**
     * @param list<int> $userIds
     *
     * @return list<\Generated\Shared\Transfer\QuicksightUserTransfer>
     */
    public function getQuicksightUsersByUserIds(array $userIds): array;

    /**
     * @param \Generated\Shared\Transfer\QuicksightUserCriteriaTransfer $quicksightUserCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUserCollectionTransfer
     */
    public function getQuicksightUserCollection(
        QuicksightUserCriteriaTransfer $quicksightUserCriteriaTransfer
    ): QuicksightUserCollectionTransfer;

    /**
     * @param string $jobId
     *
     * @return \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer|null
     */
    public function findQuicksightAssetBundleImportJobByJobId(string $jobId): ?QuicksightAssetBundleImportJobTransfer;
}
