<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Checker;

use Generated\Shared\Transfer\AnalyticsEmbedUrlRequestTransfer;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface;

class QuicksightAnalyticsEmbedUrlProviderChecker implements QuicksightAnalyticsEmbedUrlProviderCheckerInterface
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
     * @param \Generated\Shared\Transfer\AnalyticsEmbedUrlRequestTransfer $analyticsEmbedUrlRequestTransfer
     *
     * @return bool
     */
    public function isQuicksightAnalyticsEmbedUrlProviderApplicable(
        AnalyticsEmbedUrlRequestTransfer $analyticsEmbedUrlRequestTransfer
    ): bool {
        $userTransfer = $analyticsEmbedUrlRequestTransfer->getUserOrFail();

        $quicksightUserTransfers = $this->amazonQuicksightRepository
            ->getQuicksightUsersByUserIds([$userTransfer->getIdUserOrFail()]);

        return count($quicksightUserTransfers) > 0;
    }
}
