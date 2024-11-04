<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Communication;

use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightDependencyProvider;
use SprykerEco\Zed\AmazonQuicksight\Communication\DataProvider\QuicksightUserFormDataProvider;
use SprykerEco\Zed\AmazonQuicksight\Communication\DataProvider\QuicksightUserFormDataProviderInterface;
use SprykerEco\Zed\AmazonQuicksight\Communication\Expander\QuicksightUserFormExpander;
use SprykerEco\Zed\AmazonQuicksight\Communication\Expander\QuicksightUserFormExpanderInterface;
use SprykerEco\Zed\AmazonQuicksight\Communication\Form\EnableAnalyticsForm;
use SprykerEco\Zed\AmazonQuicksight\Communication\Form\ResetAnalyticsForm;
use SprykerEco\Zed\AmazonQuicksight\Communication\Transformer\QuicksightUserRoleDataTransformer;
use SprykerEco\Zed\AmazonQuicksight\Dependency\Facade\AmazonQuicksightToUserFacadeInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @method \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig getConfig()
 * @method \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface getEntityManager()
 * @method \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface getRepository()
 * @method \SprykerEco\Zed\AmazonQuicksight\Business\AmazonQuicksightFacadeInterface getFacade()
 */
class AmazonQuicksightCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Communication\Expander\QuicksightUserFormExpanderInterface
     */
    public function createQuicksightUserFormExpander(): QuicksightUserFormExpanderInterface
    {
        return new QuicksightUserFormExpander(
            $this->createQuicksightUserRoleDataTransformer(),
            $this->createQuicksightUserFormDataProvider(),
            $this->getConfig(),
        );
    }

    /**
     * @return \Symfony\Component\Form\DataTransformerInterface<array<string, mixed>, array<string, mixed>>
     */
    public function createQuicksightUserRoleDataTransformer(): DataTransformerInterface
    {
        return new QuicksightUserRoleDataTransformer();
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Communication\DataProvider\QuicksightUserFormDataProviderInterface
     */
    public function createQuicksightUserFormDataProvider(): QuicksightUserFormDataProviderInterface
    {
        return new QuicksightUserFormDataProvider($this->getConfig());
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getEnableAnalyticsForm(): FormInterface
    {
        return $this->getFormFactory()->create(EnableAnalyticsForm::class);
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getResetAnalyticsForm(): FormInterface
    {
        return $this->getFormFactory()->create(ResetAnalyticsForm::class);
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Dependency\Facade\AmazonQuicksightToUserFacadeInterface
     */
    public function getUserFacade(): AmazonQuicksightToUserFacadeInterface
    {
        return $this->getProvidedDependency(AmazonQuicksightDependencyProvider::FACADE_USER);
    }

    /**
     * @return \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface
     */
    public function getCsrfTokenManager(): CsrfTokenManagerInterface
    {
        return $this->getProvidedDependency(AmazonQuicksightDependencyProvider::SERVICE_FORM_CSRF_PROVIDER);
    }
}
