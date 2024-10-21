<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Reader;

use Generated\Shared\Transfer\UserCollectionTransfer;

interface UserReaderInterface
{
    /**
     * @return \Generated\Shared\Transfer\UserCollectionTransfer
     */
    public function getUsersApplicableForQuicksightUserRegistration(): UserCollectionTransfer;
}
