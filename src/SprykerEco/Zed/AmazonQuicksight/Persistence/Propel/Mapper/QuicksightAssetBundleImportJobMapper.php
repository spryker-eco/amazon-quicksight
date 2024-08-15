<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\QuicksightAssetBundleImportJobCollectionTransfer;
use Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer;
use Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightAssetBundleImportJob;
use Propel\Runtime\Collection\ObjectCollection;

class QuicksightAssetBundleImportJobMapper
{
    /**
     * @param \Propel\Runtime\Collection\ObjectCollection<\Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightAssetBundleImportJob> $quicksightAssetBundleImportJobEntities
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobCollectionTransfer $quicksightAssetBundleImportJobCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightAssetBundleImportJobCollectionTransfer
     */
    public function mapQuicksightAssetBundleImportJobEntitiesToQuicksightAssetBundleImportJobCollectionTransfer(
        ObjectCollection $quicksightAssetBundleImportJobEntities,
        QuicksightAssetBundleImportJobCollectionTransfer $quicksightAssetBundleImportJobCollectionTransfer
    ): QuicksightAssetBundleImportJobCollectionTransfer {
        foreach ($quicksightAssetBundleImportJobEntities as $quicksightAssetBundleImportJobEntity) {
            $quicksightAssetBundleImportJobCollectionTransfer->addQuicksightAssetBundleImportJob(
                $this->mapQuicksightAssetBundleImportJobEntityToQuicksightAssetBundleImportJobTransfer(
                    $quicksightAssetBundleImportJobEntity,
                    new QuicksightAssetBundleImportJobTransfer(),
                ),
            );
        }

        return $quicksightAssetBundleImportJobCollectionTransfer;
    }

    /**
     * @param \Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightAssetBundleImportJob $quicksightAssetBundleImportJobEntity
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer
     */
    protected function mapQuicksightAssetBundleImportJobEntityToQuicksightAssetBundleImportJobTransfer(
        SpyQuicksightAssetBundleImportJob $quicksightAssetBundleImportJobEntity,
        QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer
    ): QuicksightAssetBundleImportJobTransfer {
        return $quicksightAssetBundleImportJobTransfer->fromArray($quicksightAssetBundleImportJobEntity->toArray(), true);
    }
}
