<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Mapper;

use Generated\Shared\Transfer\QuicksightEmbedUrlTransfer;
use Generated\Shared\Transfer\QuicksightGenerateEmbedUrlRequestTransfer;
use Generated\Shared\Transfer\QuicksightGenerateEmbedUrlResponseTransfer;
use Generated\Shared\Transfer\QuicksightUserRegisterRequestTransfer;
use Generated\Shared\Transfer\QuicksightUserTransfer;
use Generated\Shared\Transfer\UserTransfer;

class AmazonQuicksightMapper implements AmazonQuicksightMapperInterface
{
    /**
     * @link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_GenerateEmbedUrlForRegisteredUser.html#API_GenerateEmbedUrlForRegisteredUser_ResponseSyntax
     *
     * @var string
     */
    protected const RESPONSE_KEY_EMBED_URL = 'EmbedUrl';

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     * @param \Generated\Shared\Transfer\QuicksightUserRegisterRequestTransfer $quicksightUserRegisterRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUserRegisterRequestTransfer
     */
    public function mapUserTransferToQuicksightUserRegisterRequestTransfer(
        UserTransfer $userTransfer,
        QuicksightUserRegisterRequestTransfer $quicksightUserRegisterRequestTransfer
    ): QuicksightUserRegisterRequestTransfer {
        return $quicksightUserRegisterRequestTransfer
            ->setEmail($userTransfer->getUsernameOrFail())
            ->setUserName($userTransfer->getUsernameOrFail())
            ->setUserRole(strtoupper($userTransfer->getQuicksightUserOrFail()->getRoleOrFail()));
    }

    /**
     * @param array<string, mixed> $quicksightUserData
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer $quicksightUserTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUserTransfer
     */
    public function mapQuicksightUserDataToQuicksightUserTransfer(
        array $quicksightUserData,
        QuicksightUserTransfer $quicksightUserTransfer
    ): QuicksightUserTransfer {
        return $quicksightUserTransfer->fromArray($quicksightUserData, true);
    }

    /**
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer $quicksightUserTransfer
     * @param \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlRequestTransfer $quicksightGenerateEmbedUrlRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlRequestTransfer
     */
    public function mapQuicksightUserTransferToQuicksightGenerateEmbedUrlRequestTransfer(
        QuicksightUserTransfer $quicksightUserTransfer,
        QuicksightGenerateEmbedUrlRequestTransfer $quicksightGenerateEmbedUrlRequestTransfer
    ): QuicksightGenerateEmbedUrlRequestTransfer {
        return $quicksightGenerateEmbedUrlRequestTransfer->setUserArn($quicksightUserTransfer->getArnOrFail());
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
}
