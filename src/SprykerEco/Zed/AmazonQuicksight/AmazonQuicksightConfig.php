<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight;

use Aws\Credentials\Credentials;
use Spryker\Zed\Kernel\AbstractBundleConfig;
use SprykerEco\Shared\AmazonQuicksight\AmazonQuicksightConstants;

class AmazonQuicksightConfig extends AbstractBundleConfig
{
    /**
     * @var string
     */
    protected const QUICKSIGHT_USER_REGISTER_NAMESPACE = 'default';

    /**
     * Specification:
     * - Returns the ID for the AWS account that contains your Amazon QuickSight account.
     *
     * @api
     *
     * @return string
     */
    public function getAwsAccountId(): string
    {
        return $this->get(AmazonQuicksightConstants::AWS_ACCOUNT_ID);
    }

    /**
     * Specification:
     * - Returns the namespace for the Quicksight user registration.
     *
     * @api
     *
     * @return string
     */
    public function getQuicksightUserRegisterNamespace(): string
    {
        return static::QUICKSIGHT_USER_REGISTER_NAMESPACE;
    }

    /**
     * Specification:
     * - Provides configuration for the Quicksight client.
     *
     * @link https://docs.aws.amazon.com/aws-sdk-php/v3/api/class-Aws.AwsClient.html#method___construct
     *
     * @api
     *
     * @return array<string, mixed>
     */
    public function getQuicksightClientConfiguration(): array
    {
        $quicksightClientConfiguration = [
            'region' => $this->get(AmazonQuicksightConstants::AWS_REGION),
        ];

        $awsCredentialsKey = $this->get(AmazonQuicksightConstants::AWS_CREDENTIALS_KEY);
        $awsCredentialsSecret = $this->get(AmazonQuicksightConstants::AWS_CREDENTIALS_SECRET);
        $awsCredentialsToken = $this->get(AmazonQuicksightConstants::AWS_CREDENTIALS_TOKEN);

        if ($awsCredentialsKey && $awsCredentialsSecret && $awsCredentialsToken) {
            $quicksightClientConfiguration['credentials'] = new Credentials(
                $awsCredentialsKey,
                $awsCredentialsSecret,
                $awsCredentialsToken,
            );
        }

        return $quicksightClientConfiguration;
    }
}
