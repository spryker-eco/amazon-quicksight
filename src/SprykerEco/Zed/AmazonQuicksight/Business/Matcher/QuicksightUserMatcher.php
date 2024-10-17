<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Matcher;

use ArrayObject;
use SprykerEco\Zed\AmazonQuicksight\Business\Filter\QuicksightUserCollectionFilterInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Filter\UserCollectionFilterInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Reader\UserReaderInterface;

class QuicksightUserMatcher implements QuicksightUserMatcherInterface
{
 /**
  * @var \SprykerEco\Zed\AmazonQuicksight\Business\Reader\UserReaderInterface
  */
    protected UserReaderInterface $userReader;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\Filter\UserCollectionFilterInterface
     */
    protected UserCollectionFilterInterface $userCollectionFilter;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\Filter\QuicksightUserCollectionFilterInterface
     */
    protected QuicksightUserCollectionFilterInterface $quicksightUserCollectionFilter;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Reader\UserReaderInterface $userReader
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Filter\UserCollectionFilterInterface $userCollectionFilter
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Filter\QuicksightUserCollectionFilterInterface $quicksightUserCollectionFilter
     */
    public function __construct(
        UserReaderInterface $userReader,
        UserCollectionFilterInterface $userCollectionFilter,
        QuicksightUserCollectionFilterInterface $quicksightUserCollectionFilter
    ) {
        $this->userReader = $userReader;
        $this->userCollectionFilter = $userCollectionFilter;
        $this->quicksightUserCollectionFilter = $quicksightUserCollectionFilter;
    }

    /**
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\QuicksightUserTransfer> $quicksightUserTransfers
     *
     * @return list<\Generated\Shared\Transfer\QuicksightUserTransfer>
     */
    public function getQuicksightUsersMatchedWithExistingUsers(ArrayObject $quicksightUserTransfers): array
    {
        $filteredQuicksightUserTransfers = $this->quicksightUserCollectionFilter->filterOutQuicksightUsersWithUnsupportedQuicksightUserRoles(
            $quicksightUserTransfers,
        );
        if ($filteredQuicksightUserTransfers === []) {
            return [];
        }

        $userCollectionTransfer = $this->userReader->getUsersApplicableForQuicksightUserRegistration();
        $filteredUserTransfers = $this->userCollectionFilter->filterOutUserTransfersWithExistingQuicksightUser(
            $userCollectionTransfer->getUsers(),
        );
        $userTransfersIndexedByUsername = $this->getUserTransfersIndexedByUsername($filteredUserTransfers);

        $matchedQuicksightUserTransfers = [];
        foreach ($filteredQuicksightUserTransfers as $quicksightUserTransfer) {
            $userTransfer = $userTransfersIndexedByUsername[$quicksightUserTransfer->getUserNameOrFail()] ?? null;
            if (!$userTransfer) {
                continue;
            }

            $quicksightUserTransfer->setFkUser($userTransfer->getIdUserOrFail());
            $matchedQuicksightUserTransfers[] = $quicksightUserTransfer;
        }

        return $matchedQuicksightUserTransfers;
    }

    /**
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\QuicksightUserTransfer> $quicksightUserTransfers
     *
     * @return list<\Generated\Shared\Transfer\QuicksightUserTransfer>
     */
    public function getQuicksightUsersNotMatchedWithExistingUsers(ArrayObject $quicksightUserTransfers): array
    {
        $filteredQuicksightUserTransfers = $this->quicksightUserCollectionFilter->filterOutQuicksightUsersWithUnsupportedQuicksightUserRoles(
            $quicksightUserTransfers,
        );
        if ($filteredQuicksightUserTransfers === []) {
            return [];
        }

        $userCollectionTransfer = $this->userReader->getUsersApplicableForQuicksightUserRegistration();
        $userTransfersIndexedByUsername = $this->getUserTransfersIndexedByUsername(
            $userCollectionTransfer->getUsers()->getArrayCopy(),
        );

        $notMatchedQuicksightUserTransfers = [];
        foreach ($filteredQuicksightUserTransfers as $quicksightUserTransfer) {
            if (isset($userTransfersIndexedByUsername[$quicksightUserTransfer->getUserNameOrFail()])) {
                continue;
            }

            $notMatchedQuicksightUserTransfers[] = $quicksightUserTransfer;
        }

        return $notMatchedQuicksightUserTransfers;
    }

    /**
     * @param array<int|string, \Generated\Shared\Transfer\UserTransfer> $userTransfers
     *
     * @return array<string, \Generated\Shared\Transfer\UserTransfer>
     */
    protected function getUserTransfersIndexedByUsername(array $userTransfers): array
    {
        $indexedUserTransfers = [];
        foreach ($userTransfers as $userTransfer) {
            $indexedUserTransfers[$userTransfer->getUsernameOrFail()] = $userTransfer;
        }

        return $indexedUserTransfers;
    }
}
