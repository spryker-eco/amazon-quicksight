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
     * @var string
     */
    protected const QUICKSIGHT_USER_ROLE_READER = 'READER';

    /**
     * @var string
     */
    protected const QUICKSIGHT_USER_ROLE_AUTHOR = 'AUTHOR';

    /**
     * Specification:
     * - Returns the list of available Quicksight user roles.
     * - The list of available roles can be found here: {@link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_User.html#QS-Type-User-Role}.
     *
     * @api
     *
     * @return list<string>
     */
    public function getQuicksightUserRoles(): array
    {
        return [
            static::QUICKSIGHT_USER_ROLE_READER,
            static::QUICKSIGHT_USER_ROLE_AUTHOR,
        ];
    }

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

    /**
     * Specification:
     * - Defines if updating quicksight user role via user form is enabled.
     *
     * @api
     *
     * @return bool
     */
    public function isQuicksightUserRoleUpdateEnabled(): bool
    {
        return false;
    }
}
