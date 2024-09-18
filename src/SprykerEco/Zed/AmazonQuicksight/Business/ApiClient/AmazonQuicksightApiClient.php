<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\ApiClient;

use Aws\QuickSight\Exception\QuickSightException;
use Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer;
use Generated\Shared\Transfer\ErrorTransfer;
use Generated\Shared\Transfer\QuicksightConsoleTransfer;
use Generated\Shared\Transfer\QuicksightDeleteUserRequestTransfer;
use Generated\Shared\Transfer\QuicksightDeleteUserResponseTransfer;
use Generated\Shared\Transfer\QuicksightDescribeAssetBundleImportJobRequestTransfer;
use Generated\Shared\Transfer\QuicksightDescribeAssetBundleImportJobResponseTransfer;
use Generated\Shared\Transfer\QuicksightExperienceConfigurationTransfer;
use Generated\Shared\Transfer\QuicksightGenerateEmbedUrlRequestTransfer;
use Generated\Shared\Transfer\QuicksightGenerateEmbedUrlResponseTransfer;
use Generated\Shared\Transfer\QuicksightListUsersRequestTransfer;
use Generated\Shared\Transfer\QuicksightListUsersResponseTransfer;
use Generated\Shared\Transfer\QuicksightOverrideParametersDataSourceCredentialPairTransfer;
use Generated\Shared\Transfer\QuicksightOverrideParametersDataSourceCredentialsTransfer;
use Generated\Shared\Transfer\QuicksightOverrideParametersDataSourceTransfer;
use Generated\Shared\Transfer\QuicksightOverrideParametersTransfer;
use Generated\Shared\Transfer\QuicksightOverridePermissionsAnalysisTransfer;
use Generated\Shared\Transfer\QuicksightOverridePermissionsDashboardTransfer;
use Generated\Shared\Transfer\QuicksightOverridePermissionsDataSetTransfer;
use Generated\Shared\Transfer\QuicksightOverridePermissionsDataSourceTransfer;
use Generated\Shared\Transfer\QuicksightOverridePermissionsTransfer;
use Generated\Shared\Transfer\QuicksightPermissionsTransfer;
use Generated\Shared\Transfer\QuicksightStartAssetBundleImportJobRequestTransfer;
use Generated\Shared\Transfer\QuicksightStartAssetBundleImportJobResponseTransfer;
use Generated\Shared\Transfer\QuicksightUpdateUserRequestTransfer;
use Generated\Shared\Transfer\QuicksightUpdateUserResponseTransfer;
use Generated\Shared\Transfer\QuicksightUserRegisterRequestTransfer;
use Generated\Shared\Transfer\QuicksightUserRegisterResponseTransfer;
use Generated\Shared\Transfer\QuicksightUserTransfer;
use Generated\Shared\Transfer\ResetQuicksightAnalyticsRequestTransfer;
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
     * @link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_ListUsers.html#QS-ListUsers-response-UserList
     *
     * @var string
     */
    protected const RESPONSE_KEY_USER_LIST = 'UserList';

    /**
     * @link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_GenerateEmbedUrlForRegisteredUser.html#API_GenerateEmbedUrlForRegisteredUser_ResponseSyntax
     *
     * @var string
     */
    protected const RESPONSE_KEY_EMBED_URL = 'EmbedUrl';

    /**
     * @link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_DescribeAssetBundleImportJob.html#API_DescribeAssetBundleImportJob_ResponseSyntax
     *
     * @var string
     */
    protected const RESPONSE_KEY_JOB_STATUS = 'JobStatus';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_USER_REGISTRATION_FAILED = 'Failed to register Quicksight user.';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_EMBED_URL_GENERATION_FAILED = 'Failed to generate embed URL.';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_USERS_LIST_RETRIEVE_FAILED = 'Failed to retrieve users list.';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_DESCRIBE_ASSET_BUNDLE_IMPORT_JOB_FAILED = 'Failed to sync asset bundle import job list.';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_USER_UPDATING_FAILED = 'Failed to update Quicksight user.';

    /**
     * @link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_StartAssetBundleImportJob.html#QS-StartAssetBundleImportJob-request-FailureAction
     *
     * @var string
     */
    protected const START_ASSET_BUNDLE_IMPORT_JOB_FAILURE_ACTION = 'ROLLBACK';

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig
     */
    protected AmazonQuicksightConfig $amazonQuicksightConfig;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\Mapper\AmazonQuicksightMapperInterface
     */
    protected AmazonQuicksightMapperInterface $amazonQuicksightMapper;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\Formatter\AmazonQuicksightRequestDataFormatterInterface
     */
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
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer $quicksightUserTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlResponseTransfer
     */
    public function generateEmbedUrlForRegisteredUser(
        QuicksightUserTransfer $quicksightUserTransfer
    ): QuicksightGenerateEmbedUrlResponseTransfer {
        $quicksightGenerateEmbedUrlRequestTransfer = $this->createQuicksightGenerateEmbedUrlRequestTransfer();
        $quicksightGenerateEmbedUrlRequestTransfer = $this->amazonQuicksightMapper
            ->mapQuicksightUserTransferToQuicksightGenerateEmbedUrlRequestTransfer(
                $quicksightUserTransfer,
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

        return $this->amazonQuicksightMapper
            ->mapGenerateEmbedUrlResponseDataToQuicksightGenerateEmbedUrlResponseTransfer(
                $response->toArray(),
                $quicksightGenerateEmbedUrlResponseTransfer,
            );
    }

    /**
     * @param \Generated\Shared\Transfer\QuicksightUserTransfer $quicksightUserTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightDeleteUserResponseTransfer
     */
    public function deleteUserByPrincipalId(QuicksightUserTransfer $quicksightUserTransfer): QuicksightDeleteUserResponseTransfer
    {
        $quicksightUserDeleteRequestTransfer = $this->createQuicksightUserDeleteRequestTransfer();
        $quicksightUserDeleteRequestTransfer = $this->amazonQuicksightMapper->mapQuicksightUserTransferToQuicksightDeleteUserRequestTransfer(
            $quicksightUserTransfer,
            $quicksightUserDeleteRequestTransfer,
        );

        $requestData = $this->amazonQuicksightRequestDataFormatter->formatRequestData(
            $quicksightUserDeleteRequestTransfer->toArray(true, true),
        );

        $quicksightDeleteUserResponseTransfer = new QuicksightDeleteUserResponseTransfer();
        try {
            $this->amazonQuicksightToAwsQuicksightClient->deleteUserByPrincipalId($requestData);
        } catch (QuickSightException $quickSightException) {
            return $quicksightDeleteUserResponseTransfer->addError(
                (new ErrorTransfer())->setMessage($quickSightException->getAwsErrorMessage()),
            );
        }

        return $quicksightDeleteUserResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightDeleteUserResponseTransfer
     */
    public function deleteUserByUsername(UserTransfer $userTransfer): QuicksightDeleteUserResponseTransfer
    {
        $quicksightUserDeleteRequestTransfer = $this->createQuicksightUserDeleteRequestTransfer();
        $quicksightUserDeleteRequestTransfer = $this->amazonQuicksightMapper->mapUserTransferToQuicksightDeleteUserRequestTransfer(
            $userTransfer,
            $quicksightUserDeleteRequestTransfer,
        );

        $requestData = $this->amazonQuicksightRequestDataFormatter->formatRequestData(
            $quicksightUserDeleteRequestTransfer->toArray(true, true),
        );

        $quicksightDeleteUserResponseTransfer = new QuicksightDeleteUserResponseTransfer();
        try {
            $this->amazonQuicksightToAwsQuicksightClient->deleteUser($requestData);
        } catch (QuickSightException $quickSightException) {
            return $quicksightDeleteUserResponseTransfer->addError(
                (new ErrorTransfer())->setMessage($quickSightException->getAwsErrorMessage()),
            );
        }

        return $quicksightDeleteUserResponseTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\QuicksightListUsersResponseTransfer
     */
    public function listUsers(): QuicksightListUsersResponseTransfer
    {
        $quicksightListUsersRequestDataTransfer = $this->createQuicksightListUsersRequestDataTransfer();
        $quicksightListUsersResponseTransfer = new QuicksightListUsersResponseTransfer();

        $requestData = $this->amazonQuicksightRequestDataFormatter->formatRequestData(
            $quicksightListUsersRequestDataTransfer->toArray(true, true),
        );

        try {
            $result = $this->amazonQuicksightToAwsQuicksightClient->listUsers($requestData);
        } catch (QuickSightException $quickSightException) {
            return $quicksightListUsersResponseTransfer->addError(
                (new ErrorTransfer())->setMessage($quickSightException->getAwsErrorMessage()),
            );
        }

        if (!$result->hasKey(static::RESPONSE_KEY_USER_LIST)) {
            return $quicksightListUsersResponseTransfer->addError(
                (new ErrorTransfer())->setMessage(static::ERROR_MESSAGE_USERS_LIST_RETRIEVE_FAILED),
            );
        }

        foreach ($result->get(static::RESPONSE_KEY_USER_LIST) as $quicksightUserData) {
            $quicksightUser = $this->amazonQuicksightMapper->mapQuicksightUserDataToQuicksightUserTransfer(
                $quicksightUserData,
                new QuicksightUserTransfer(),
            );

            $quicksightListUsersResponseTransfer->addQuicksightUser($quicksightUser);
        }

        return $quicksightListUsersResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightUpdateUserResponseTransfer
     */
    public function updateUser(UserTransfer $userTransfer): QuicksightUpdateUserResponseTransfer
    {
        $quicksightUpdateUserRequestTransfer = $this->createQuicksightUpdateUserRequestTransfer();
        $quicksightUpdateUserRequestTransfer = $this->amazonQuicksightMapper
            ->mapUserTransferToQuicksightUpdateUserRequestTransfer(
                $userTransfer,
                $quicksightUpdateUserRequestTransfer,
            );

        $requestData = $this->amazonQuicksightRequestDataFormatter->formatRequestData(
            $quicksightUpdateUserRequestTransfer->toArray(true, true),
        );

        $quicksightUpdateUserResponseTransfer = new QuicksightUpdateUserResponseTransfer();

        try {
            $response = $this->amazonQuicksightToAwsQuicksightClient->updateUser($requestData);
        } catch (QuickSightException $quickSightException) {
            return $quicksightUpdateUserResponseTransfer->addError(
                (new ErrorTransfer())->setMessage($quickSightException->getAwsErrorMessage()),
            );
        }

        if (!$response->hasKey(static::RESPONSE_KEY_USER)) {
            return $quicksightUpdateUserResponseTransfer->addError(
                (new ErrorTransfer())->setMessage(static::ERROR_MESSAGE_USER_UPDATING_FAILED),
            );
        }

        $quicksightUserTransfer = $this->amazonQuicksightMapper->mapQuicksightUserDataToQuicksightUserTransfer(
            $response->get(static::RESPONSE_KEY_USER),
            $userTransfer->getQuicksightUserOrFail(),
        );

        return $quicksightUpdateUserResponseTransfer->setQuicksightUser($quicksightUserTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightStartAssetBundleImportJobResponseTransfer
     */
    public function startAssetBundleImportJobByEnableQuicksightAnalyticsRequest(
        EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer
    ): QuicksightStartAssetBundleImportJobResponseTransfer {
        $quicksightStartAssetBundleImportJobRequestTransfer = $this->createQuicksightStartAssetBundleImportJobRequestTransfer();
        $quicksightStartAssetBundleImportJobRequestTransfer = $this->amazonQuicksightMapper
            ->mapEnableQuicksightAnalyticsRequestTransferToQuicksightStartAssetBundleImportJobRequestTransfer(
                $enableQuicksightAnalyticsRequestTransfer,
                $quicksightStartAssetBundleImportJobRequestTransfer,
            );

        return $this->startAssetBundleImportJob($quicksightStartAssetBundleImportJobRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\ResetQuicksightAnalyticsRequestTransfer $resetQuicksightAnalyticsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightStartAssetBundleImportJobResponseTransfer
     */
    public function startAssetBundleImportJobByResetQuicksightAnalyticsRequest(
        ResetQuicksightAnalyticsRequestTransfer $resetQuicksightAnalyticsRequestTransfer
    ): QuicksightStartAssetBundleImportJobResponseTransfer {
        $quicksightStartAssetBundleImportJobRequestTransfer = $this->createQuicksightStartAssetBundleImportJobRequestTransfer();
        $quicksightStartAssetBundleImportJobRequestTransfer = $this->amazonQuicksightMapper
            ->mapResetQuicksightAnalyticsRequestTransferToQuicksightStartAssetBundleImportJobRequestTransfer(
                $resetQuicksightAnalyticsRequestTransfer,
                $quicksightStartAssetBundleImportJobRequestTransfer,
            );

        return $this->startAssetBundleImportJob($quicksightStartAssetBundleImportJobRequestTransfer);
    }

    /**
     * @param string $assetBundleImportJobId
     *
     * @return \Generated\Shared\Transfer\QuicksightDescribeAssetBundleImportJobResponseTransfer
     */
    public function describeAssetBundleImportJob(
        string $assetBundleImportJobId
    ): QuicksightDescribeAssetBundleImportJobResponseTransfer {
        $quicksightDescribeAssetBundleImportJobRequestTransfer = $this->createQuicksightDescribeAssetBundleImportJobRequestTransfer($assetBundleImportJobId);
        $requestData = $this->amazonQuicksightRequestDataFormatter->formatRequestData(
            $quicksightDescribeAssetBundleImportJobRequestTransfer->toArray(true, true),
        );
        $quicksightDescribeAssetBundleImportJobResponseTransfer = new QuicksightDescribeAssetBundleImportJobResponseTransfer();

        try {
            $result = $this->amazonQuicksightToAwsQuicksightClient->describeAssetBundleImportJob($requestData);
        } catch (QuickSightException $quickSightException) {
            return $quicksightDescribeAssetBundleImportJobResponseTransfer->addError(
                (new ErrorTransfer())->setMessage($quickSightException->getAwsErrorMessage()),
            );
        }

        if (!$result->hasKey(static::RESPONSE_KEY_JOB_STATUS)) {
            return $quicksightDescribeAssetBundleImportJobResponseTransfer->addError(
                (new ErrorTransfer())->setMessage(static::ERROR_MESSAGE_DESCRIBE_ASSET_BUNDLE_IMPORT_JOB_FAILED),
            );
        }

        return $this->amazonQuicksightMapper->mapDescribeAssetBundleImportJobDataToQuicksightDescribeAssetBundleImportJobResponseTransfer(
            $result->toArray(),
            new QuicksightDescribeAssetBundleImportJobResponseTransfer(),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\QuicksightStartAssetBundleImportJobRequestTransfer $quicksightStartAssetBundleImportJobRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightStartAssetBundleImportJobResponseTransfer
     */
    protected function startAssetBundleImportJob(
        QuicksightStartAssetBundleImportJobRequestTransfer $quicksightStartAssetBundleImportJobRequestTransfer
    ): QuicksightStartAssetBundleImportJobResponseTransfer {
        $requestData = $this->amazonQuicksightRequestDataFormatter->formatRequestData(
            $quicksightStartAssetBundleImportJobRequestTransfer->toArray(true, true),
        );
        $quicksightStartAssetBundleImportJobResponseTransfer = new QuicksightStartAssetBundleImportJobResponseTransfer();

        try {
            $this->amazonQuicksightToAwsQuicksightClient->startAssetBundleImportJob($requestData);
        } catch (QuickSightException $quickSightException) {
            return $quicksightStartAssetBundleImportJobResponseTransfer->addError(
                (new ErrorTransfer())->setMessage($quickSightException->getAwsErrorMessage()),
            );
        }

        return $quicksightStartAssetBundleImportJobResponseTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\QuicksightUserRegisterRequestTransfer
     */
    protected function createQuicksightUserRegisterRequestTransfer(): QuicksightUserRegisterRequestTransfer
    {
        return (new QuicksightUserRegisterRequestTransfer())
            ->setIdentityType(static::QUICKSIGHT_USER_IDENTITY_TYPE)
            ->setAwsAccountId($this->amazonQuicksightConfig->getAwsAccountId())
            ->setNamespace($this->amazonQuicksightConfig->getAwsQuicksightNamespace());
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
     * @return \Generated\Shared\Transfer\QuicksightDeleteUserRequestTransfer
     */
    protected function createQuicksightUserDeleteRequestTransfer(): QuicksightDeleteUserRequestTransfer
    {
        return (new QuicksightDeleteUserRequestTransfer())
            ->setAwsAccountId($this->amazonQuicksightConfig->getAwsAccountId())
            ->setNamespace($this->amazonQuicksightConfig->getAwsQuicksightNamespace());
    }

    /**
     * @return \Generated\Shared\Transfer\QuicksightListUsersRequestTransfer
     */
    protected function createQuicksightListUsersRequestDataTransfer(): QuicksightListUsersRequestTransfer
    {
        return (new QuicksightListUsersRequestTransfer())
            ->setAwsAccountId($this->amazonQuicksightConfig->getAwsAccountId())
            ->setNamespace($this->amazonQuicksightConfig->getAwsQuicksightNamespace());
    }

    /**
     * @return \Generated\Shared\Transfer\QuicksightStartAssetBundleImportJobRequestTransfer
     */
    protected function createQuicksightStartAssetBundleImportJobRequestTransfer(): QuicksightStartAssetBundleImportJobRequestTransfer
    {
        return (new QuicksightStartAssetBundleImportJobRequestTransfer())
            ->setAwsAccountId($this->amazonQuicksightConfig->getAwsAccountId())
            ->setFailureAction(static::START_ASSET_BUNDLE_IMPORT_JOB_FAILURE_ACTION)
            ->setOverrideParameters(
                (new QuicksightOverrideParametersTransfer())->addQuicksightOverrideParametersDataSource(
                    (new QuicksightOverrideParametersDataSourceTransfer())->setCredentials(
                        (new QuicksightOverrideParametersDataSourceCredentialsTransfer())->setCredentialPair(
                            (new QuicksightOverrideParametersDataSourceCredentialPairTransfer())
                                ->setPassword($this->amazonQuicksightConfig->getDefaultDataSourcePassword())
                                ->setUsername($this->amazonQuicksightConfig->getDefaultDataSourceUsername()),
                        ),
                    )->setDataSourceId($this->amazonQuicksightConfig->getDefaultDataSourceId()),
                ),
            )
            ->setOverridePermissions(
                (new QuicksightOverridePermissionsTransfer())
                    ->addQuicksightOverridePermissionsAnalysis(
                        (new QuicksightOverridePermissionsAnalysisTransfer())
                            ->addIdAnalysis('*')
                            ->setPermissions((new QuicksightPermissionsTransfer())->setActions(
                                $this->amazonQuicksightConfig->getDefaultAnalysisPermissionsActions(),
                            )),
                    )
                    ->addQuicksightOverridePermissionsDashboard(
                        (new QuicksightOverridePermissionsDashboardTransfer())
                            ->addIdDashboard('*')
                            ->setPermissions((new QuicksightPermissionsTransfer())->setActions(
                                $this->amazonQuicksightConfig->getDefaultDashboardPermissionsActions(),
                            )),
                    )
                    ->addQuicksightOverridePermissionsDataSet(
                        (new QuicksightOverridePermissionsDataSetTransfer())
                            ->addIdDataSet('*')
                            ->setPermissions((new QuicksightPermissionsTransfer())->setActions(
                                $this->amazonQuicksightConfig->getDefaultDataSetPermissionsActions(),
                            )),
                    )
                      ->addQuicksightOverridePermissionsDataSource(
                          (new QuicksightOverridePermissionsDataSourceTransfer())
                            ->addIdDataSource('*')
                            ->setPermissions((new QuicksightPermissionsTransfer())->setActions(
                                $this->amazonQuicksightConfig->getDefaultDataSourcePermissionsActions(),
                            )),
                      ),
            );
    }

    /**
     * @param string $assetBundleImportJobId
     *
     * @return \Generated\Shared\Transfer\QuicksightDescribeAssetBundleImportJobRequestTransfer
     */
    protected function createQuicksightDescribeAssetBundleImportJobRequestTransfer(
        string $assetBundleImportJobId
    ): QuicksightDescribeAssetBundleImportJobRequestTransfer {
        return (new QuicksightDescribeAssetBundleImportJobRequestTransfer())
            ->setAssetBundleImportJobId($assetBundleImportJobId)
            ->setAwsAccountId($this->amazonQuicksightConfig->getAwsAccountId());
    }

    /**
     * @return \Generated\Shared\Transfer\QuicksightUpdateUserRequestTransfer
     */
    protected function createQuicksightUpdateUserRequestTransfer(): QuicksightUpdateUserRequestTransfer
    {
        return (new QuicksightUpdateUserRequestTransfer())
            ->setAwsAccountId($this->amazonQuicksightConfig->getAwsAccountId())
            ->setNamespace($this->amazonQuicksightConfig->getAwsQuicksightNamespace());
    }
}
