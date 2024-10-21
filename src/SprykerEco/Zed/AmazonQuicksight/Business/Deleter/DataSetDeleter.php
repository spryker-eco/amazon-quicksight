<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\Deleter;

use Generated\Shared\Transfer\QuicksightDeleteAssetBundleDataSetsResponseTransfer;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig;
use SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AssetBundleAmazonQuicksightApiClientInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Mapper\AmazonQuicksightMapperInterface;

class DataSetDeleter implements DataSetDeleterInterface
{
    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig
     */
    protected AmazonQuicksightConfig $amazonQuicksightConfig;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AssetBundleAmazonQuicksightApiClientInterface
     */
    protected AssetBundleAmazonQuicksightApiClientInterface $assetBundleAmazonQuicksightApiClient;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Business\Mapper\AmazonQuicksightMapperInterface
     */
    protected AmazonQuicksightMapperInterface $amazonQuicksightMapper;

    /**
     * @var list<string>
     */
    protected const ERROR_CODES_TO_IGNORE = [
        'ResourceNotFoundException',
    ];

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig $amazonQuicksightConfig
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AssetBundleAmazonQuicksightApiClientInterface $assetBundleAmazonQuicksightApiClient
     * @param \SprykerEco\Zed\AmazonQuicksight\Business\Mapper\AmazonQuicksightMapperInterface $amazonQuicksightMapper
     */
    public function __construct(
        AmazonQuicksightConfig $amazonQuicksightConfig,
        AssetBundleAmazonQuicksightApiClientInterface $assetBundleAmazonQuicksightApiClient,
        AmazonQuicksightMapperInterface $amazonQuicksightMapper
    ) {
        $this->amazonQuicksightConfig = $amazonQuicksightConfig;
        $this->assetBundleAmazonQuicksightApiClient = $assetBundleAmazonQuicksightApiClient;
        $this->amazonQuicksightMapper = $amazonQuicksightMapper;
    }

    /**
     * @return \Generated\Shared\Transfer\QuicksightDeleteAssetBundleDataSetsResponseTransfer
     */
    public function deleteAssetBundleDataSets(): QuicksightDeleteAssetBundleDataSetsResponseTransfer
    {
        $dataSetIds = $this->amazonQuicksightConfig->getAssetBundleImportDeleteDataSetIds();
        $quicksightDeleteAssetBundleDataSetsResponseTransfer = new QuicksightDeleteAssetBundleDataSetsResponseTransfer();

        foreach ($dataSetIds as $idDataSet) {
            $quicksightDeleteDataSetResponseTransfer = $this->assetBundleAmazonQuicksightApiClient
                ->deleteDataSet($idDataSet, static::ERROR_CODES_TO_IGNORE);

            if ($quicksightDeleteDataSetResponseTransfer->getErrors()->count() !== 0) {
                $quicksightDeleteAssetBundleDataSetsResponseTransfer = $this->amazonQuicksightMapper->mapQuicksightDeleteDataSetResponseTransferToQuicksightDeleteAssetBundleDataSetsResponseTransfer(
                    $quicksightDeleteDataSetResponseTransfer,
                    $quicksightDeleteAssetBundleDataSetsResponseTransfer,
                );
            }
        }

        return $quicksightDeleteAssetBundleDataSetsResponseTransfer;
    }
}
