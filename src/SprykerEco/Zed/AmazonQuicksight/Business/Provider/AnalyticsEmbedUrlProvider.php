<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Provider;

use Generated\Shared\Transfer\AnalyticsEmbedUrlRequestTransfer;
use Generated\Shared\Transfer\AnalyticsEmbedUrlResponseTransfer;
use SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Mapper\QuicksightEmbedUrlMapperInterface;

class AnalyticsEmbedUrlProvider implements AnalyticsEmbedUrlProviderInterface
{
    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface
     */
    protected AmazonQuicksightApiClientInterface $amazonQuicksightApiClient;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\Mapper\QuicksightEmbedUrlMapperInterface
     */
    protected QuicksightEmbedUrlMapperInterface $quicksightEmbedUrlMapper;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface $amazonQuicksightApiClient
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Mapper\QuicksightEmbedUrlMapperInterface $quicksightEmbedUrlMapper
     */
    public function __construct(
        AmazonQuicksightApiClientInterface $amazonQuicksightApiClient,
        QuicksightEmbedUrlMapperInterface $quicksightEmbedUrlMapper
    ) {
        $this->amazonQuicksightApiClient = $amazonQuicksightApiClient;
        $this->quicksightEmbedUrlMapper = $quicksightEmbedUrlMapper;
    }

    /**
     * @param \Generated\Shared\Transfer\AnalyticsEmbedUrlRequestTransfer $analyticsEmbedUrlRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AnalyticsEmbedUrlResponseTransfer
     */
    public function getAnalyticsEmbedUrl(
        AnalyticsEmbedUrlRequestTransfer $analyticsEmbedUrlRequestTransfer
    ): AnalyticsEmbedUrlResponseTransfer {
        $quicksightGenerateEmbedUrlResponseTransfer = $this->amazonQuicksightApiClient->generateEmbedUrlForRegisteredUser(
            $analyticsEmbedUrlRequestTransfer->getUserOrFail(),
        );

        return $this->quicksightEmbedUrlMapper
            ->mapQuicksightGenerateEmbedUrlResponseTransferToAnalyticsEmbedUrlResponseTransfer(
                $quicksightGenerateEmbedUrlResponseTransfer,
                new AnalyticsEmbedUrlResponseTransfer(),
            );
    }
}
