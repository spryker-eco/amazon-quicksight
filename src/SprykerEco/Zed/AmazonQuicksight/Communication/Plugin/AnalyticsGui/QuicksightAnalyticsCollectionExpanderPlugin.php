<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Communication\Plugin\AnalyticsGui;

use Generated\Shared\Transfer\AnalyticsCollectionTransfer;
use Generated\Shared\Transfer\AnalyticsRequestTransfer;
use Spryker\Zed\AnalyticsGuiExtension\Dependency\Plugin\AnalyticsCollectionExpanderPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \SprykerEco\Zed\AmazonQuicksight\Communication\AmazonQuicksightCommunicationFactory getFactory()
 * @method \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig getConfig()
 * @method \SprykerEco\Zed\AmazonQuicksight\Business\AmazonQuicksightFacadeInterface getFacade()
 */
class QuicksightAnalyticsCollectionExpanderPlugin extends AbstractPlugin implements AnalyticsCollectionExpanderPluginInterface
{
    /**
     * {@inheritDoc}
     * - Requires `AnalyticsRequestTransfer.user` to be set.
     * - Requires `AnalyticsRequestTransfer.user.idUser` to be set.
     * - If Quicksight user with the provided user ID does not exist in DB returns `AnalyticsCollectionTransfer` without any changes.
     * - Otherwise sends request to AWS API to generate an embed URL for a registered Quicksight user. For more information see {@link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_GenerateEmbedUrlForRegisteredUser.html}.
     * - Renders a Quicksight analytics template with the generated embed URL.
     * - Creates `AnalyticsTransfer` and populates `AnalyticsTransfer.content` with the rendered content.
     * - Adds the newly introduced `AnalyticsTransfer` to `AnalyticsCollectionTransfer.analyticsList`.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AnalyticsRequestTransfer $analyticsRequestTransfer
     * @param \Generated\Shared\Transfer\AnalyticsCollectionTransfer $analyticsCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\AnalyticsCollectionTransfer
     */
    public function expand(
        AnalyticsRequestTransfer $analyticsRequestTransfer,
        AnalyticsCollectionTransfer $analyticsCollectionTransfer
    ): AnalyticsCollectionTransfer {
        return $this->getFacade()
            ->expandAnalyticsCollectionWithQuicksightAnalytics($analyticsRequestTransfer, $analyticsCollectionTransfer);
    }
}
