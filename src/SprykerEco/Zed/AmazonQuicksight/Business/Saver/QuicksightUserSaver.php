<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Saver;

use ArrayObject;
use Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\UserAmazonQuicksightApiClientInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Matcher\QuicksightUserMatcherInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Reader\QuicksightUserReaderInterface;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface;

class QuicksightUserSaver implements QuicksightUserSaverInterface
{
    use TransactionTrait;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Matcher\QuicksightUserMatcherInterface $quicksightUserMatcher
     * @param \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\UserAmazonQuicksightApiClientInterface $userAmazonQuicksightApiClient
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Reader\QuicksightUserReaderInterface $quicksightUserReader
     */
    public function __construct(
        protected QuicksightUserMatcherInterface $quicksightUserMatcher,
        protected AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager,
        protected UserAmazonQuicksightApiClientInterface $userAmazonQuicksightApiClient,
        protected QuicksightUserReaderInterface $quicksightUserReader
    ) {
    }

    /**
     * @param bool $skipExistingQuicksightUsers
     *
     * @return \Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer
     */
    public function saveMatchedQuicksightUsers(bool $skipExistingQuicksightUsers = false): QuicksightUserCollectionResponseTransfer
    {
        $quicksightUserCollectionResponseTransfer = new QuicksightUserCollectionResponseTransfer();

        $quicksightListUsersResponseTransfer = $this->userAmazonQuicksightApiClient->listUsers();
        if ($quicksightListUsersResponseTransfer->getErrors()->count() !== 0) {
            return $quicksightUserCollectionResponseTransfer->setErrors(
                $quicksightListUsersResponseTransfer->getErrors(),
            );
        }

        $quicksightUserTransfers = $quicksightListUsersResponseTransfer->getQuicksightUsers();
        if ($quicksightUserTransfers->count() === 0) {
            return $quicksightUserCollectionResponseTransfer;
        }

        $matchedQuicksightUserTransfers = $this->quicksightUserMatcher->getQuicksightUsersMatchedWithExistingUsers(
            $quicksightUserTransfers,
            $skipExistingQuicksightUsers,
        );

        if ($matchedQuicksightUserTransfers === []) {
            return $quicksightUserCollectionResponseTransfer;
        }

        return $this->executeMatcherQuicksightSave($skipExistingQuicksightUsers, $matchedQuicksightUserTransfers);
    }

    /**
     * @param bool $skipExistingQuicksightUsers
     * @param list<\Generated\Shared\Transfer\QuicksightUserTransfer> $matchedQuicksightUserTransfers
     *
     * @return \Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer
     */
    protected function executeMatcherQuicksightSave(
        bool $skipExistingQuicksightUsers,
        array $matchedQuicksightUserTransfers
    ): QuicksightUserCollectionResponseTransfer {
        $existingQuicksightUserTransfers = $skipExistingQuicksightUsers ? new ArrayObject() : $this->quicksightUserReader->getQuicksightUserCollectionByUserIds(
            $this->getQuicksightUserTransfersUserIds($matchedQuicksightUserTransfers),
        )->getQuicksightUsers();
        $existingQuicksightUserTransfersIndexedByIdUser = $this->getQuicksightUserTransfersIndexedByIdUser($existingQuicksightUserTransfers);
        $quicksightUserTransfersToUpdate = $skipExistingQuicksightUsers ? [] : $this->getQuicksightUserTransfersToUpdate(
            $matchedQuicksightUserTransfers,
            $existingQuicksightUserTransfersIndexedByIdUser,
        );
        $quicksightUserTransfersToCreate = $skipExistingQuicksightUsers ? $matchedQuicksightUserTransfers : $this->getQuicksightUserTransfersToCreate(
            $matchedQuicksightUserTransfers,
            $existingQuicksightUserTransfersIndexedByIdUser,
        );

        $this->getTransactionHandler()->handleTransaction(function () use ($quicksightUserTransfersToUpdate, $quicksightUserTransfersToCreate): void {
            $this->executeSaveMatchedQuicksightUsersTransaction($quicksightUserTransfersToUpdate, $quicksightUserTransfersToCreate);
        });

        return (new QuicksightUserCollectionResponseTransfer())->setQuicksightUsers(new ArrayObject($matchedQuicksightUserTransfers));
    }

    /**
     * @param list<\Generated\Shared\Transfer\QuicksightUserTransfer> $quicksightUserTransfersToUpdate
     * @param list<\Generated\Shared\Transfer\QuicksightUserTransfer> $quicksightUserTransfersToCreate
     *
     * @return void
     */
    protected function executeSaveMatchedQuicksightUsersTransaction(
        array $quicksightUserTransfersToUpdate,
        array $quicksightUserTransfersToCreate
    ): void {
        foreach ($quicksightUserTransfersToUpdate as $quicksightUserTransferToUpdate) {
            $this->amazonQuicksightEntityManager->updateQuicksightUser($quicksightUserTransferToUpdate);
        }

        foreach ($quicksightUserTransfersToCreate as $quicksightUserTransferToCreate) {
            $this->amazonQuicksightEntityManager->createQuicksightUser($quicksightUserTransferToCreate);
        }
    }

    /**
     * @param list<\Generated\Shared\Transfer\QuicksightUserTransfer> $quicksightUserTransfers
     * @param array<int, \Generated\Shared\Transfer\QuicksightUserTransfer> $existingQuicksightUserTransfersIndexedByIdUser
     *
     * @return list<\Generated\Shared\Transfer\QuicksightUserTransfer>
     */
    protected function getQuicksightUserTransfersToUpdate(
        array $quicksightUserTransfers,
        array $existingQuicksightUserTransfersIndexedByIdUser
    ): array {
        $quicksightUserTransfersToUpdate = [];
        foreach ($quicksightUserTransfers as $quicksightUserTransfer) {
            $existingQuicksightUserTransfer = $existingQuicksightUserTransfersIndexedByIdUser[$quicksightUserTransfer->getFkUserOrFail()] ?? null;
            if (!$existingQuicksightUserTransfer) {
                continue;
            }

            $quicksightUserTransfer->setIdQuicksightUser($existingQuicksightUserTransfer->getIdQuicksightUserOrFail());
            $quicksightUserTransfersToUpdate[] = $quicksightUserTransfer;
        }

        return $quicksightUserTransfersToUpdate;
    }

    /**
     * @param list<\Generated\Shared\Transfer\QuicksightUserTransfer> $quicksightUserTransfers
     * @param array<int, \Generated\Shared\Transfer\QuicksightUserTransfer> $existingQuicksightUserTransfersIndexedByIdUser
     *
     * @return list<\Generated\Shared\Transfer\QuicksightUserTransfer>
     */
    protected function getQuicksightUserTransfersToCreate(
        array $quicksightUserTransfers,
        array $existingQuicksightUserTransfersIndexedByIdUser
    ): array {
        $quicksightUserTransfersToCreate = [];
        foreach ($quicksightUserTransfers as $quicksightUserTransfer) {
            if (!isset($existingQuicksightUserTransfersIndexedByIdUser[$quicksightUserTransfer->getFkUserOrFail()])) {
                $quicksightUserTransfersToCreate[] = $quicksightUserTransfer;
            }
        }

        return $quicksightUserTransfersToCreate;
    }

    /**
     * @param list<\Generated\Shared\Transfer\QuicksightUserTransfer> $quicksightUserTransfers
     *
     * @return list<int>
     */
    protected function getQuicksightUserTransfersUserIds(array $quicksightUserTransfers): array
    {
        $userIds = [];
        foreach ($quicksightUserTransfers as $quicksightUserTransfer) {
            $userIds[] = $quicksightUserTransfer->getFkUserOrFail();
        }

        return $userIds;
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
