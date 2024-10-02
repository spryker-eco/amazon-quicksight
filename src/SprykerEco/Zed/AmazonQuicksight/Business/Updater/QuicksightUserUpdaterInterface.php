<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Updater;

use Generated\Shared\Transfer\QuicksightUpdateUserResponseTransfer;
use Generated\Shared\Transfer\UserTransfer;

interface QuicksightUserUpdaterInterface
{
    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUpdateUserResponseTransfer
     */
    public function updateQuicksightUser(UserTransfer $userTransfer): QuicksightUpdateUserResponseTransfer;
}
