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
    protected const QUICKSIGHT_API_VERSION = '2018-04-01';

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
     * @var list<string>
     */
    protected const DEFAULT_ANALYSIS_PERMISSIONS_ACTIONS = [
        'quicksight:RestoreAnalysis',
        'quicksight:DescribeAnalysis',
        'quicksight:DescribeAnalysisPermissions',
        'quicksight:UpdateAnalysis',
        'quicksight:UpdateAnalysisPermissions',
        'quicksight:QueryAnalysis',
        'quicksight:DeleteAnalysis',
    ];

    /**
     * @var list<string>
     */
    protected const DEFAULT_DASHBOARD_PERMISSIONS_ACTIONS = [
        'quicksight:DescribeDashboard',
        'quicksight:DescribeDashboardPermissions',
        'quicksight:ListDashboardVersions',
        'quicksight:UpdateDashboard',
        'quicksight:UpdateDashboardPermissions',
        'quicksight:UpdateDashboardPublishedVersion',
        'quicksight:QueryDashboard',
        'quicksight:DeleteDashboard',
    ];

    /**
     * @var list<string>
     */
    protected const DEFAULT_DATA_SET_PERMISSIONS_ACTIONS = [
        'quicksight:DeleteDataSet',
        'quicksight:ListIngestions',
        'quicksight:UpdateDataSetPermissions',
        'quicksight:CancelIngestion',
        'quicksight:DescribeDataSetPermissions',
        'quicksight:PassDataSet',
        'quicksight:UpdateDataSet',
        'quicksight:DescribeDataSet',
        'quicksight:CreateIngestion',
        'quicksight:DescribeIngestion',
    ];

    /**
     * @var list<string>
     */
    protected const DEFAULT_DATA_SOURCE_PERMISSIONS_ACTIONS = [
        'quicksight:DescribeDataSource',
        'quicksight:DescribeDataSourcePermissions',
        'quicksight:PassDataSource',
        'quicksight:UpdateDataSource',
        'quicksight:DeleteDataSource',
        'quicksight:UpdateDataSourcePermissions',
    ];

    /**
     * @var string
     */
    protected const DEFAULT_DATA_SOURCE_ID = 'SprykerDefaultDataSource';

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
     * - Returns the list of available Quicksight user Author related roles.
     * - The list of available roles can be found here: {@link https://docs.aws.amazon.com/quicksight/latest/APIReference/API_User.html#QS-Type-User-Role}.
     *
     * @api
     *
     * @return list<string>
     */
    public function getQuicksightUserAuthorRoles(): array
    {
        return [
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
    public function getAwsQuicksightNamespace(): string
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
            'version' => static::QUICKSIGHT_API_VERSION,
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
        throw new AssetBundleImportFilePathNotDefinedException(
            'Asset bundle import file path is not defined. You need to configure the asset bundle import file path
            in your AmazonQuicksightConfig::getAssetBundleImportFilePath() to be able to import Quicksight asset bundle.',
        );
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

    /**
     * Specification:
     * - Returns the list of IAM actions to assign to the analysis during the default asset bundle import.
     *
     * @api
     *
     * @return list<string>
     */
    public function getDefaultAnalysisPermissionsActions(): array
    {
        return static::DEFAULT_ANALYSIS_PERMISSIONS_ACTIONS;
    }

    /**
     * Specification:
     * - Returns the list of IAM actions to assign to the dashboards during the default asset bundle import.
     *
     * @api
     *
     * @return list<string>
     */
    public function getDefaultDashboardPermissionsActions(): array
    {
        return static::DEFAULT_DASHBOARD_PERMISSIONS_ACTIONS;
    }

    /**
     * Specification:
     * - Returns the list of IAM actions to assign to the data sets during the default asset bundle import.
     *
     * @api
     *
     * @return list<string>
     */
    public function getDefaultDataSetPermissionsActions(): array
    {
        return static::DEFAULT_DATA_SET_PERMISSIONS_ACTIONS;
    }

    /**
     * Specification:
     * - Returns the list of IAM actions to assign to the data sources during the default asset bundle import.
     *
     * @api
     *
     * @return list<string>
     */
    public function getDefaultDataSourcePermissionsActions(): array
    {
        return static::DEFAULT_DATA_SOURCE_PERMISSIONS_ACTIONS;
    }

    /**
     * Specification:
     * - Returns the default data source ID.
     *
     * @api
     *
     * @return string
     */
    public function getDefaultDataSourceId(): string
    {
        return static::DEFAULT_DATA_SOURCE_ID;
    }

    /**
     * Specification:
     * - Returns the default data source username.
     *
     * @api
     *
     * @return string
     */
    public function getDefaultDataSourceUsername(): string
    {
        return $this->get(AmazonQuicksightConstants::DEFAULT_DATA_SOURCE_USERNAME);
    }

    /**
     * Specification:
     * - Returns the default data source password.
     *
     * @api
     *
     * @return string
     */
    public function getDefaultDataSourcePassword(): string
    {
        return $this->get(AmazonQuicksightConstants::DEFAULT_DATA_SOURCE_PASSWORD);
    }

    /**
     * Specification:
     * - Returns the default data source database name.
     *
     * @api
     *
     * @return string
     */
    public function getDefaultDataSourceDatabaseName(): string
    {
        return $this->get(AmazonQuicksightConstants::DEFAULT_DATA_SOURCE_DATABASE_NAME);
    }

    /**
     * Specification:
     * - Returns the default data source database port.
     *
     * @api
     *
     * @return int
     */
    public function getDefaultDataSourceDatabasePort(): int
    {
        return $this->get(AmazonQuicksightConstants::DEFAULT_DATA_SOURCE_DATABASE_PORT);
    }

    /**
     * Specification:
     * - Returns the default data source database host.
     *
     * @api
     *
     * @return string
     */
    public function getDefaultDataSourceDatabaseHost(): string
    {
        return $this->get(AmazonQuicksightConstants::DEFAULT_DATA_SOURCE_DATABASE_HOST);
    }

    /**
     * Specification:
     * - Returns the default data source VPC connection ARN.
     *
     * @api
     *
     * @return string
     */
    public function getDefaultDataSourceVpcConnectionArn(): string
    {
        return $this->get(AmazonQuicksightConstants::DEFAULT_DATA_SOURCE_VPC_CONNECTION_ARN);
    }
}
