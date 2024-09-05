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
    protected const QUICKSIGHT_REGISTER_USER_NAMESPACE = 'spryker-test';

    /**
     * @var string
     */
    protected const QUICKSIGHT_USER_ROLE_READER = 'READER';

    /**
     * @var string
     */
    protected const QUICKSIGHT_USER_ROLE_AUTHOR = 'AUTHOR';

    /**
     * @uses \Orm\Zed\User\Persistence\Map\SpyUserTableMap::COL_STATUS_ACTIVE
     *
     * @var string
     */
    protected const USER_STATUS_ACTIVE = 'active';

    /**
     * @uses \Orm\Zed\User\Persistence\Map\SpyUserTableMap::COL_STATUS_BLOCKED
     *
     * @var string
     */
    protected const USER_STATUS_BLOCKED = 'blocked';

    /**
     * @uses \Orm\Zed\User\Persistence\Map\SpyUserTableMap::COL_STATUS_DELETED
     *
     * @var string
     */
    protected const USER_STATUS_DELETED = 'deleted';

    /**
     * @var string
     */
    protected const QUICKSIGHT_CONSOLE_INITIAL_PATH = '/start';

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
     * - Returns the name of the Quicksight namespace.
     *
     * @api
     *
     * @return string
     */
    public function getQuicksightNamespace(): string
    {
        return $this->get(AmazonQuicksightConstants::AWS_QUICKSIGHT_NAMESPACE);
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

    /**
     * Specification:
     * - Provides the starting path for the QuickSight console.
     *
     * @api
     *
     * @return string
     */
    public function getQuicksightConsoleInitialPath(): string
    {
        return static::QUICKSIGHT_CONSOLE_INITIAL_PATH;
    }

    /**
     * Specification:
     * - Returns a list of user statuses that allows users to be registered in Quicksight.
     *
     * @api
     *
     * @return list<string>
     */
    public function getUserStatusesApplicableForQuicksightUserRegistration(): array
    {
        return [
            static::USER_STATUS_ACTIVE,
        ];
    }

    /**
     * Specification:
     * - Returns a list of user statuses that allows user deletion from Quicksight.
     *
     * @api
     *
     * @return list<string>
     */
    public function getUserStatusesApplicableForQuicksightUserDeletion(): array
    {
        return [
            static::USER_STATUS_BLOCKED,
            static::USER_STATUS_DELETED,
        ];
    }
}
