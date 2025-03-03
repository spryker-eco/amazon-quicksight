<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Filter;

use ArrayObject;
use Generated\Shared\Transfer\UserTransfer;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig;
use SprykerEco\Zed\AmazonQuicksight\Business\Reader\QuicksightUserReaderInterface;

class UserCollectionFilter implements UserCollectionFilterInterface
{
    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig
     */
    protected AmazonQuicksightConfig $amazonQuicksightConfig;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\Reader\QuicksightUserReaderInterface
     */
    protected QuicksightUserReaderInterface $quicksightUserReader;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig $amazonQuicksightConfig
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Reader\QuicksightUserReaderInterface $quicksightUserReader
     */
    public function __construct(AmazonQuicksightConfig $amazonQuicksightConfig, QuicksightUserReaderInterface $quicksightUserReader)
    {
        $this->amazonQuicksightConfig = $amazonQuicksightConfig;
        $this->quicksightUserReader = $quicksightUserReader;
    }

    /**
     * @param array<int|string, \Generated\Shared\Transfer\UserTransfer> $userTransfers
     *
     * @return array<int|string, \Generated\Shared\Transfer\UserTransfer>
     */
    public function filterOutUserTransfersNotApplicableForQuicksightUserDeletion(array $userTransfers): array
    {
        $filteredUserTransfers = $this->filterOutUsersWithNotApplicableStatus(
            $userTransfers,
            $this->amazonQuicksightConfig->getUserStatusesApplicableForQuicksightUserDeletion(),
        );
        $filteredUserTransfers = $this->filterOutUserTransfersWithoutPersistedQuicksightUser($filteredUserTransfers);

        return $filteredUserTransfers;
    }

    /**
     * @param list<\Generated\Shared\Transfer\UserTransfer> $userTransfers
     *
     * @return array<int|string, \Generated\Shared\Transfer\UserTransfer>
     */
    public function filterOutUserTransfersWithExistingQuicksightUser(array $userTransfers): array
    {
        return $this->filterOutUserTransfersWithPersistedQuicksightUser($userTransfers);
    }

    /**
     * @param array<int|string, \Generated\Shared\Transfer\UserTransfer> $userTransfers
     * @param list<string> $applicableUserStatuses
     *
     * @return array<int|string, \Generated\Shared\Transfer\UserTransfer>
     */
    protected function filterOutUsersWithNotApplicableStatus(array $userTransfers, array $applicableUserStatuses): array
    {
        $applicableUserStatuses = array_flip($applicableUserStatuses);

        return array_filter($userTransfers, function (UserTransfer $userTransfer) use ($applicableUserStatuses) {
            return isset($applicableUserStatuses[$userTransfer->getStatusOrFail()]);
        });
    }

    /**
     * @param array<int|string, \Generated\Shared\Transfer\UserTransfer> $userTransfers
     *
     * @return array<int|string, \Generated\Shared\Transfer\UserTransfer>
     */
    protected function filterOutUserTransfersWithPersistedQuicksightUser(array $userTransfers): array
    {
        $quicksightUserCollectionTransfer = $this->quicksightUserReader->getQuicksightUserCollectionByUserTransfers(
            $userTransfers,
        );

        if ($quicksightUserCollectionTransfer->getQuicksightUsers()->count() === 0) {
            return $userTransfers;
        }
        $quicksightUserTransfersIndexedByIdUser = $this->getQuicksightUserTransfersIndexedByIdUser(
            $quicksightUserCollectionTransfer->getQuicksightUsers(),
        );

        return array_filter($userTransfers, function (UserTransfer $userTransfer) use ($quicksightUserTransfersIndexedByIdUser) {
            return !isset($quicksightUserTransfersIndexedByIdUser[$userTransfer->getIdUserOrFail()]);
        });
    }

    /**
     * @param array<int|string, \Generated\Shared\Transfer\UserTransfer> $userTransfers
     *
     * @return array<int|string, \Generated\Shared\Transfer\UserTransfer>
     */
    protected function filterOutUserTransfersWithoutPersistedQuicksightUser(array $userTransfers): array
    {
        $quicksightUserCollectionTransfer = $this->quicksightUserReader->getQuicksightUserCollectionByUserTransfers(
            $userTransfers,
        );

        if ($quicksightUserCollectionTransfer->getQuicksightUsers()->count() === 0) {
            return [];
        }

        $quicksightUserTransfersIndexedByIdUser = $this->getQuicksightUserTransfersIndexedByIdUser(
            $quicksightUserCollectionTransfer->getQuicksightUsers(),
        );

        return array_filter($userTransfers, function (UserTransfer $userTransfer) use ($quicksightUserTransfersIndexedByIdUser) {
            return isset($quicksightUserTransfersIndexedByIdUser[$userTransfer->getIdUserOrFail()]);
        });
    }

    /**
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\QuicksightUserTransfer> $quicksightUserTransfers
     *
     * @return array<int, \Generated\Shared\Transfer\QuicksightUserTransfer>
     */
    protected function getQuicksightUserTransfersIndexedByIdUser(ArrayObject $quicksightUserTransfers): array
    {
        $indexedQuicksightUserTransfers = [];
        foreach ($quicksightUserTransfers as $quicksightUserTransfer) {
            $indexedQuicksightUserTransfers[$quicksightUserTransfer->getFkUserOrFail()] = $quicksightUserTransfer;
        }

        return $indexedQuicksightUserTransfers;
    }
}
