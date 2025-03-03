<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Saver;

use Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer;

interface QuicksightUserSaverInterface
{
    /**
     * @param bool $skipExistingQuicksightUsers
     *
     * @return \Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer
     */
    public function saveMatchedQuicksightUsers(bool $skipExistingQuicksightUsers = false): QuicksightUserCollectionResponseTransfer;
}
