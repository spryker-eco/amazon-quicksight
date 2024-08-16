<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Filter;

use ArrayObject;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig;

class QuicksightUserCollectionFilter implements QuicksightUserCollectionFilterInterface
{
    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig
     */
    protected AmazonQuicksightConfig $amazonQuicksightConfig;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig $amazonQuicksightConfig
     */
    public function __construct(AmazonQuicksightConfig $amazonQuicksightConfig)
    {
        $this->amazonQuicksightConfig = $amazonQuicksightConfig;
    }

    /**
     * @param \ArrayObject<array-key, \Generated\Shared\Transfer\QuicksightUserTransfer> $quicksightUserTransfers
     *
     * @return list<\Generated\Shared\Transfer\QuicksightUserTransfer>
     */
    public function filterOutQuicksightUsersWithUnsupportedQuicksightUserRoles(ArrayObject $quicksightUserTransfers): array
    {
        $quicksightUserRoles = array_flip($this->amazonQuicksightConfig->getQuicksightUserRoles());

        $filteredQuicksightUserTransfers = [];
        foreach ($quicksightUserTransfers as $quicksightUserTransfer) {
            if (isset($quicksightUserRoles[$quicksightUserTransfer->getRoleOrFail()])) {
                $filteredQuicksightUserTransfers[] = $quicksightUserTransfer;
            }
        }

        return $filteredQuicksightUserTransfers;
    }
}
