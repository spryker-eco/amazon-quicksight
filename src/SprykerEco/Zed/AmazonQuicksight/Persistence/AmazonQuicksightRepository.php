<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Persistence;

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
}
