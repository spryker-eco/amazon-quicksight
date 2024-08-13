<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Expander;

use Generated\Shared\Transfer\UserCollectionTransfer;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface;

class UserExpander implements UserExpanderInterface
{
    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface
     */
    protected AmazonQuicksightRepositoryInterface $amazonQuicksightRepository;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface $amazonQuicksightRepository
     */
    public function __construct(AmazonQuicksightRepositoryInterface $amazonQuicksightRepository)
    {
        $this->amazonQuicksightRepository = $amazonQuicksightRepository;
    }

    /**
     * @param \Generated\Shared\Transfer\UserCollectionTransfer $userCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\UserCollectionTransfer
     */
    public function expandUserCollectionWithQuicksightUsers(
        UserCollectionTransfer $userCollectionTransfer
    ): UserCollectionTransfer {
        $userIds = $this->extractUserIds($userCollectionTransfer);
        $quicksightUserTransfers = $this->amazonQuicksightRepository->getQuicksightUsersByUserIds($userIds);

        if (!$quicksightUserTransfers) {
            return $userCollectionTransfer;
        }

        $quicksightUserTransfersIndexedByIdUser = $this->getQuicksightUserTransfersIndexedByIdUser($quicksightUserTransfers);

        foreach ($userCollectionTransfer->getUsers() as $userTransfer) {
            $idUser = $userTransfer->getIdUserOrFail();
            if (isset($quicksightUserTransfersIndexedByIdUser[$idUser])) {
                $userTransfer->setQuicksightUser($quicksightUserTransfersIndexedByIdUser[$idUser]);
            }
        }

        return $userCollectionTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UserCollectionTransfer $userCollectionTransfer
     *
     * @return list<int>
     */
    protected function extractUserIds(UserCollectionTransfer $userCollectionTransfer): array
    {
        $userIds = [];
        foreach ($userCollectionTransfer->getUsers() as $userTransfer) {
            $userIds[] = $userTransfer->getIdUserOrFail();
        }

        return $userIds;
    }

    /**
     * @param list<\Generated\Shared\Transfer\QuicksightUserTransfer> $quicksightUserTransfers
     *
     * @return array<int, \Generated\Shared\Transfer\QuicksightUserTransfer>
     */
    protected function getQuicksightUserTransfersIndexedByIdUser(array $quicksightUserTransfers): array
    {
        $quicksightUserTransfersIndexedByIdUser = [];
        foreach ($quicksightUserTransfers as $quicksightUserTransfer) {
            $quicksightUserTransfersIndexedByIdUser[$quicksightUserTransfer->getFkUserOrFail()] = $quicksightUserTransfer;
        }

        return $quicksightUserTransfersIndexedByIdUser;
    }
}
