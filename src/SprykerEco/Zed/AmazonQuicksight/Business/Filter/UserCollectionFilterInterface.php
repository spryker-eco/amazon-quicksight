<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Filter;

interface UserCollectionFilterInterface
{
    /**
     * @param array<int|string, \Generated\Shared\Transfer\UserTransfer> $userTransfers
     *
     * @return array<int|string, \Generated\Shared\Transfer\UserTransfer>
     */
    public function filterOutUserTransfersNotApplicableForQuicksightUserDeletion(array $userTransfers): array;

    /**
     * @param list<\Generated\Shared\Transfer\UserTransfer> $userTransfers
     *
     * @return list<\Generated\Shared\Transfer\UserTransfer>
     */
    public function filterOutUserTransfersWithExistingQuicksightUser(array $userTransfers): array;
}
