<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business\FileContentLoader;

interface AssetBundleImportFileContentLoaderInterface
{
    /**
     * @return string
     */
    public function getAssetBundleImportFileContent(): string;
}
