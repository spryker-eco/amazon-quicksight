<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Persistence;

use Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer;
use Generated\Shared\Transfer\QuicksightUserTransfer;
use Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightAssetBundleImportJob;
use Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightUser;
use Spryker\Zed\Kernel\Persistence\AbstractEntityManager;

/**
 * @method \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightPersistenceFactory getFactory()
 */
class AmazonQuicksightEntityManager extends AbstractEntityManager implements AmazonQuicksightEntityManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer $quicksightUserTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUserTransfer
     */
    public function createQuicksightUser(QuicksightUserTransfer $quicksightUserTransfer): QuicksightUserTransfer
    {
        $quicksightUserEntity = $this->getFactory()
            ->createQuicksightUserMapper()
            ->mapQuicksightUserTransferToQuicksightUserEntity($quicksightUserTransfer, new SpyQuicksightUser());

        $quicksightUserEntity->save();

        return $this->getFactory()
            ->createQuicksightUserMapper()
            ->mapQuicksightUserEntityToQuicksightUserTransfer($quicksightUserEntity, $quicksightUserTransfer);
    }

    /**
     * @param list<int> $userIds
     *
     * @return void
     */
    public function deleteQuicksightUsersByUserIds(array $userIds): void
    {
        $this->getFactory()
            ->getQuicksightUserQuery()
            ->filterByFkUser_In($userIds)
            ->delete();
    }

    /**
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer
     */
    public function createQuicksightAssetBundleImportJob(
        QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer
    ): QuicksightAssetBundleImportJobTransfer {
        $quicksightAssetBundleImportJobEntity = $this->getFactory()
            ->createQuicksightAssetBundleImportJobMapper()
            ->mapQuicksightAssetBundleImportJobTransferToQuicksightAssetBundleImportJobEntity(
                $quicksightAssetBundleImportJobTransfer,
                new SpyQuicksightAssetBundleImportJob(),
            );

        $quicksightAssetBundleImportJobEntity->save();

        return $this->getFactory()
            ->createQuicksightAssetBundleImportJobMapper()
            ->mapQuicksightAssetBundleImportJobEntityToQuicksightAssetBundleImportJobTransfer(
                $quicksightAssetBundleImportJobEntity,
                $quicksightAssetBundleImportJobTransfer,
            );
    }

    /**
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer
     */
    public function updateQuicksightAssetBundleImportJob(
        QuicksightAssetBundleImportJobTransfer $quicksightAssetBundleImportJobTransfer
    ): QuicksightAssetBundleImportJobTransfer {
        $quicksightAssetBundleImportJobQuery = $this->getFactory()->getQuicksightAssetBundleImportJobQuery();
        $quicksightAssetBundleImportJobQuery->filterByJobId($quicksightAssetBundleImportJobTransfer->getJobIdOrFail());

        $quicksightAssetBundleImportJobEntity = $quicksightAssetBundleImportJobQuery->findOne();

        if ($quicksightAssetBundleImportJobEntity === null) {
            return $quicksightAssetBundleImportJobTransfer;
        }

        $quicksightAssetBundleImportJobEntity = $this->getFactory()
            ->createQuicksightAssetBundleImportJobMapper()
            ->mapQuicksightAssetBundleImportJobTransferToQuicksightAssetBundleImportJobEntity(
                $quicksightAssetBundleImportJobTransfer,
                $quicksightAssetBundleImportJobEntity,
            );

        $quicksightAssetBundleImportJobEntity->save();

        return $this->getFactory()
            ->createQuicksightAssetBundleImportJobMapper()
            ->mapQuicksightAssetBundleImportJobEntityToQuicksightAssetBundleImportJobTransfer(
                $quicksightAssetBundleImportJobEntity,
                $quicksightAssetBundleImportJobTransfer,
            );
    }
}
