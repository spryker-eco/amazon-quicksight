<?php

/**
 * MIT License
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
     * @param list<int> $quicksightUserIds
     *
     * @return \Generated\Shared\Transfer\QuicksightUserCollectionTransfer
     */
    public function getQuicksightUserCollectionByQuicksightUserIds(array $quicksightUserIds): QuicksightUserCollectionTransfer
    {
        $quicksightUserConditionsTransfer = (new QuicksightUserConditionsTransfer())
            ->setQuicksightUserIds($quicksightUserIds);
        $quicksightUSerCriteriaTransfer = (new QuicksightUserCriteriaTransfer())
            ->setQuicksightUserConditions($quicksightUserConditionsTransfer);

        return $this->amazonQuicksightRepository->getQuicksightUserCollection($quicksightUSerCriteriaTransfer);
    }
}
