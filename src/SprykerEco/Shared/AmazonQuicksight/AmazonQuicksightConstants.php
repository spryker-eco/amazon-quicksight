<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Shared\AmazonQuicksight;

/**
 * Declares global environment configuration keys. Do not use it for other class constants.
 */
interface AmazonQuicksightConstants
{
    /**
     * Specification:
     * - The ID for the AWS account that contains your Amazon QuickSight account.
     *
     * @api
     *
     * @var string
     */
    public const AWS_ACCOUNT_ID = 'AMAZON_QUICKSIGHT:AWS_ACCOUNT_ID';

    /**
     * Specification:
     * - The AWS region that you use for the Amazon QuickSight account.
     *
     * @api
     *
     * @var string
     */
    public const AWS_REGION = 'AMAZON_QUICKSIGHT:AWS_REGION';

    /**
     * Specification:
     * - The API version of Quicksight Client API provider.
     *
     * @api
     *
     * @var string
     */
    public const AWS_CLIENT_API_VERSION = 'AMAZON_QUICKSIGHT:AWS_CLIENT_API_VERSION';

    /**
     * Specification:
     * - AWS access key ID.
     *
     * @api
     *
     * @var string
     */
    public const AWS_CREDENTIALS_KEY = 'AMAZON_QUICKSIGHT:AWS_CREDENTIALS_KEY';

    /**
     * Specification:
     * - AWS access key secret.
     *
     * @api
     *
     * @var string
     */
    public const AWS_CREDENTIALS_SECRET = 'AMAZON_QUICKSIGHT:AWS_CREDENTIALS_SECRET';

    /**
     * Specification:
     * - AWS security token.
     *
     * @api
     *
     * @var string
     */
    public const AWS_CREDENTIALS_TOKEN = 'AMAZON_QUICKSIGHT:AWS_CREDENTIALS_TOKEN';

    /**
     * Specification:
     * - The name of Quicksight namespace.
     *
     * @api
     *
     * @var string
     */
    public const AWS_QUICKSIGHT_NAMESPACE = 'AMAZON_QUICKSIGHT:AWS_QUICKSIGHT_NAMESPACE';

    /**
     * Specification:
     * - The default data source username.
     *
     * @api
     *
     * @var string
     */
    public const DEFAULT_DATA_SOURCE_USERNAME = 'AMAZON_QUICKSIGHT:DEFAULT_DATA_SOURCE_USERNAME';

    /**
     * Specification:
     * - The default data source password.
     *
     * @api
     *
     * @var string
     */
    public const DEFAULT_DATA_SOURCE_PASSWORD = 'AMAZON_QUICKSIGHT:DEFAULT_DATA_SOURCE_PASSWORD';

    /**
     * Specification:
     * - The default data source MySQL port.
     *
     * @api
     *
     * @var string
     */
    public const DEFAULT_DATA_SOURCE_MYSQL_PORT = 'AMAZON_QUICKSIGHT:DEFAULT_DATA_SOURCE_MYSQL_PORT';
}
