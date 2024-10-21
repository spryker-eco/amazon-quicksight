<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Adder;

use ArrayObject;
use Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer;
use Generated\Shared\Transfer\UserCollectionResponseTransfer;

class ErrorAdder implements ErrorAdderInterface
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
    ): UserCollectionResponseTransfer {
        foreach ($errorTransfers as $errorTransfer) {
            $errorTransfer->setEntityIdentifier($entityIdentifier);
            $userCollectionResponseTransfer->addError($errorTransfer);
        }

        return $userCollectionResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer $quicksightUserCollectionResponseTransfer
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\ErrorTransfer> $errorTransfers
     *
     * @return \Generated\Shared\Transfer\QuicksightUserCollectionResponseTransfer
     */
    public function addErrorsToQuicksightUserCollectionResponse(
        QuicksightUserCollectionResponseTransfer $quicksightUserCollectionResponseTransfer,
        ArrayObject $errorTransfers
    ): QuicksightUserCollectionResponseTransfer {
        foreach ($errorTransfers as $errorTransfer) {
            $quicksightUserCollectionResponseTransfer->addError($errorTransfer);
        }

        return $quicksightUserCollectionResponseTransfer;
    }
}
