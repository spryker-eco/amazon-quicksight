<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Checker;

use Generated\Shared\Transfer\AnalyticsEmbedUrlRequestTransfer;

interface QuicksightAnalyticsEmbedUrlProviderCheckerInterface
{
    /**
     * @param \Generated\Shared\Transfer\AnalyticsEmbedUrlRequestTransfer $analyticsEmbedUrlRequestTransfer
     *
     * @return bool
     */
    public function isQuicksightAnalyticsEmbedUrlProviderApplicable(
        AnalyticsEmbedUrlRequestTransfer $analyticsEmbedUrlRequestTransfer
    ): bool;
}
