<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\ApiClient;

use Aws\QuickSight\Exception\QuickSightException;
use Generated\Shared\Transfer\ErrorTransfer;
use Generated\Shared\Transfer\QuicksightUserRegisterRequestTransfer;
use Generated\Shared\Transfer\QuicksightUserRegisterResponseTransfer;
use Generated\Shared\Transfer\UserTransfer;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig;
use SprykerEco\Zed\AmazonQuicksight\Business\Formatter\AmazonQuicksightRequestDataFormatterInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Mapper\AmazonQuicksightMapperInterface;
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
     * @var string
     */
    protected const ERROR_MESSAGE_USER_REGISTRATION_FAILED = 'Failed to register Quicksight user.';

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig
     */
    protected AmazonQuicksightConfig $amazonQuicksightConfig;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\Mapper\AmazonQuicksightMapperInterface
     */
    protected AmazonQuicksightMapperInterface $amazonQuicksightMapper;

    protected AmazonQuicksightRequestDataFormatterInterface $amazonQuicksightRequestDataFormatter;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface
     */
    protected AmazonQuicksightToAwsQuicksightClientInterface $amazonQuicksightToAwsQuicksightClient;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig $amazonQuicksightConfig
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Mapper\AmazonQuicksightMapperInterface $amazonQuicksightMapper
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Formatter\AmazonQuicksightRequestDataFormatterInterface $amazonQuicksightRequestDataFormatter
     * @param \SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface $amazonQuicksightToAwsQuicksightClient
     */
    public function __construct(
        AmazonQuicksightConfig $amazonQuicksightConfig,
        AmazonQuicksightMapperInterface $amazonQuicksightMapper,
        AmazonQuicksightRequestDataFormatterInterface $amazonQuicksightRequestDataFormatter,
        AmazonQuicksightToAwsQuicksightClientInterface $amazonQuicksightToAwsQuicksightClient
    ) {
        $this->amazonQuicksightConfig = $amazonQuicksightConfig;
        $this->amazonQuicksightMapper = $amazonQuicksightMapper;
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
