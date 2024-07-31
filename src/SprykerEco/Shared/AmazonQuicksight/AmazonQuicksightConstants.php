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
    public const AWS_ACCOUNT_ID = 'QUICKSIGHT:AWS_ACCOUNT_ID';

    /**
     * Specification:
     * - The AWS region that you use for the Amazon QuickSight account.
     *
     * @api
     *
     * @var string
     */
    public const AWS_REGION = 'QUICKSIGHT:AWS_REGION';

    /**
     * Specification:
     * - AWS access key ID.
     *
     * @api
     *
     * @var string
     */
    public const AWS_CREDENTIALS_KEY = 'QUICKSIGHT:AWS_CREDENTIALS_KEY';

    /**
     * Specification:
     * - AWS access key ID.
     *
     * @api
     *
     * @var string
     */
    public const AWS_CREDENTIALS_SECRET = 'QUICKSIGHT:AWS_CREDENTIALS_SECRET';

    /**
     * Specification:
     * - AWS security token.
     *
     * @api
     *
     * @var string
     */
    public const AWS_CREDENTIALS_TOKEN = 'QUICKSIGHT:AWS_CREDENTIALS_TOKEN';
}
