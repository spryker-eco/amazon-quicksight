<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Communication\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * @implements \Symfony\Component\Form\DataTransformerInterface<array<string, mixed>, array<string, mixed>>
 */
class QuicksightUserRoleDataTransformer implements DataTransformerInterface
{
    /**
     * @uses \SprykerEco\Zed\AmazonQuicksight\Communication\Expander\QuicksightUserFormExpander::FIELD_QUICKSIGHT_USER_ROLE
     *
     * @var string
     */
    protected const FIELD_QUICKSIGHT_USER_ROLE = 'quicksight_user_role';

    /**
     * @var string
     */
    protected const KEY_QUICKSIGHT_USER = 'quicksight_user';

    /**
     * @var string
     */
    protected const KEY_ROLE = 'role';

    /**
     * @param mixed $value
     *
     * @return mixed|array<string, mixed>
     */
    public function transform(mixed $value)
    {
        if (!is_array($value)) {
            return $value;
        }

        $quicksightUserRole = $value[static::KEY_QUICKSIGHT_USER][static::KEY_ROLE] ?? null;
        if ($quicksightUserRole !== null) {
            $value[static::FIELD_QUICKSIGHT_USER_ROLE] = $quicksightUserRole;
        }

        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return mixed|array<string, mixed>
     */
    public function reverseTransform(mixed $value)
    {
        if (!is_array($value)) {
            return $value;
        }

        $quicksightUserRole = $value[static::FIELD_QUICKSIGHT_USER_ROLE] ?? null;
        if ($quicksightUserRole !== null) {
            $value[static::KEY_QUICKSIGHT_USER][static::KEY_ROLE] = $quicksightUserRole;
        }

        return $value;
    }
}
