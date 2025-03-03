<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Matcher;

use ArrayObject;

interface QuicksightUserMatcherInterface
{
    /**
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\QuicksightUserTransfer> $quicksightUserTransfers
     * @param bool $filterOutExistingQuicksightUsers
     *
     * @return list<\Generated\Shared\Transfer\QuicksightUserTransfer>
     */
    public function getQuicksightUsersMatchedWithExistingUsers(
        ArrayObject $quicksightUserTransfers,
        bool $filterOutExistingQuicksightUsers = false
    ): array;
}
