<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Persistence;

use Generated\Shared\Transfer\QuicksightUserCollectionTransfer;
use Generated\Shared\Transfer\QuicksightUserCriteriaTransfer;
use Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightUserQuery;
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
     * @param \Generated\Shared\Transfer\QuicksightUserCriteriaTransfer $quicksightUserCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUserCollectionTransfer
     */
    public function getQuicksightUserCollection(
        QuicksightUserCriteriaTransfer $quicksightUserCriteriaTransfer
    ): QuicksightUserCollectionTransfer {
        $quicksightUserQuery = $this->getFactory()->getQuicksightUserQuery();
        $quicksightUserQuery = $this->applyQuicksightUserFilters($quicksightUserQuery, $quicksightUserCriteriaTransfer);

        $quicksightUserCollectionTransfer = new QuicksightUserCollectionTransfer();
        $quicksightUserEntities = $quicksightUserQuery->find();
        if ($quicksightUserEntities->count() === 0) {
            return $quicksightUserCollectionTransfer;
        }

        return $this->getFactory()
            ->createQuicksightUserMapper()
            ->mapQuicksightUserEntitiesToQuicksightUserCollectionTransfer(
                $quicksightUserEntities,
                $quicksightUserCollectionTransfer,
            );
    }

    protected function applyQuicksightUserFilters(
        SpyQuicksightUserQuery $quicksightUserQuery,
        QuicksightUserCriteriaTransfer $quicksightUserCriteriaTransfer
    ): SpyQuicksightUserQuery {
        $quicksightUserConditions = $quicksightUserCriteriaTransfer->getQuicksightUserConditions();
        if ($quicksightUserConditions === null) {
            return $quicksightUserQuery;
        }

        if ($quicksightUserConditions->getQuicksightUserIds() !== []) {
            $quicksightUserQuery->filterByIdQuicksightUser_In($quicksightUserConditions->getQuicksightUserIds());
        }

        return $quicksightUserQuery;
    }
}
