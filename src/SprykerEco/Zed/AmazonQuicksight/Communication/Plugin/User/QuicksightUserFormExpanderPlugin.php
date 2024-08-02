<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Communication\Plugin\User;

use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\UserExtension\Dependency\Plugin\UserFormExpanderPluginInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @method \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig getConfig()
 * @method \SprykerEco\Zed\AmazonQuicksight\Business\AmazonQuicksightFacadeInterface getFacade()
 * @method \SprykerEco\Zed\AmazonQuicksight\Communication\AmazonQuicksightCommunicationFactory getFactory()
 */
class QuicksightUserFormExpanderPlugin extends AbstractPlugin implements UserFormExpanderPluginInterface
{
    /**
     * {@inheritDoc}
     * - Expands user form with Quicksight user role field.
     *
     * @api
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder): void
    {
        $this->getFactory()
            ->createQuicksightUserFormExpander()
            ->expandForm($builder);
    }
}
