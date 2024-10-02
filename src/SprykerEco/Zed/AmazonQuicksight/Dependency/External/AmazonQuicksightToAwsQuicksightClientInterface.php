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
     * @param array<string, mixed> $deleteUserRequestData
     *
     * @return \Aws\ResultInterface<string, mixed>
     */
    public function deleteUserByPrincipalId(array $deleteUserRequestData): ResultInterface;

    /**
     * @param array<string, mixed> $deleteUserRequestData
     *
     * @return \Aws\ResultInterface<string, mixed>
     */
    public function deleteUser(array $deleteUserRequestData): ResultInterface;

    /**
     * @param array<string, mixed> $listUsersRequestData
     *
     * @return \Aws\ResultInterface<string, mixed>
     */
    public function listUsers(array $listUsersRequestData): ResultInterface;

    /**
     * @param array<string, mixed> $updateUserRequestData
     *
     * @return \Aws\ResultInterface<string, mixed>
     */
    public function updateUser(array $updateUserRequestData): ResultInterface;

    /**
     * @param array<string, mixed> $startAssetBundleImportJobRequestData
     *
     * @return \Aws\ResultInterface<string, mixed>
     */
    public function startAssetBundleImportJob(array $startAssetBundleImportJobRequestData): ResultInterface;

    /**
     * @param array<string, mixed> $describeAssetBundleImportJobRequestData
     *
     * @return \Aws\ResultInterface<string, mixed>
     */
    public function describeAssetBundleImportJob(array $describeAssetBundleImportJobRequestData): ResultInterface;
}
