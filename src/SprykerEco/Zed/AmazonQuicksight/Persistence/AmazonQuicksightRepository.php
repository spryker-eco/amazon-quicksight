<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Persistence;

use Generated\Shared\Transfer\QuicksightAssetBundleImportJobCollectionTransfer;
use Generated\Shared\Transfer\QuicksightAssetBundleImportJobCriteriaTransfer;
use Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightAssetBundleImportJobQuery;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightPersistenceFactory getFactory()
 */
class AmazonQuicksightRepository extends AbstractRepository implements AmazonQuicksightRepositoryInterface
{
    /**
     * @param list<int> $userIds
     *
     * @return list<\Generated\Shared\Transfer\QuicksightUserTransfer>
     */
    public function getQuicksightUsersByUserIds(array $userIds): array
    {
        $quicksightUserEntities = $this->getFactory()
            ->getQuicksightUserQuery()
            ->filterByFkUser_In($userIds)
            ->find();

        if (!$quicksightUserEntities->count()) {
            return [];
        }

        return $this->getFactory()
            ->createQuicksightUserMapper()
            ->mapQuicksightUserEntitiesToQuicksightUserTransfers($quicksightUserEntities, []);
    }

    /**
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobCriteriaTransfer $quicksightAssetBundleImportJobCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightAssetBundleImportJobCollectionTransfer
     */
    public function getQuicksightAssetBundleImportJobCollection(
        QuicksightAssetBundleImportJobCriteriaTransfer $quicksightAssetBundleImportJobCriteriaTransfer
    ): QuicksightAssetBundleImportJobCollectionTransfer {
        $quicksightAssetBundleImportJobQuery = $this->getFactory()->getQuicksightAssetBundleImportJobQuery();
        $quicksightAssetBundleImportJobQuery = $this->applyQuicksightAssetBundleImportJobFilters(
            $quicksightAssetBundleImportJobQuery,
            $quicksightAssetBundleImportJobCriteriaTransfer,
        );

        return $this->getFactory()
            ->createQuicksightAssetBundleImportJobMapper()
            ->mapQuicksightAssetBundleImportJobEntitiesToQuicksightAssetBundleImportJobCollectionTransfer(
                $quicksightAssetBundleImportJobQuery->find(),
                new QuicksightAssetBundleImportJobCollectionTransfer(),
            );
    }

    /**
     * @param \Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightAssetBundleImportJobQuery $quicksightAssetBundleImportJobQuery
     * @param \Generated\Shared\Transfer\QuicksightAssetBundleImportJobCriteriaTransfer $quicksightAssetBundleImportJobCriteriaTransfer
     *
     * @return \Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightAssetBundleImportJobQuery
     */
    protected function applyQuicksightAssetBundleImportJobFilters(
        SpyQuicksightAssetBundleImportJobQuery $quicksightAssetBundleImportJobQuery,
        QuicksightAssetBundleImportJobCriteriaTransfer $quicksightAssetBundleImportJobCriteriaTransfer
    ): SpyQuicksightAssetBundleImportJobQuery {
        $quicksightAssetBundleImportJobConditionsTransfer = $quicksightAssetBundleImportJobCriteriaTransfer
            ->getQuicksightAssetBundleImportJobConditions();

        if (!$quicksightAssetBundleImportJobConditionsTransfer) {
            return $quicksightAssetBundleImportJobQuery;
        }

        if ($quicksightAssetBundleImportJobConditionsTransfer->getJobIds()) {
            $quicksightAssetBundleImportJobQuery->filterByIdQuicksightAssetBundleImportJob_In(
                $quicksightAssetBundleImportJobConditionsTransfer->getJobIds(),
            );
        }

        return $quicksightAssetBundleImportJobQuery;
    }
}
