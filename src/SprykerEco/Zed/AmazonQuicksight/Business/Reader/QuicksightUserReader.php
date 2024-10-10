<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Reader;

use Generated\Shared\Transfer\QuicksightUserCollectionTransfer;
use Generated\Shared\Transfer\QuicksightUserConditionsTransfer;
use Generated\Shared\Transfer\QuicksightUserCriteriaTransfer;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface;

class QuicksightUserReader implements QuicksightUserReaderInterface
{
    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface
     */
    protected AmazonQuicksightRepositoryInterface $amazonQuicksightRepository;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface $amazonQuicksightRepository
     */
    public function __construct(AmazonQuicksightRepositoryInterface $amazonQuicksightRepository)
    {
        $this->amazonQuicksightRepository = $amazonQuicksightRepository;
    }

    /**
     * @param array<int|string, \Generated\Shared\Transfer\UserTransfer> $userTransfers
     *
     * @return \Generated\Shared\Transfer\QuicksightUserCollectionTransfer
     */
    public function getQuicksightUserCollectionByUserTransfers(array $userTransfers): QuicksightUserCollectionTransfer
    {
        $quicksightUserConditionsTransfer = new QuicksightUserConditionsTransfer();
        foreach ($userTransfers as $userTransfer) {
            $quicksightUserConditionsTransfer->addIdUser($userTransfer->getIdUserOrFail());
        }

        $quicksightUserCriteriaTransfer = (new QuicksightUserCriteriaTransfer())
            ->setQuicksightUserConditions($quicksightUserConditionsTransfer);

        return $this->amazonQuicksightRepository->getQuicksightUserCollection($quicksightUserCriteriaTransfer);
    }
}
