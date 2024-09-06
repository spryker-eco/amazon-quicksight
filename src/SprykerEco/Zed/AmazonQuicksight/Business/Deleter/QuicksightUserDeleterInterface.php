<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Deleter;

use Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer;
use Generated\Shared\Transfer\UserCollectionResponseTransfer;

interface QuicksightUserDeleterInterface
{
    /**
     * @param \Generated\Shared\Transfer\UserCollectionResponseTransfer $userCollectionResponseTransfer
     *
     * @return \Generated\Shared\Transfer\UserCollectionResponseTransfer
     */
    public function deleteQuicksightUsersByUserCollectionResponse(
        UserCollectionResponseTransfer $userCollectionResponseTransfer
    ): UserCollectionResponseTransfer;

    /**
     * @return \Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer
     */
    public function deleteNotMatchedQuicksightUsers(): QuicksightUserCollectionResponseTransfer;
}
