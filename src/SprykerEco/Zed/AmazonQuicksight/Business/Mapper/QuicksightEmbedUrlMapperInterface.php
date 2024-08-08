<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Mapper;

use Generated\Shared\Transfer\AnalyticsEmbedUrlResponseTransfer;
use Generated\Shared\Transfer\QuicksightGenerateEmbedUrlRequestTransfer;
use Generated\Shared\Transfer\QuicksightGenerateEmbedUrlResponseTransfer;
use Generated\Shared\Transfer\UserTransfer;

interface QuicksightEmbedUrlMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     * @param \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlRequestTransfer $quicksightGenerateEmbedUrlRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlRequestTransfer
     */
    public function mapUserTransferToQuicksightGenerateEmbedUrlRequestTransfer(
        UserTransfer $userTransfer,
        QuicksightGenerateEmbedUrlRequestTransfer $quicksightGenerateEmbedUrlRequestTransfer
    ): QuicksightGenerateEmbedUrlRequestTransfer;

    /**
     * @param array<string, mixed> $generateEmbedUrlResponseData
     * @param \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlResponseTransfer $quicksightGenerateEmbedUrlResponseTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlResponseTransfer
     */
    public function mapGenerateEmbedUrlResponseDataToQuicksightGenerateEmbedUrlResponseTransfer(
        array $generateEmbedUrlResponseData,
        QuicksightGenerateEmbedUrlResponseTransfer $quicksightGenerateEmbedUrlResponseTransfer
    ): QuicksightGenerateEmbedUrlResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlResponseTransfer $quicksightGenerateEmbedUrlResponseTransfer
     * @param \Generated\Shared\Transfer\AnalyticsEmbedUrlResponseTransfer $analyticsEmbedUrlResponseTransfer
     *
     * @return \Generated\Shared\Transfer\AnalyticsEmbedUrlResponseTransfer
     */
    public function mapQuicksightGenerateEmbedUrlResponseTransferToAnalyticsEmbedUrlResponseTransfer(
        QuicksightGenerateEmbedUrlResponseTransfer $quicksightGenerateEmbedUrlResponseTransfer,
        AnalyticsEmbedUrlResponseTransfer $analyticsEmbedUrlResponseTransfer
    ): AnalyticsEmbedUrlResponseTransfer;
}
