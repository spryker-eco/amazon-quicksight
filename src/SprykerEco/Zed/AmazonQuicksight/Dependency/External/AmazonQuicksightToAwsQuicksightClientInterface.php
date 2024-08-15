<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Dependency\External;

use Aws\ResultInterface;

interface AmazonQuicksightToAwsQuicksightClientInterface
{
    /**
     * @param array<string, mixed> $registerUserRequestData
     *
     * @return \Aws\ResultInterface<string, mixed>
     */
    public function registerUser(array $registerUserRequestData): ResultInterface;

    /**
     * @param array<string, mixed> $generateEmbedUrlRequestData
     *
     * @return \Aws\ResultInterface<string, mixed>
     */
    public function generateEmbedUrlForRegisteredUser(array $generateEmbedUrlRequestData): ResultInterface;

    /**
     * @param array $data
     *
     * @return \Aws\ResultInterface
     */
    public function listDashboards(array $data): ResultInterface;

    /**
     * @param array $data
     *
     * @return \Aws\ResultInterface
     */
    public function listAnalyses(array $data): ResultInterface;

    /**
     * @param array $data
     *
     * @return \Aws\ResultInterface
     */
    public function listDataSets(array $data): ResultInterface;

    /**
     * @param array $data
     *
     * @return \Aws\ResultInterface
     */
    public function listDataSources(array $data): ResultInterface;

    /**
     * @param array $data
     *
     * @return \Aws\ResultInterface
     */
    public function startAssetBundleExportJob(array $data): ResultInterface;

    /**
     * @param array $data
     *
     * @return \Aws\ResultInterface
     */
    public function listAssetBundleExportJobs(array $data): ResultInterface;

    /**
     * @param array $data
     *
     * @return \Aws\ResultInterface
     */
    public function describeAssetBundleExportJob(array $data): ResultInterface;
}
