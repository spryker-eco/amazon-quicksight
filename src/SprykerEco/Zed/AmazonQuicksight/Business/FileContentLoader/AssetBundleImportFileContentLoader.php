<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\FileContentLoader;

use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig;
use SprykerEco\Zed\AmazonQuicksight\Business\Exception\AssetBundleImportFileReadFailureException;

class AssetBundleImportFileContentLoader implements AssetBundleImportFileContentLoaderInterface
{
    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig
     */
    protected AmazonQuicksightConfig $amazonQuicksightConfig;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig $amazonQuicksightConfig
     */
    public function __construct(AmazonQuicksightConfig $amazonQuicksightConfig)
    {
        $this->amazonQuicksightConfig = $amazonQuicksightConfig;
    }

    /**
     * @throws \SprykerEco\Zed\AmazonQuicksight\Business\Exception\AssetBundleImportFileReadFailureException
     *
     * @return string
     */
    public function getAssetBundleImportFileContent(): string
    {
        $assetBundleImportFileContent = file_get_contents($this->amazonQuicksightConfig->getAssetBundleImportFilePath());

        if ($assetBundleImportFileContent === false) {
            throw new AssetBundleImportFileReadFailureException('Failed to read asset bundle import file content.');
        }

        return $assetBundleImportFileContent;
    }
}
