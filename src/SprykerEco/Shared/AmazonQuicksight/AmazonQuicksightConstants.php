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
}
