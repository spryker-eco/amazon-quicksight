<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Filter;

use ArrayObject;

interface UserCollectionFilterInterface
{
    /**
     * @param array<int|string, \Generated\Shared\Transfer\UserTransfer> $userTransfers
     *
     * @return array<int|string, \Generated\Shared\Transfer\UserTransfer>
     */
    public function filterOutUserTransfersNotApplicableForQuicksightUserRegistration(array $userTransfers): array;

    /**
     * @param array<int|string, \Generated\Shared\Transfer\UserTransfer> $userTransfers
     *
     * @return array<int|string, \Generated\Shared\Transfer\UserTransfer>
     */
    public function filterOutUserTransfersNotApplicableForQuicksightUserDeletion(array $userTransfers): array;

    /**
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\UserTransfer> $userTransfers
     *
     * @return list<\Generated\Shared\Transfer\UserTransfer>
     */
    public function filterOutUserTransfersWithExistingQuicksightUser(ArrayObject $userTransfers): array;
}
