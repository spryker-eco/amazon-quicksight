<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Business;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightDependencyProvider;
use SprykerEco\Zed\AmazonQuicksight\Business\Adder\ErrorAdder;
use SprykerEco\Zed\AmazonQuicksight\Business\Adder\ErrorAdderInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AssetBundleAmazonQuicksightApiClient;
use SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AssetBundleAmazonQuicksightApiClientInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\UserAmazonQuicksightApiClient;
use SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\UserAmazonQuicksightApiClientInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Creator\QuicksightAssetBundleImportJobCreator;
use SprykerEco\Zed\AmazonQuicksight\Business\Creator\QuicksightAssetBundleImportJobCreatorInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Deleter\DataSetDeleter;
use SprykerEco\Zed\AmazonQuicksight\Business\Deleter\DataSetDeleterInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Deleter\QuicksightUserDeleter;
use SprykerEco\Zed\AmazonQuicksight\Business\Deleter\QuicksightUserDeleterInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Enabler\AssetBundleEnabler;
use SprykerEco\Zed\AmazonQuicksight\Business\Enabler\AssetBundleEnablerInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Expander\AnalyticsExpander;
use SprykerEco\Zed\AmazonQuicksight\Business\Expander\AnalyticsExpanderInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Expander\UserExpander;
use SprykerEco\Zed\AmazonQuicksight\Business\Expander\UserExpanderInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\FileContentLoader\AssetBundleImportFileContentLoader;
use SprykerEco\Zed\AmazonQuicksight\Business\FileContentLoader\AssetBundleImportFileContentLoaderInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Filter\QuicksightUserCollectionFilter;
use SprykerEco\Zed\AmazonQuicksight\Business\Filter\QuicksightUserCollectionFilterInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Filter\UserCollectionFilter;
use SprykerEco\Zed\AmazonQuicksight\Business\Filter\UserCollectionFilterInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Formatter\AmazonQuicksightRequestDataFormatter;
use SprykerEco\Zed\AmazonQuicksight\Business\Formatter\AmazonQuicksightRequestDataFormatterInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Mapper\AmazonQuicksightMapper;
use SprykerEco\Zed\AmazonQuicksight\Business\Mapper\AmazonQuicksightMapperInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Matcher\QuicksightUserMatcher;
use SprykerEco\Zed\AmazonQuicksight\Business\Matcher\QuicksightUserMatcherInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Reader\QuicksightUserReader;
use SprykerEco\Zed\AmazonQuicksight\Business\Reader\QuicksightUserReaderInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Reader\UserReader;
use SprykerEco\Zed\AmazonQuicksight\Business\Reader\UserReaderInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Saver\QuicksightUserSaver;
use SprykerEco\Zed\AmazonQuicksight\Business\Saver\QuicksightUserSaverInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Synchronizer\QuicksightAssetBundleImportJobSynchronizer;
use SprykerEco\Zed\AmazonQuicksight\Business\Synchronizer\QuicksightAssetBundleImportJobSynchronizerInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Updater\QuicksightAssetBundleImportJobUpdater;
use SprykerEco\Zed\AmazonQuicksight\Business\Updater\QuicksightAssetBundleImportJobUpdaterInterface;
use SprykerEco\Zed\AmazonQuicksight\Business\Validator\QuicksightAnalyticsRequestValidator;
use SprykerEco\Zed\AmazonQuicksight\Business\Validator\QuicksightAnalyticsRequestValidatorInterface;
use SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface;
use SprykerEco\Zed\AmazonQuicksight\Dependency\Facade\AmazonQuicksightToMessengerFacadeInterface;
use SprykerEco\Zed\AmazonQuicksight\Dependency\Facade\AmazonQuicksightToUserFacadeInterface;
use Twig\Environment;

/**
 * @method \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig getConfig()
 * @method \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface getEntityManager()
 * @method \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface getRepository()
 */
class AmazonQuicksightBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\Expander\UserExpanderInterface
     */
    public function createUserExpander(): UserExpanderInterface
    {
        return new UserExpander($this->getRepository());
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\Saver\QuicksightUserSaverInterface
     */
    public function createQuicksightUserSaver(): QuicksightUserSaverInterface
    {
        return new QuicksightUserSaver(
            $this->createQuicksightUserMatcher(),
            $this->getEntityManager(),
            $this->createUserAmazonQuicksightApiClient(),
            $this->createQuicksightUserReader(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\Deleter\QuicksightUserDeleterInterface
     */
    public function createQuicksightUserDeleter(): QuicksightUserDeleterInterface
    {
        return new QuicksightUserDeleter(
            $this->createUserCollectionFilter(),
            $this->createQuicksightUserMatcher(),
            $this->getEntityManager(),
            $this->createUserAmazonQuicksightApiClient(),
            $this->getMessengerFacade(),
            $this->createErrorAdder(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\Matcher\QuicksightUserMatcherInterface
     */
    public function createQuicksightUserMatcher(): QuicksightUserMatcherInterface
    {
        return new QuicksightUserMatcher(
            $this->createUserReader(),
            $this->createUserCollectionFilter(),
            $this->createQuicksightUserCollectionFilter(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\Reader\QuicksightUserReaderInterface
     */
    public function createQuicksightUserReader(): QuicksightUserReaderInterface
    {
        return new QuicksightUserReader($this->getRepository());
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\Reader\UserReaderInterface
     */
    public function createUserReader(): UserReaderInterface
    {
        return new UserReader(
            $this->getConfig(),
            $this->getUserFacade(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\Filter\QuicksightUserCollectionFilterInterface
     */
    public function createQuicksightUserCollectionFilter(): QuicksightUserCollectionFilterInterface
    {
        return new QuicksightUserCollectionFilter($this->getConfig());
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\Filter\UserCollectionFilterInterface
     */
    public function createUserCollectionFilter(): UserCollectionFilterInterface
    {
        return new UserCollectionFilter(
            $this->getConfig(),
            $this->createQuicksightUserReader(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\UserAmazonQuicksightApiClientInterface
     */
    public function createUserAmazonQuicksightApiClient(): UserAmazonQuicksightApiClientInterface
    {
        return new UserAmazonQuicksightApiClient(
            $this->getConfig(),
            $this->createAmazonQuicksightMapper(),
            $this->createAmazonQuicksightRequestDataFormatter(),
            $this->getAwsQuicksightClient(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AssetBundleAmazonQuicksightApiClientInterface
     */
    public function createAssetBundleAmazonQuicksightApiClient(): AssetBundleAmazonQuicksightApiClientInterface
    {
        return new AssetBundleAmazonQuicksightApiClient(
            $this->getConfig(),
            $this->createAmazonQuicksightMapper(),
            $this->createAmazonQuicksightRequestDataFormatter(),
            $this->getAwsQuicksightClient(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\Mapper\AmazonQuicksightMapperInterface
     */
    public function createAmazonQuicksightMapper(): AmazonQuicksightMapperInterface
    {
        return new AmazonQuicksightMapper();
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\Formatter\AmazonQuicksightRequestDataFormatterInterface
     */
    public function createAmazonQuicksightRequestDataFormatter(): AmazonQuicksightRequestDataFormatterInterface
    {
        return new AmazonQuicksightRequestDataFormatter();
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\Expander\AnalyticsExpanderInterface
     */
    public function createAnalyticsExpander(): AnalyticsExpanderInterface
    {
        return new AnalyticsExpander(
            $this->getRepository(),
            $this->getConfig(),
            $this->createUserAmazonQuicksightApiClient(),
            $this->createQuicksightAnalyticsRequestValidator(),
            $this->createQuicksightAssetBundleImportJobSynchronizer(),
            $this->getTwigEnvironment(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\Validator\QuicksightAnalyticsRequestValidatorInterface
     */
    public function createQuicksightAnalyticsRequestValidator(): QuicksightAnalyticsRequestValidatorInterface
    {
        return new QuicksightAnalyticsRequestValidator($this->getConfig());
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\Enabler\AssetBundleEnablerInterface
     */
    public function createAssetBundleEnabler(): AssetBundleEnablerInterface
    {
        return new AssetBundleEnabler(
            $this->createQuicksightAssetBundleImportJobCreator(),
            $this->createQuicksightAssetBundleImportJobUpdater(),
            $this->getRepository(),
            $this->createQuicksightAnalyticsRequestValidator(),
            $this->createAssetBundleImportFileContentLoader(),
            $this->createDataSetDeleter(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\Synchronizer\QuicksightAssetBundleImportJobSynchronizerInterface
     */
    public function createQuicksightAssetBundleImportJobSynchronizer(): QuicksightAssetBundleImportJobSynchronizerInterface
    {
        return new QuicksightAssetBundleImportJobSynchronizer(
            $this->createAssetBundleAmazonQuicksightApiClient(),
            $this->getConfig(),
            $this->getEntityManager(),
            $this->getRepository(),
            $this->createQuicksightAnalyticsRequestValidator(),
            $this->createAmazonQuicksightMapper(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\FileContentLoader\AssetBundleImportFileContentLoaderInterface
     */
    public function createAssetBundleImportFileContentLoader(): AssetBundleImportFileContentLoaderInterface
    {
        return new AssetBundleImportFileContentLoader($this->getConfig());
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\Creator\QuicksightAssetBundleImportJobCreatorInterface
     */
    public function createQuicksightAssetBundleImportJobCreator(): QuicksightAssetBundleImportJobCreatorInterface
    {
        return new QuicksightAssetBundleImportJobCreator(
            $this->createAssetBundleAmazonQuicksightApiClient(),
            $this->getConfig(),
            $this->getEntityManager(),
            $this->createQuicksightAssetBundleImportJobUpdater(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\Updater\QuicksightAssetBundleImportJobUpdaterInterface
     */
    public function createQuicksightAssetBundleImportJobUpdater(): QuicksightAssetBundleImportJobUpdaterInterface
    {
        return new QuicksightAssetBundleImportJobUpdater(
            $this->createAssetBundleAmazonQuicksightApiClient(),
            $this->getConfig(),
            $this->getEntityManager(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\Adder\ErrorAdderInterface
     */
    public function createErrorAdder(): ErrorAdderInterface
    {
        return new ErrorAdder();
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Business\Deleter\DataSetDeleterInterface
     */
    public function createDataSetDeleter(): DataSetDeleterInterface
    {
        return new DataSetDeleter(
            $this->getConfig(),
            $this->createAssetBundleAmazonQuicksightApiClient(),
            $this->createAmazonQuicksightMapper(),
        );
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface
     */
    public function getAwsQuicksightClient(): AmazonQuicksightToAwsQuicksightClientInterface
    {
        return $this->getProvidedDependency(AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT);
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Dependency\Facade\AmazonQuicksightToUserFacadeInterface
     */
    public function getUserFacade(): AmazonQuicksightToUserFacadeInterface
    {
        return $this->getProvidedDependency(AmazonQuicksightDependencyProvider::FACADE_USER);
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Dependency\Facade\AmazonQuicksightToMessengerFacadeInterface
     */
    public function getMessengerFacade(): AmazonQuicksightToMessengerFacadeInterface
    {
        return $this->getProvidedDependency(AmazonQuicksightDependencyProvider::FACADE_MESSENGER);
    }

    /**
     * @return \Twig\Environment
     */
    public function getTwigEnvironment(): Environment
    {
        return $this->getProvidedDependency(AmazonQuicksightDependencyProvider::SERVICE_TWIG);
    }
}
