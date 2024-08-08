<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Persistence;

use Generated\Shared\Transfer\QuicksightUserTransfer;

interface AmazonQuicksightEntityManagerInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer $quicksightUserTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUserTransfer
     */
    public function createQuicksightUser(QuicksightUserTransfer $quicksightUserTransfer): QuicksightUserTransfer;

    /**
     * @param list<int> $quicksightUserIds
     *
     * @return void
     */
    public function deleteQuicksightUsers(array $quicksightUserIds): void;
}
