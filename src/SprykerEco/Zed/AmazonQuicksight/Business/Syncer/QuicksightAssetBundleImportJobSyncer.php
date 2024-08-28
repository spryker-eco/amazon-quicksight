<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Syncer;

use Generated\Shared\Transfer\QuicksightAssetBundleImportJobTransfer;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig;
use SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Mapper\AmazonQuicksightMapperInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Validator\QuicksightAnalyticsRequestValidatorInterface;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface;
use SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface;

class QuicksightAssetBundleImportJobSyncer implements QuicksightAssetBundleImportJobSyncerInterface
{
    /**
     * @var string
     */
    protected const ASSET_BUNDLE_IMPORT_JOB_STATUS_SUCCESSFUL = 'SUCCESSFUL';

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface
     */
    protected AmazonQuicksightApiClientInterface $amazonQuicksightApiClient;

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
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClientInterface $amazonQuicksightApiClient
     * @param \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig $amazonQuicksightConfig
     * @param \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager
     * @param \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface $amazonQuicksightRepository
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Validator\QuicksightAnalyticsRequestValidatorInterface $quicksightAnalyticsRequestValidator
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Mapper\AmazonQuicksightMapperInterface $amazonQuicksightMapper
     */
    public function __construct(
        AmazonQuicksightApiClientInterface $amazonQuicksightApiClient,
        AmazonQuicksightConfig $amazonQuicksightConfig,
        AmazonQuicksightEntityManagerInterface $amazonQuicksightEntityManager,
        AmazonQuicksightRepositoryInterface $amazonQuicksightRepository,
        QuicksightAnalyticsRequestValidatorInterface $quicksightAnalyticsRequestValidator,
        AmazonQuicksightMapperInterface $amazonQuicksightMapper
    ) {
        $this->amazonQuicksightApiClient = $amazonQuicksightApiClient;
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

        $quicksightDescribeAssetBundleImportJobResponseTransfer = $this->amazonQuicksightApiClient
            ->describeAssetBundleImportJob($defaultAssetBundleImportJobId);

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
