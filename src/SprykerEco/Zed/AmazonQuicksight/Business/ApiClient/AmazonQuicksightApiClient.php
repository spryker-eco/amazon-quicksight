<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\ApiClient;

use Aws\QuickSight\Exception\QuickSightException;
use Generated\Shared\Transfer\ErrorTransfer;
use Generated\Shared\Transfer\QuicksightConsoleTransfer;
use Generated\Shared\Transfer\QuicksightExperienceConfigurationTransfer;
use Generated\Shared\Transfer\QuicksightGenerateEmbedUrlRequestTransfer;
use Generated\Shared\Transfer\QuicksightGenerateEmbedUrlResponseTransfer;
use Generated\Shared\Transfer\QuicksightUserRegisterRequestTransfer;
use Generated\Shared\Transfer\QuicksightUserRegisterResponseTransfer;
use Generated\Shared\Transfer\UserTransfer;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig;
use SprykerEco\Zed\AmazonQuicksight\Business\Formatter\AmazonQuicksightRequestDataFormatterInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Mapper\AmazonQuicksightMapperInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Mapper\QuicksightEmbedUrlMapperInterface;
use SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface;

class AmazonQuicksightApiClient implements AmazonQuicksightApiClientInterface
{
    /**
     * @var string
     */
    protected const QUICKSIGHT_USER_IDENTITY_TYPE = 'QUICKSIGHT';

    /**
     * @link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_RegisterUser.html#API_RegisterUser_ResponseSyntax
     *
     * @var string
     */
    protected const RESPONSE_KEY_USER = 'User';

    /**
     * @link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_GenerateEmbedUrlForRegisteredUser.html#API_GenerateEmbedUrlForRegisteredUser_ResponseSyntax
     *
     * @var string
     */
    protected const RESPONSE_KEY_EMBED_URL = 'EmbedUrl';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_USER_REGISTRATION_FAILED = 'Failed to register Quicksight user.';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_EMBED_URL_GENERATION_FAILED = 'Failed to generate Embed URL user.';

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig
     */
    protected AmazonQuicksightConfig $amazonQuicksightConfig;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\Mapper\AmazonQuicksightMapperInterface
     */
    protected AmazonQuicksightMapperInterface $amazonQuicksightMapper;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\Mapper\QuicksightEmbedUrlMapperInterface
     */
    protected QuicksightEmbedUrlMapperInterface $quicksightEmbedUrlMapper;

    protected AmazonQuicksightRequestDataFormatterInterface $amazonQuicksightRequestDataFormatter;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface
     */
    protected AmazonQuicksightToAwsQuicksightClientInterface $amazonQuicksightToAwsQuicksightClient;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig $amazonQuicksightConfig
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Mapper\AmazonQuicksightMapperInterface $amazonQuicksightMapper
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Mapper\QuicksightEmbedUrlMapperInterface $quicksightEmbedUrlMapper
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Formatter\AmazonQuicksightRequestDataFormatterInterface $amazonQuicksightRequestDataFormatter
     * @param \SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface $amazonQuicksightToAwsQuicksightClient
     */
    public function __construct(
        AmazonQuicksightConfig $amazonQuicksightConfig,
        AmazonQuicksightMapperInterface $amazonQuicksightMapper,
        QuicksightEmbedUrlMapperInterface $quicksightEmbedUrlMapper,
        AmazonQuicksightRequestDataFormatterInterface $amazonQuicksightRequestDataFormatter,
        AmazonQuicksightToAwsQuicksightClientInterface $amazonQuicksightToAwsQuicksightClient
    ) {
        $this->amazonQuicksightConfig = $amazonQuicksightConfig;
        $this->amazonQuicksightMapper = $amazonQuicksightMapper;
        $this->quicksightEmbedUrlMapper = $quicksightEmbedUrlMapper;
        $this->amazonQuicksightRequestDataFormatter = $amazonQuicksightRequestDataFormatter;
        $this->amazonQuicksightToAwsQuicksightClient = $amazonQuicksightToAwsQuicksightClient;
    }

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUserRegisterResponseTransfer
     */
    public function registerUser(UserTransfer $userTransfer): QuicksightUserRegisterResponseTransfer
    {
        $quicksightUserRegisterRequestTransfer = $this->createQuicksightUserRegisterRequestTransfer();
        $quicksightUserRegisterRequestTransfer = $this->amazonQuicksightMapper->mapUserTransferToQuicksightUserRegisterRequestTransfer(
            $userTransfer,
            $quicksightUserRegisterRequestTransfer,
        );

        $requestData = $this->amazonQuicksightRequestDataFormatter->formatRequestData(
            $quicksightUserRegisterRequestTransfer->toArray(true, true),
        );

        $quicksightUserRegisterResponseTransfer = new QuicksightUserRegisterResponseTransfer();
        try {
            $response = $this->amazonQuicksightToAwsQuicksightClient->registerUser($requestData);
        } catch (QuickSightException $quickSightException) {
            return $quicksightUserRegisterResponseTransfer->addError(
                (new ErrorTransfer())->setMessage($quickSightException->getAwsErrorMessage()),
            );
        }

        if (!$response->hasKey(static::RESPONSE_KEY_USER)) {
            return $quicksightUserRegisterResponseTransfer->addError(
                (new ErrorTransfer())->setMessage(static::ERROR_MESSAGE_USER_REGISTRATION_FAILED),
            );
        }

        $quicksightUserTransfer = $this->amazonQuicksightMapper->mapQuicksightUserDataToQuicksightUserTransfer(
            $response->get(static::RESPONSE_KEY_USER),
            $userTransfer->getQuicksightUserOrFail(),
        );

        return $quicksightUserRegisterResponseTransfer->setQuicksightUser($quicksightUserTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlResponseTransfer
     */
    public function generateEmbedUrlForRegisteredUser(UserTransfer $userTransfer): QuicksightGenerateEmbedUrlResponseTransfer
    {
        $quicksightGenerateEmbedUrlRequestTransfer = $this->createQuicksightGenerateEmbedUrlRequestTransfer();
        $quicksightGenerateEmbedUrlRequestTransfer = $this->quicksightEmbedUrlMapper
            ->mapUserTransferToQuicksightGenerateEmbedUrlRequestTransfer(
                $userTransfer,
                $quicksightGenerateEmbedUrlRequestTransfer,
            );
        $quicksightGenerateEmbedUrlResponseTransfer = new QuicksightGenerateEmbedUrlResponseTransfer();

        $requestData = $this->amazonQuicksightRequestDataFormatter->formatRequestData(
            $quicksightGenerateEmbedUrlRequestTransfer->modifiedToArray(true, true),
        );

        try {
            $response = $this->amazonQuicksightToAwsQuicksightClient->generateEmbedUrlForRegisteredUser($requestData);
        } catch (QuickSightException $quickSightException) {
            return $quicksightGenerateEmbedUrlResponseTransfer->addError(
                (new ErrorTransfer())->setMessage($quickSightException->getAwsErrorMessage()),
            );
        }

        if (!$response->hasKey(static::RESPONSE_KEY_EMBED_URL)) {
            return $quicksightGenerateEmbedUrlResponseTransfer->addError(
                (new ErrorTransfer())->setMessage(static::ERROR_MESSAGE_EMBED_URL_GENERATION_FAILED),
            );
        }

        return $this->quicksightEmbedUrlMapper
            ->mapGenerateEmbedUrlResponseDataToQuicksightGenerateEmbedUrlResponseTransfer(
                $response->toArray(),
                $quicksightGenerateEmbedUrlResponseTransfer,
            );
    }

    /**
     * @return \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlRequestTransfer
     */
    protected function createQuicksightGenerateEmbedUrlRequestTransfer(): QuicksightGenerateEmbedUrlRequestTransfer
    {
        return (new QuicksightGenerateEmbedUrlRequestTransfer())
            ->setAwsAccountId($this->amazonQuicksightConfig->getAwsAccountId())
            ->setExperienceConfiguration((new QuicksightExperienceConfigurationTransfer())->setQuickSightConsole(
                (new QuicksightConsoleTransfer())->setInitialPath(
                    $this->amazonQuicksightConfig->getQuicksightConsoleInitialPath(),
                ),
            ));
    }

    /**
     * @return \Generated\Shared\Transfer\QuicksightUserRegisterRequestTransfer
     */
    protected function createQuicksightUserRegisterRequestTransfer(): QuicksightUserRegisterRequestTransfer
    {
        return (new QuicksightUserRegisterRequestTransfer())
            ->setIdentityType(static::QUICKSIGHT_USER_IDENTITY_TYPE)
            ->setAwsAccountId($this->amazonQuicksightConfig->getAwsAccountId())
            ->setNamespace($this->amazonQuicksightConfig->getQuicksightRegisterUserNamespace());
    }
}