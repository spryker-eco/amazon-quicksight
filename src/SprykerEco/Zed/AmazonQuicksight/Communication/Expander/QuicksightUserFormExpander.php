<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Communication\Expander;

use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig;
use SprykerEco\Zed\AmazonQuicksight\Dependency\Facade\AmazonQuicksightToTranslatorFacadeInterface;
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
    protected const FIELD_QUICKSIGHT_USER_ROLE_HELP_MESSAGE = 'Author: A user who can create data sources, datasets, analyses, and dashboards. <br/>Reader: A user who has read-only access to dashboards.';

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
     * @var \Symfony\Component\Form\DataTransformerInterface
     */
    protected DataTransformerInterface $quicksightUserRoleDataTransformer;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig
     */
    protected AmazonQuicksightConfig $amazonQuicksightConfig;

    /**
     * @var \SprykerEco\Zed\AmazonQuicksight\Dependency\Facade\AmazonQuicksightToTranslatorFacadeInterface
     */
    protected AmazonQuicksightToTranslatorFacadeInterface $translatorFacade;

    /**
     * @param \Symfony\Component\Form\DataTransformerInterface $quicksightUserRoleDataTransformer
     * @param \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig $amazonQuicksightConfig
     * @param \SprykerEco\Zed\AmazonQuicksight\Dependency\Facade\AmazonQuicksightToTranslatorFacadeInterface $translatorFacade
     */
    public function __construct(
        DataTransformerInterface $quicksightUserRoleDataTransformer,
        AmazonQuicksightConfig $amazonQuicksightConfig,
        AmazonQuicksightToTranslatorFacadeInterface $translatorFacade
    ) {
        $this->quicksightUserRoleDataTransformer = $quicksightUserRoleDataTransformer;
        $this->amazonQuicksightConfig = $amazonQuicksightConfig;
        $this->translatorFacade = $translatorFacade;
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
                'choices' => $this->getQuicksightUserRoleChoices(),
                'placeholder' => static::PLACEHOLDER_QUICKSIGHT_USER_ROLE,
                'attr' => [
                    'template_path' => static::TEMPLATE_PATH_QUICKSIGHT_USER_ROLE,
                    'disabled' => $quicksightUserRole && !$this->amazonQuicksightConfig->isQuicksightUserRoleUpdateEnabled(),
                ],
                'help' => $this->translatorFacade->trans(static::FIELD_QUICKSIGHT_USER_ROLE_HELP_MESSAGE),
            ]);
        });

        $builder->addModelTransformer($this->quicksightUserRoleDataTransformer);
    }

    /**
     * @return array<string, string>
     */
    protected function getQuicksightUserRoleChoices(): array
    {
        $quicksightUserRoles = $this->amazonQuicksightConfig->getQuicksightUserRoles();

        return array_combine(
            $this->formatQuicksightUserRoleChoiceLabels($quicksightUserRoles),
            $quicksightUserRoles,
        );
    }

    /**
     * @param list<string> $quicksightUserRoles
     *
     * @return list<string>
     */
    protected function formatQuicksightUserRoleChoiceLabels(array $quicksightUserRoles): array
    {
        return array_map(function ($quicksightUserRole) {
            return ucfirst(strtolower($quicksightUserRole));
        }, $quicksightUserRoles);
    }
}
