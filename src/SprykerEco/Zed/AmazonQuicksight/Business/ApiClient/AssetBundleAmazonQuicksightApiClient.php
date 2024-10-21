<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\ApiClient;

use Aws\QuickSight\Exception\QuickSightException;
use Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer;
use Generated\Shared\Transfer\ErrorTransfer;
use Generated\Shared\Transfer\QuicksightDeleteDataSetRequestTransfer;
use Generated\Shared\Transfer\QuicksightDeleteDataSetResponseTransfer;
use Generated\Shared\Transfer\QuicksightDescribeAssetBundleImportJobRequestTransfer;
use Generated\Shared\Transfer\QuicksightDescribeAssetBundleImportJobResponseTransfer;
use Generated\Shared\Transfer\QuicksightOverrideParametersDataSourceCredentialPairTransfer;
use Generated\Shared\Transfer\QuicksightOverrideParametersDataSourceCredentialsTransfer;
use Generated\Shared\Transfer\QuicksightOverrideParametersDataSourceMariaDbParametersTransfer;
use Generated\Shared\Transfer\QuicksightOverrideParametersDataSourceParametersTransfer;
use Generated\Shared\Transfer\QuicksightOverrideParametersDataSourceTransfer;
use Generated\Shared\Transfer\QuicksightOverrideParametersDataSourceVpcConnectionPropertiesTransfer;
use Generated\Shared\Transfer\QuicksightOverrideParametersTransfer;
use Generated\Shared\Transfer\QuicksightOverridePermissionsAnalysisTransfer;
use Generated\Shared\Transfer\QuicksightOverridePermissionsDashboardTransfer;
use Generated\Shared\Transfer\QuicksightOverridePermissionsDataSetTransfer;
use Generated\Shared\Transfer\QuicksightOverridePermissionsDataSourceTransfer;
use Generated\Shared\Transfer\QuicksightOverridePermissionsTransfer;
use Generated\Shared\Transfer\QuicksightPermissionsTransfer;
use Generated\Shared\Transfer\QuicksightStartAssetBundleImportJobRequestTransfer;
use Generated\Shared\Transfer\QuicksightStartAssetBundleImportJobResponseTransfer;
use Generated\Shared\Transfer\ResetQuicksightAnalyticsRequestTransfer;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig;
use SprykerEco\Zed\AmazonQuicksight\Business\Formatter\AmazonQuicksightRequestDataFormatterInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Mapper\AmazonQuicksightMapperInterface;
use SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface;

class AssetBundleAmazonQuicksightApiClient implements AssetBundleAmazonQuicksightApiClientInterface
{
    /**
     * @link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_StartAssetBundleImportJob.html#QS-StartAssetBundleImportJob-request-FailureAction
     *
     * @var string
     */
    protected const START_ASSET_BUNDLE_IMPORT_JOB_FAILURE_ACTION = 'ROLLBACK';

    /**
     * @link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_DescribeAssetBundleImportJob.html#API_DescribeAssetBundleImportJob_ResponseSyntax
     *
     * @var string
     */
    protected const RESPONSE_KEY_JOB_STATUS = 'JobStatus';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_DESCRIBE_ASSET_BUNDLE_IMPORT_JOB_FAILED = 'Failed to sync asset bundle import job list.';

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
     * @param string $idDataSet
     * @param list<string> $errorCodesToIgnore
     *
     * @return \Generated\Shared\Transfer\QuicksightDeleteDataSetResponseTransfer
     */
    public function deleteDataSet(string $idDataSet, array $errorCodesToIgnore = []): QuicksightDeleteDataSetResponseTransfer
    {
        $quicksightDeleteDataSetRequestTransfer = (new QuicksightDeleteDataSetRequestTransfer())
            ->setAwsAccountId($this->amazonQuicksightConfig->getAwsAccountId())
            ->setDataSetId($idDataSet);
        $requestData = $this->amazonQuicksightRequestDataFormatter->formatRequestData(
            $quicksightDeleteDataSetRequestTransfer->toArray(true, true),
        );
        $quicksightDeleteDataSetResponseTransfer = new QuicksightDeleteDataSetResponseTransfer();

        try {
            $this->amazonQuicksightToAwsQuicksightClient->deleteDataSet($requestData);
        } catch (QuickSightException $quickSightException) {
            if (!in_array($quickSightException->getAwsErrorCode(), $errorCodesToIgnore, true)) {
                return $quicksightDeleteDataSetResponseTransfer->addError(
                    (new ErrorTransfer())->setMessage($quickSightException->getAwsErrorMessage()),
                );
            }
        }

        return $quicksightDeleteDataSetResponseTransfer;
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
                    )->setDataSourceId($this->amazonQuicksightConfig->getDefaultDataSourceId())
                        ->setDataSourceParameters(
                            (new QuicksightOverrideParametersDataSourceParametersTransfer())->setMariaDbParameters(
                                (new QuicksightOverrideParametersDataSourceMariaDbParametersTransfer())
                                    ->setDatabase($this->amazonQuicksightConfig->getDefaultDataSourceDatabaseName())
                                    ->setHost($this->amazonQuicksightConfig->getDefaultDataSourceDatabaseHost())
                                    ->setPort($this->amazonQuicksightConfig->getDefaultDataSourceDatabasePort()),
                            ),
                        )
                        ->setVpcConnectionProperties(
                            (new QuicksightOverrideParametersDataSourceVpcConnectionPropertiesTransfer())
                                ->setVpcConnectionArn($this->amazonQuicksightConfig->getDefaultDataSourceVpcConnectionArn()),
                        ),
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
}
