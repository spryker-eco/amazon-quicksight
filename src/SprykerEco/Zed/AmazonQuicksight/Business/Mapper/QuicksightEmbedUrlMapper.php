<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Mapper;

use Generated\Shared\Transfer\AnalyticsEmbedUrlResponseTransfer;
use Generated\Shared\Transfer\QuicksightEmbedUrlTransfer;
use Generated\Shared\Transfer\QuicksightGenerateEmbedUrlRequestTransfer;
use Generated\Shared\Transfer\QuicksightGenerateEmbedUrlResponseTransfer;
use Generated\Shared\Transfer\UserTransfer;

class QuicksightEmbedUrlMapper implements QuicksightEmbedUrlMapperInterface
{
    /**
     * @link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_GenerateEmbedUrlForRegisteredUser.html#API_GenerateEmbedUrlForRegisteredUser_ResponseSyntax
     *
     * @var string
     */
    protected const RESPONSE_KEY_EMBED_URL = 'EmbedUrl';

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     * @param \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlRequestTransfer $quicksightGenerateEmbedUrlRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlRequestTransfer
     */
    public function mapUserTransferToQuicksightGenerateEmbedUrlRequestTransfer(
        UserTransfer $userTransfer,
        QuicksightGenerateEmbedUrlRequestTransfer $quicksightGenerateEmbedUrlRequestTransfer
    ): QuicksightGenerateEmbedUrlRequestTransfer {
        return $quicksightGenerateEmbedUrlRequestTransfer->setUserArn(
            $userTransfer->getQuicksightUserOrFail()->getArnOrFail(),
        );
    }

    /**
     * @param array<string, mixed> $generateEmbedUrlResponseData
     * @param \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlResponseTransfer $quicksightGenerateEmbedUrlResponseTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlResponseTransfer
     */
    public function mapGenerateEmbedUrlResponseDataToQuicksightGenerateEmbedUrlResponseTransfer(
        array $generateEmbedUrlResponseData,
        QuicksightGenerateEmbedUrlResponseTransfer $quicksightGenerateEmbedUrlResponseTransfer
    ): QuicksightGenerateEmbedUrlResponseTransfer {
        return $quicksightGenerateEmbedUrlResponseTransfer->setEmbedUrl(
            (new QuicksightEmbedUrlTransfer())->setUrl($generateEmbedUrlResponseData[static::RESPONSE_KEY_EMBED_URL]),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlResponseTransfer $quicksightGenerateEmbedUrlResponseTransfer
     * @param \Generated\Shared\Transfer\AnalyticsEmbedUrlResponseTransfer $analyticsEmbedUrlResponseTransfer
     *
     * @return \Generated\Shared\Transfer\AnalyticsEmbedUrlResponseTransfer
     */
    public function mapQuicksightGenerateEmbedUrlResponseTransferToAnalyticsEmbedUrlResponseTransfer(
        QuicksightGenerateEmbedUrlResponseTransfer $quicksightGenerateEmbedUrlResponseTransfer,
        AnalyticsEmbedUrlResponseTransfer $analyticsEmbedUrlResponseTransfer
    ): AnalyticsEmbedUrlResponseTransfer {
        return $analyticsEmbedUrlResponseTransfer->fromArray($quicksightGenerateEmbedUrlResponseTransfer->toArray(), true);
    }
}
