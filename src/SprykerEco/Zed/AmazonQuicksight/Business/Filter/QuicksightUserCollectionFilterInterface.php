<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Filter;

use ArrayObject;

interface QuicksightUserCollectionFilterInterface
{
    /**
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\QuicksightUserTransfer> $quicksightUserTransfers
     *
     * @return list<\Generated\Shared\Transfer\QuicksightUserTransfer>
     */
    public function filterOutQuicksightUsersWithUnsupportedQuicksightUserRoles(ArrayObject $quicksightUserTransfers): array;
}
