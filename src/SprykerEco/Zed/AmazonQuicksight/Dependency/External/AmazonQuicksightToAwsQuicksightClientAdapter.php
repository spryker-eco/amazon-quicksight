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
     * @param array<string, mixed> $deleteUserRequestData
     *
     * @return \Aws\ResultInterface<string, mixed>
     */
    public function deleteUserByPrincipalId(array $deleteUserRequestData): ResultInterface
    {
        return $this->quicksightClient->deleteUserByPrincipalId($deleteUserRequestData);
    }

    /**
     * @param array<string, mixed> $deleteUserRequestData
     *
     * @return \Aws\ResultInterface<string, mixed>
     */
    public function deleteUser(array $deleteUserRequestData): ResultInterface
    {
        return $this->quicksightClient->deleteUser($deleteUserRequestData);
    }

    /**
     * @param array<string, mixed> $listUsersRequestData
     *
     * @return \Aws\ResultInterface<string, mixed>
     */
    public function listUsers(array $listUsersRequestData): ResultInterface
    {
        return $this->quicksightClient->listUsers($listUsersRequestData);
    }

    /**
     * @param array<string, mixed> $updateUserRequestData
     *
     * @return \Aws\ResultInterface<string, mixed>
     */
    public function updateUser(array $updateUserRequestData): ResultInterface
    {
        return $this->quicksightClient->updateUser($updateUserRequestData);
    }

    /**
     * @param array<string, mixed> $startAssetBundleImportJobRequestData
     *
     * @return \Aws\ResultInterface<string, mixed>
     */
    public function startAssetBundleImportJob(array $startAssetBundleImportJobRequestData): ResultInterface
    {
        return $this->quicksightClient->startAssetBundleImportJob($startAssetBundleImportJobRequestData);
    }

    /**
     * @param array<string, mixed> $describeAssetBundleImportJobRequestData
     *
     * @return \Aws\ResultInterface<string, mixed>
     */
    public function describeAssetBundleImportJob(array $describeAssetBundleImportJobRequestData): ResultInterface
    {
        return $this->quicksightClient->describeAssetBundleImportJob($describeAssetBundleImportJobRequestData);
    }
}
