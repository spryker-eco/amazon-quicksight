<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Communication;

use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightDependencyProvider;
use SprykerEco\Zed\AmazonQuicksight\Communication\Expander\QuicksightUserFormExpander;
use SprykerEco\Zed\AmazonQuicksight\Communication\Expander\QuicksightUserFormExpanderInterface;
use SprykerEco\Zed\AmazonQuicksight\Communication\Transformer\QuicksightUserRoleDataTransformer;
use SprykerEco\Zed\AmazonQuicksight\Dependency\Facade\AmazonQuicksightToTranslatorFacadeInterface;
use Symfony\Component\Form\DataTransformerInterface;

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
            $this->getConfig(),
            $this->getTranslatorFacade(),
        );
    }

    /**
     * @return \Symfony\Component\Form\DataTransformerInterface
     */
    public function createQuicksightUserRoleDataTransformer(): DataTransformerInterface
    {
        return new QuicksightUserRoleDataTransformer();
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Dependency\Facade\AmazonQuicksightToTranslatorFacadeInterface
     */
    public function getTranslatorFacade(): AmazonQuicksightToTranslatorFacadeInterface
    {
        return $this->getProvidedDependency(AmazonQuicksightDependencyProvider::FACADE_TRANSLATOR);
    }
}
