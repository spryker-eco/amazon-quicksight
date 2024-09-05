<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\FileContentLoader;

use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig;

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
     * @return string
     */
    public function getAssetBundleImportFileContent(): string
    {
        return file_get_contents($this->amazonQuicksightConfig->getAssetBundleImportFilePath());
    }
}
