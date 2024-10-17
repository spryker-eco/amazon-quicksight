<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Communication\Expander;

use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig;
use SprykerEco\Zed\AmazonQuicksight\Communication\DataProvider\QuicksightUserFormDataProviderInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class QuicksightUserFormExpander implements QuicksightUserFormExpanderInterface
{
    /**
     * @var string
     */
    protected const FIELD_QUICKSIGHT_USER_ROLE = 'quicksight_user_role';

    /**
     * @var string
     */
    protected const PLACEHOLDER_QUICKSIGHT_USER_ROLE = 'Select user role';

    /**
     * @var string
     */
    protected const HELP_MESSAGE_QUICKSIGHT_USER_ROLE = 'Author: A user who can create data sources, datasets, analyses, and dashboards. <br/>Reader: A user who has read-only access to dashboards.';

    /**
     * @var string
     */
    protected const TEMPLATE_PATH_QUICKSIGHT_USER_ROLE = '@AmazonQuicksight/_partials/user-form-quicksight-user-role-field.twig';

    /**
     * @var string
     */
    protected const KEY_QUICKSIGHT_USER = 'quicksight_user';

    /**
     * @var string
     */
    protected const KEY_ROLE = 'role';

    /**
     * @var \Symfony\Component\Form\DataTransformerInterface<array<string, mixed>, array<string, mixed>>
     */
    protected DataTransformerInterface $quicksightUserRoleDataTransformer;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Communication\DataProvider\QuicksightUserFormDataProviderInterface
     */
    protected QuicksightUserFormDataProviderInterface $quicksightUserFormDataProvider;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig
     */
    protected AmazonQuicksightConfig $amazonQuicksightConfig;

    /**
     * @param \Symfony\Component\Form\DataTransformerInterface<array<string, mixed>, array<string, mixed>> $quicksightUserRoleDataTransformer
     * @param \SprykerEco\Zed\AmazonQuicksight\Communication\DataProvider\QuicksightUserFormDataProviderInterface $quicksightUserFormDataProvider
     * @param \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig $amazonQuicksightConfig
     */
    public function __construct(
        DataTransformerInterface $quicksightUserRoleDataTransformer,
        QuicksightUserFormDataProviderInterface $quicksightUserFormDataProvider,
        AmazonQuicksightConfig $amazonQuicksightConfig
    ) {
        $this->quicksightUserRoleDataTransformer = $quicksightUserRoleDataTransformer;
        $this->quicksightUserFormDataProvider = $quicksightUserFormDataProvider;
        $this->amazonQuicksightConfig = $amazonQuicksightConfig;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return void
     */
    public function expandForm(FormBuilderInterface $builder): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $data = $event->getData();
            $quicksightUserRole = $data[static::KEY_QUICKSIGHT_USER][static::KEY_ROLE] ?? null;

            $event->getForm()->add(static::FIELD_QUICKSIGHT_USER_ROLE, ChoiceType::class, [
                'required' => false,
                'choices' => $this->quicksightUserFormDataProvider->getQuicksightUserRoleChoices(),
                'placeholder' => static::PLACEHOLDER_QUICKSIGHT_USER_ROLE,
                'attr' => [
                    'template_path' => static::TEMPLATE_PATH_QUICKSIGHT_USER_ROLE,
                    'disabled' => $quicksightUserRole && !$this->amazonQuicksightConfig->isQuicksightUserRoleUpdateEnabled(),
                ],
                'help' => static::HELP_MESSAGE_QUICKSIGHT_USER_ROLE,
            ]);
        });

        $builder->addModelTransformer($this->quicksightUserRoleDataTransformer);
    }
}
