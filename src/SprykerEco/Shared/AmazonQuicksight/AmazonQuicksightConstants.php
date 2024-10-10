<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
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
     * - The default data source database name.
     *
     * @api
     *
     * @var string
     */
    public const DEFAULT_DATA_SOURCE_DATABASE_NAME = 'AMAZON_QUICKSIGHT:DEFAULT_DATA_SOURCE_DATABASE_NAME';

    /**
     * Specification:
     * - The default data source database port.
     *
     * @api
     *
     * @var string
     */
    public const DEFAULT_DATA_SOURCE_DATABASE_PORT = 'AMAZON_QUICKSIGHT:DEFAULT_DATA_SOURCE_DATABASE_PORT';

    /**
     * Specification:
     * - The default data source database host.
     *
     * @api
     *
     * @var string
     */
    public const DEFAULT_DATA_SOURCE_DATABASE_HOST = 'AMAZON_QUICKSIGHT:DEFAULT_DATA_SOURCE_DATABASE_HOST';

    /**
     * Specification:
     * - The default data source VPC connection ARN.
     *
     * @api
     *
     * @var string
     */
    public const DEFAULT_DATA_SOURCE_VPC_CONNECTION_ARN = 'AMAZON_QUICKSIGHT:DEFAULT_DATA_SOURCE_VPC_CONNECTION_ARN';
}
