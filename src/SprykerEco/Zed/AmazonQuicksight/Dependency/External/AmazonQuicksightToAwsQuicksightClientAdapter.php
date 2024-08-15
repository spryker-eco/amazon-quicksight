<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Dependency\External;

use Aws\QuickSight\QuickSightClient;
use Aws\ResultInterface;

class AmazonQuicksightToAwsQuicksightClientAdapter implements AmazonQuicksightToAwsQuicksightClientInterface
{
    /**
     * @var \Aws\QuickSight\QuickSightClient
     */
    protected $quicksightClient;

    /**
     * @param array<string, mixed> $args
     */
    public function __construct(array $args)
    {
        $this->quicksightClient = new QuickSightClient($args);
    }

    /**
     * @param array<string, mixed> $registerUserRequestData
     *
     * @return \Aws\ResultInterface<string, mixed>
     */
    public function registerUser(array $registerUserRequestData): ResultInterface
    {
        return $this->quicksightClient->registerUser($registerUserRequestData);
    }

    /**
     * @param array<string, mixed> $generateEmbedUrlRequestData
     *
     * @return \Aws\ResultInterface<string, mixed>
     */
    public function generateEmbedUrlForRegisteredUser(array $generateEmbedUrlRequestData): ResultInterface
    {
        return $this->quicksightClient->generateEmbedUrlForRegisteredUser($generateEmbedUrlRequestData);
    }

    /**
     * @param array $data
     *
     * @return \Aws\ResultInterface
     */
    public function listDashboards(array $data): ResultInterface
    {
        return $this->quicksightClient->listDashboards($data);
    }

    /**
     * @param array $data
     *
     * @return \Aws\ResultInterface
     */
    public function listAnalyses(array $data): ResultInterface
    {
        return $this->quicksightClient->listAnalyses($data);
    }

    /**
     * @param array $data
     *
     * @return \Aws\ResultInterface
     */
    public function listDataSets(array $data): ResultInterface
    {
        return $this->quicksightClient->listDataSets($data);
    }

    /**
     * @param array $data
     *
     * @return \Aws\ResultInterface
     */
    public function listDataSources(array $data): ResultInterface
    {
        return $this->quicksightClient->listDataSources($data);
    }

    /**
     * @param array $data
     *
     * @return \Aws\ResultInterface
     */
    public function startAssetBundleExportJob(array $data): ResultInterface
    {
        return $this->quicksightClient->startAssetBundleExportJob($data);
    }

    /**
     * @param array $data
     *
     * @return \Aws\ResultInterface
     */
    public function listAssetBundleExportJobs(array $data): ResultInterface
    {
        return $this->quicksightClient->listAssetBundleExportJobs($data);
    }

    /**
     * @param array $data
     *
     * @return \Aws\ResultInterface
     */
    public function describeAssetBundleExportJob(array $data): ResultInterface
    {
        return $this->quicksightClient->describeAssetBundleExportJob($data);
    }
}
