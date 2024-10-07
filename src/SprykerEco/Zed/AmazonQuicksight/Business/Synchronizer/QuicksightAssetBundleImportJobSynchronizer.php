<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Synchronizer;

use Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig;
use SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AssetBundleAmazonQuicksightApiClientInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Mapper\AmazonQuicksightMapperInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Validator\QuicksightAnalyticsRequestValidatorInterface;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface;

class QuicksightAssetBundleImportJobSynchronizer implements QuicksightAssetBundleImportJobSynchronizerInterface
{
    /**
     * @var string
     */
    protected const ASSET_BUNDLE_IMPORT_JOB_STATUS_SUCCESSFUL = 'SUCCESSFUL';

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AssetBundleAmazonQuicksightApiClientInterface
     */
    protected AssetBundleAmazonQuicksightApiClientInterface $assetBundleAmazonQuicksightApiClient;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig
     */
    protected AmazonQuicksightConfig $amazonQuicksightConfig;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface
     */
    protected AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface
     */
    protected AmazonQuicksightRepositoryInterface $amazonQuicksightRepository;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\Validator\QuicksightAnalyticsRequestValidatorInterface
     */
    protected QuicksightAnalyticsRequestValidatorInterface $quicksightAnalyticsRequestValidator;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\Mapper\AmazonQuicksightMapperInterface
     */
    protected AmazonQuicksightMapperInterface $amazonQuicksightMapper;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AssetBundleAmazonQuicksightApiClientInterface $assetBundleAmazonQuicksightApiClient
     * @param \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig $amazonQuicksightConfig
     * @param \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager
     * @param \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface $amazonQuicksightRepository
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Validator\QuicksightAnalyticsRequestValidatorInterface $quicksightAnalyticsRequestValidator
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Mapper\AmazonQuicksightMapperInterface $amazonQuicksightMapper
     */
    public function __construct(
        AssetBundleAmazonQuicksightApiClientInterface $assetBundleAmazonQuicksightApiClient,
        AmazonQuicksightConfig $amazonQuicksightConfig,
        AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager,
        AmazonQuicksightRepositoryInterface $amazonQuicksightRepository,
        QuicksightAnalyticsRequestValidatorInterface $quicksightAnalyticsRequestValidator,
        AmazonQuicksightMapperInterface $amazonQuicksightMapper
    ) {
        $this->assetBundleAmazonQuicksightApiClient = $assetBundleAmazonQuicksightApiClient;
        $this->amazonQuicksightConfig = $amazonQuicksightConfig;
        $this->amazonQuicksightEntityManager = $amazonQuicksightEntityManager;
        $this->amazonQuicksightRepository = $amazonQuicksightRepository;
        $this->quicksightAnalyticsRequestValidator = $quicksightAnalyticsRequestValidator;
        $this->amazonQuicksightMapper = $amazonQuicksightMapper;
    }

    /**
     * @return \Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer|null
     */
    public function findSyncedDefaultQuicksightAssetBundleImportJob(): ?QuicksightAssetBundleImportJobTransfer
    {
        $defaultAssetBundleImportJobId = $this->amazonQuicksightConfig->getDefaultAssetBundleImportJobId();

        $quicksightAssetBundleImportJobTransfer = $this->amazonQuicksightRepository->findQuicksightAssetBundleImportJobByJobId(
            $defaultAssetBundleImportJobId,
        );

        if (!$this->quicksightAnalyticsRequestValidator->isAssetBundleInitializationInProgress($quicksightAssetBundleImportJobTransfer)) {
            return $quicksightAssetBundleImportJobTransfer;
        }

        if ($quicksightAssetBundleImportJobTransfer === null) {
            return null;
        }

        $quicksightDescribeAssetBundleImportJobResponseTransfer = $this->assetBundleAmazonQuicksightApiClient
            ->describeAssetBundleImportJob($defaultAssetBundleImportJobId);

        if ($quicksightDescribeAssetBundleImportJobResponseTransfer->getJobStatus() === null) {
            $quicksightDescribeAssetBundleImportJobResponseTransfer->setJobStatus(
                $quicksightAssetBundleImportJobTransfer->getStatusOrFail(),
            );
        }

        $quicksightAssetBundleImportJobTransfer = $this->amazonQuicksightMapper
            ->mapQuicksightDescribeAssetBundleImportJobResponseTransferToQuicksightAssetBundleImportJobTransfer(
                $quicksightDescribeAssetBundleImportJobResponseTransfer,
                $quicksightAssetBundleImportJobTransfer,
            );

        if ($quicksightAssetBundleImportJobTransfer->getStatus() === static::ASSET_BUNDLE_IMPORT_JOB_STATUS_SUCCESSFUL) {
            $quicksightAssetBundleImportJobTransfer->setIsInitialized(true);
        }

        return $this->amazonQuicksightEntityManager
            ->updateQuicksightAssetBundleImportJob($quicksightAssetBundleImportJobTransfer);
    }
}
