<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Adder;

use ArrayObject;
use Generated\Shared\Transfer\UserCollectionResponseTransfer;

interface ErrorAdderInterface
{
    /**
     * @param \Generated\Shared\Transfer\UserCollectionResponseTransfer $userCollectionResponseTransfer
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\ErrorTransfer> $errorTransfers
     * @param string $entityIdentifier
     *
     * @return \Generated\Shared\Transfer\UserCollectionResponseTransfer
     */
    public function addErrorsToUserCollectionResponse(
        UserCollectionResponseTransfer $userCollectionResponseTransfer,
        ArrayObject $errorTransfers,
        string $entityIdentifier
    ): UserCollectionResponseTransfer;
}
