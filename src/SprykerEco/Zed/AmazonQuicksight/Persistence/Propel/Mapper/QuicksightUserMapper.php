<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\QuicksightUserTransfer;
use Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightUser;
use Propel\Runtime\Collection\ObjectCollection;

class QuicksightUserMapper
{
    /**
     * @param \Propel\Runtime\Collection\ObjectCollection<\Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightUser> $quicksightUserEntities
     * @param list<\Generated\Shared\Transfer\QuicksightUserTransfer> $quicksightUserTransfers
     *
     * @return list<\Generated\Shared\Transfer\QuicksightUserTransfer>
     */
    public function mapQuicksightUserEntitiesToQuicksightUserTransfers(
        ObjectCollection $quicksightUserEntities,
        array $quicksightUserTransfers
    ): array {
        foreach ($quicksightUserEntities as $quicksightUserEntity) {
            $quicksightUserTransfers[] = $this->mapQuicksightUserEntityToQuicksightUserTransfer(
                $quicksightUserEntity,
                new QuicksightUserTransfer(),
            );
        }

        return $quicksightUserTransfers;
    }

    /**
     * @param \Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightUser $quicksightUserEntity
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer $quicksightUserTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUserTransfer
     */
    protected function mapQuicksightUserEntityToQuicksightUserTransfer(
        SpyQuicksightUser $quicksightUserEntity,
        QuicksightUserTransfer $quicksightUserTransfer
    ): QuicksightUserTransfer {
        return $quicksightUserTransfer->fromArray($quicksightUserEntity->toArray(), true);
    }
}
