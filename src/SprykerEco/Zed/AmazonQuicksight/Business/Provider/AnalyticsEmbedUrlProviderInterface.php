<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Provider;

use Generated\Shared\Transfer\AnalyticsEmbedUrlRequestTransfer;
use Generated\Shared\Transfer\AnalyticsEmbedUrlResponseTransfer;

interface AnalyticsEmbedUrlProviderInterface
{
    /**
     * @param \Generated\Shared\Transfer\AnalyticsEmbedUrlRequestTransfer $analyticsEmbedUrlRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AnalyticsEmbedUrlResponseTransfer
     */
    public function getAnalyticsEmbedUrl(
        AnalyticsEmbedUrlRequestTransfer $analyticsEmbedUrlRequestTransfer
    ): AnalyticsEmbedUrlResponseTransfer;
}
