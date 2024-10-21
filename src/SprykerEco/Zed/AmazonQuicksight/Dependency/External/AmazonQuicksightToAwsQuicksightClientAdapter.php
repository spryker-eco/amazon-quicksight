<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Dependency\External;

use Aws\Credentials\Credentials;
use Aws\QuickSight\QuickSightClient;
use Aws\ResultInterface;
use Aws\Sts\StsClient;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig;

class AmazonQuicksightToAwsQuicksightClientAdapter implements AmazonQuicksightToAwsQuicksightClientInterface
{
    /**
     * @var \Aws\QuickSight\QuickSightClient
     */
    protected $quicksightClient;

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig $config
     */
    public function __construct(AmazonQuicksightConfig $config)
    {
        $this->quicksightClient = new QuickSightClient($this->getQuicksightClientConfiguration($config));
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

    /**
     * @param array<string, mixed> $deleteDataSetRequestData
     *
     * @return \Aws\ResultInterface<string, mixed>
     */
    public function deleteDataSet(array $deleteDataSetRequestData): ResultInterface
    {
        return $this->quicksightClient->deleteDataSet($deleteDataSetRequestData);
    }

    /**
     * @link https://docs.aws.amazon.com/aws-sdk-php/v3/api/class-Aws.AwsClient.html#method___construct
     *
     * @param \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig $config
     *
     * @return array<string, mixed>
     */
    protected function getQuicksightClientConfiguration(AmazonQuicksightConfig $config): array
    {
        $quicksightClientConfiguration = [
            'region' => $config->getAwsRegion(),
            'version' => $config->getQuicksightApiVersion(),
        ];

        $awsCredentialsKey = $config->findAwsCredentialsKey();
        $awsCredentialsSecret = $config->findAwsCredentialsSecret();
        $awsCredentialsToken = $config->findAwsCredentialsToken();

        if ($awsCredentialsKey && $awsCredentialsSecret && $awsCredentialsToken) {
            $quicksightClientConfiguration['credentials'] = new Credentials(
                $awsCredentialsKey,
                $awsCredentialsSecret,
                $awsCredentialsToken,
            );

            return $quicksightClientConfiguration;
        }

        $quicksightAssumedRoleArn = $config->getQuicksightAssumedRoleArn();

        if ($quicksightAssumedRoleArn) {
            $quicksightClientConfiguration['credentials'] = $this->getStsClientCredentials($config, $quicksightAssumedRoleArn);
        }

        return $quicksightClientConfiguration;
    }

    /**
     * @param \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig $config
     * @param string $quicksightAssumedRoleArn
     *
     * @return \Aws\Credentials\Credentials
     */
    protected function getStsClientCredentials(
        AmazonQuicksightConfig $config,
        string $quicksightAssumedRoleArn
    ): Credentials {
        $stsClient = new StsClient([
            'region' => $config->getAwsRegion(),
            'version' => $config->getStsClientVersion(),
        ]);

        $result = $stsClient->AssumeRole([
            'RoleArn' => $quicksightAssumedRoleArn,
            'RoleSessionName' => $config->getStsClientRoleSessionName(),
        ]);

        return new Credentials(
            $result['Credentials']['AccessKeyId'],
            $result['Credentials']['SecretAccessKey'],
            $result['Credentials']['SessionToken'],
        );
    }
}
