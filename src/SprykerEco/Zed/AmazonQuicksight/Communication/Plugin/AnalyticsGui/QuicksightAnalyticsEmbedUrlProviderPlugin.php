<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Communication\Plugin\AnalyticsGui;

use Generated\Shared\Transfer\AnalyticsEmbedUrlRequestTransfer;
use Generated\Shared\Transfer\AnalyticsEmbedUrlResponseTransfer;
use Spryker\Zed\AnalyticsGuiExtension\Communication\Dependency\Plugin\AnalyticsEmbedUrlProviderPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig getConfig()
 * @method \SprykerEco\Zed\AmazonQuicksight\Business\AmazonQuicksightFacadeInterface getFacade()
 * @method \SprykerEco\Zed\AmazonQuicksight\Communication\AmazonQuicksightCommunicationFactory getFactory()
 */
class QuicksightAnalyticsEmbedUrlProviderPlugin extends AbstractPlugin implements AnalyticsEmbedUrlProviderPluginInterface
{
    /**
     * {@inheritDoc}
     * - Requires `AnalyticsEmbedUrlRequestTransfer.user` and `AnalyticsEmbedUrlRequestTransfer.user.idUser` to be set.
     * - Returns `true` if Quicksight user with the provided user ID exists in DB.
     * - Returns `false` otherwise.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AnalyticsEmbedUrlRequestTransfer $analyticsEmbedUrlRequestTransfer
     *
     * @return bool
     */
    public function isApplicable(AnalyticsEmbedUrlRequestTransfer $analyticsEmbedUrlRequestTransfer): bool
    {
        return $this->getFacade()->isQuicksightAnalyticsEmbedUrlProviderApplicable($analyticsEmbedUrlRequestTransfer);
    }

    /**
     * {@inheritDoc}
     * - Requires `AnalyticsEmbedUrlRequestTransfer.user`, `AnalyticsEmbedUrlRequestTransfer.user.quicksightUser`
     *  and `AnalyticsEmbedUrlRequestTransfer.user.quicksightUser.arn` to be set.
     * - Sends request to AWS API to generate an embed URL for a registered Quicksight user. For more information see {@link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_GenerateEmbedUrlForRegisteredUser.html}.
     * - Adds errors to `AnalyticsEmbedUrlResponseTransfer.errors` if Quicksight embed URL generation failed.
     * - Populates `AnalyticsEmbedUrlResponseTransfer.embedUrl.url` with the generated embed URL.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AnalyticsEmbedUrlRequestTransfer $analyticsEmbedUrlRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AnalyticsEmbedUrlResponseTransfer
     */
    public function getEmbedUrl(AnalyticsEmbedUrlRequestTransfer $analyticsEmbedUrlRequestTransfer): AnalyticsEmbedUrlResponseTransfer
    {
        return $this->getFacade()->getQuicksightAnalyticsEmbedUrl($analyticsEmbedUrlRequestTransfer);
    }
}
