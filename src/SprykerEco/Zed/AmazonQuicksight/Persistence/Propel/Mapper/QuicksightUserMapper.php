<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\QuicksightUserTransfer;
use Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightUser;

class QuicksightUserMapper
{
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
