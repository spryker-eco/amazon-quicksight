<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight;

use Aws\Credentials\Credentials;
use Spryker\Zed\Kernel\AbstractBundleConfig;
use SprykerEco\Shared\AmazonQuicksight\AmazonQuicksightConstants;
use SprykerEco\Zed\AmazonQuicksight\Business\Exception\AssetBundleImportFilePathNotDefinedException;

class AmazonQuicksightConfig extends AbstractBundleConfig
{
    /**
     * @var string
     */
    protected const QUICKSIGHT_REGISTER_USER_NAMESPACE = 'default';

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
     * @var string
     */
    protected const DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID = 'defaultAssetBundleImportJobId';

    /**
     * @var string
     */
    protected const ASSET_BUNDLE_IMPORT_JOB_STATUS_SUCCESSFUL = 'SUCCESSFUL';

    /**
     * @var string
     */
    protected const ASSET_BUNDLE_IMPORT_JOB_STATUS_FAILED = 'FAILED';

    /**
     * @var string
     */
    protected const ASSET_BUNDLE_IMPORT_JOB_STATUS_FAILED_ROLLBACK_COMPLETED = 'FAILED_ROLLBACK_COMPLETED';

    /**
     * @var string
     */
    protected const ASSET_BUNDLE_IMPORT_JOB_STATUS_FAILED_ROLLBACK_ERROR = 'FAILED_ROLLBACK_ERROR';

    /**
     * @link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_DescribeAssetBundleImportJob.html#API_DescribeAssetBundleImportJob_ResponseSyntax
     *
     * @var string
     */
    protected const DEFAULT_NEW_ASSET_BUNDLE_IMPORT_JOB_STATUS = 'QUEUED_FOR_IMMEDIATE_EXECUTION';

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
    public function getQuicksightRegisterUserNamespace(): string
    {
        return static::QUICKSIGHT_REGISTER_USER_NAMESPACE;
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

    /**
     * Specification:
     * - Returns the default asset bundle import job ID.
     *
     * @api
     *
     * @return string
     */
    public function getDefaultAssetBundleImportJobId(): string
    {
        return static::DEFAULT_ASSET_BUNDLE_IMPORT_JOB_ID;
    }

    /**
     * Specification:
     * - Returns the list of statuses that indicate the completion of the asset bundle import job.
     *
     * @api
     *
     * @return list<string>
     */
    public function getAssetBundleImportJobCompletionStatuses(): array
    {
        return [
            static::ASSET_BUNDLE_IMPORT_JOB_STATUS_SUCCESSFUL,
            static::ASSET_BUNDLE_IMPORT_JOB_STATUS_FAILED,
            static::ASSET_BUNDLE_IMPORT_JOB_STATUS_FAILED_ROLLBACK_COMPLETED,
            static::ASSET_BUNDLE_IMPORT_JOB_STATUS_FAILED_ROLLBACK_ERROR,
        ];
    }

    /**
     * Specification:
     * - Returns the path to the asset bundle import file.
     *
     * @api
     *
     * @throws \SprykerEco\Zed\AmazonQuicksight\Business\Exception\AssetBundleImportFilePathNotDefinedException
     *
     * @return string
     */
    public function getAssetBundleImportFilePath(): string
    {
        throw new AssetBundleImportFilePathNotDefinedException('Asset bundle import file path is not defined.');
    }

    /**
     * Specification:
     * - Returns the default status for new asset bundle import jobs.
     *
     * @api
     *
     * @return string
     */
    public function getDefaultNewAssetBundleImportJobStatus(): string
    {
        return static::DEFAULT_NEW_ASSET_BUNDLE_IMPORT_JOB_STATUS;
    }
}
