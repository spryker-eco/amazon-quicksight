<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\QuicksightUserCollectionTransfer;
use Generated\Shared\Transfer\QuicksightUserTransfer;
use Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightUser;
use Propel\Runtime\Collection\Collection;

class QuicksightUserMapper
{
    /**
     * @param \Propel\Runtime\Collection\Collection<\Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightUser> $quicksightUserEntities
     * @param \Generated\Shared\Transfer\QuicksightUserCollectionTransfer $quicksightUserCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUserCollectionTransfer
     */
    public function mapQuicksightUserEntitiesToQuicksightUserCollectionTransfer(
        Collection $quicksightUserEntities,
        QuicksightUserCollectionTransfer $quicksightUserCollectionTransfer
    ): QuicksightUserCollectionTransfer {
        foreach ($quicksightUserEntities as $quicksightUserEntity) {
            $quicksightUserCollectionTransfer->addQuicksightUser(
                $this->mapQuicksightUserEntityToQuicksightUserTransfer(
                    $quicksightUserEntity,
                    new QuicksightUserTransfer(),
                ),
            );
        }

        return $quicksightUserCollectionTransfer;
    }

    /**
     * @param \Propel\Runtime\Collection\Collection<\Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightUser> $quicksightUserEntities
     * @param list<\Generated\Shared\Transfer\QuicksightUserTransfer> $quicksightUserTransfers
     *
     * @return list<\Generated\Shared\Transfer\QuicksightUserTransfer>
     */
    public function mapQuicksightUserEntitiesToQuicksightUserTransfers(
        Collection $quicksightUserEntities,
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
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer $quicksightUserTransfer
     * @param \Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightUser $quicksightUserEntity
     *
     * @return \Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightUser
     */
    public function mapQuicksightUserTransferToQuicksightUserEntity(
        QuicksightUserTransfer $quicksightUserTransfer,
        SpyQuicksightUser $quicksightUserEntity
    ): SpyQuicksightUser {
        return $quicksightUserEntity->fromArray($quicksightUserTransfer->modifiedToArray());
    }

    /**
     * @param \Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightUser $quicksightUserEntity
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer $quicksightUserTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUserTransfer
     */
    public function mapQuicksightUserEntityToQuicksightUserTransfer(
        SpyQuicksightUser $quicksightUserEntity,
        QuicksightUserTransfer $quicksightUserTransfer
    ): QuicksightUserTransfer {
        return $quicksightUserTransfer->fromArray($quicksightUserEntity->toArray(), true);
    }
}
