<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientAdapter;
use SprykerEco\Zed\AmazonQuicksight\Dependency\Facade\AmazonQuicksightToMessengerFacadeBridge;

/**
 * @method \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig getConfig()
 */
class AmazonQuicksightDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const AWS_QUICKSIGHT_CLIENT = 'AWS_QUICKSIGHT_CLIENT';

    /**
     * @var string
     */
    public const FACADE_MESSENGER = 'FACADE_MESSENGER';

    /**
     * @uses \Spryker\Zed\Twig\Communication\Plugin\Application\TwigApplicationPlugin::SERVICE_TWIG
     *
     * @var string
     */
    public const SERVICE_TWIG = 'twig';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);
        $container = $this->addAwsQuicksightClient($container);
        $container = $this->addMessengerFacade($container);
        $container = $this->addTwigEnvironment($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addAwsQuicksightClient(Container $container): Container
    {
        $container->set(static::AWS_QUICKSIGHT_CLIENT, function () {
            return new AmazonQuicksightToAwsQuicksightClientAdapter(
                $this->getConfig()->getQuicksightClientConfiguration(),
            );
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMessengerFacade(Container $container): Container
    {
        $container->set(static::FACADE_MESSENGER, function (Container $container) {
            return new AmazonQuicksightToMessengerFacadeBridge(
                $container->getLocator()->messenger()->facade(),
            );
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addTwigEnvironment(Container $container): Container
    {
        $container->set(static::SERVICE_TWIG, function (Container $container) {
            return $container->getApplicationService(static::SERVICE_TWIG);
        });

        return $container;
    }
}
