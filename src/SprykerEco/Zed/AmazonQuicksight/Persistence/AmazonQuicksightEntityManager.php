<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Persistence;

use Generated\Shared\Transfer\QuicksightUserTransfer;
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
     * @param list<int> $quicksightUserIds
     *
     * @return void
     */
    public function deleteQuicksightUsers(array $quicksightUserIds): void
    {
        $this->getFactory()
            ->getQuicksightUserQuery()
            ->filterByIdQuicksightUser_In($quicksightUserIds)
            ->delete();
    }
}
