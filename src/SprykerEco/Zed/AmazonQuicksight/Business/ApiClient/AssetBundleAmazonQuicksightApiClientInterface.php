<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\ApiClient;

use Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer;
use Generated\Shared\Transfer\QuicksightDeleteDataSetResponseTransfer;
use Generated\Shared\Transfer\QuicksightDescribeAssetBundleImportJobResponseTransfer;
use Generated\Shared\Transfer\QuicksightStartAssetBundleImportJobResponseTransfer;
use Generated\Shared\Transfer\ResetQuicksightAnalyticsRequestTransfer;

interface AssetBundleAmazonQuicksightApiClientInterface
{
    /**
     * @param \Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightStartAssetBundleImportJobResponseTransfer
     */
    public function startAssetBundleImportJobByEnableQuicksightAnalyticsRequest(
        EnableQuicksightAnalyticsRequestTransfer $enableQuicksightAnalyticsRequestTransfer
    ): QuicksightStartAssetBundleImportJobResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\ResetQuicksightAnalyticsRequestTransfer $resetQuicksightAnalyticsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuicksightStartAssetBundleImportJobResponseTransfer
     */
    public function startAssetBundleImportJobByResetQuicksightAnalyticsRequest(
        ResetQuicksightAnalyticsRequestTransfer $resetQuicksightAnalyticsRequestTransfer
    ): QuicksightStartAssetBundleImportJobResponseTransfer;

    /**
     * @param string $assetBundleImportJobId
     *
     * @return \Generated\Shared\Transfer\QuicksightDescribeAssetBundleImportJobResponseTransfer
     */
    public function describeAssetBundleImportJob(
        string $assetBundleImportJobId
    ): QuicksightDescribeAssetBundleImportJobResponseTransfer;

    /**
     * @param string $idDataSet
     * @param list<string> $errorCodesToIgnore
     *
     * @return \Generated\Shared\Transfer\QuicksightDeleteDataSetResponseTransfer
     */
    public function deleteDataSet(string $idDataSet, array $errorCodesToIgnore = []): QuicksightDeleteDataSetResponseTransfer;
}
