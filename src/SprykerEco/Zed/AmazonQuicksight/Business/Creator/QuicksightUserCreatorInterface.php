<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Creator;

use Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer;

interface QuicksightUserCreatorInterface
{
    /**
     * @return \Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer
     */
    public function createMatchedQuicksightUsers(): QuicksightUserCollectionResponseTransfer;
}
