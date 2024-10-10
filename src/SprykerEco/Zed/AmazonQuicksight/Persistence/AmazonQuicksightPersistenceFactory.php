<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Persistence;

use Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightAssetBundleImportJobQuery;
use Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightUserQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightDependencyProvider;
use SprykerEco\Zed\AmazonQuicksight\Dependency\Service\AmazonQuicksightToUtilEncodingServiceInterface;
use SprykerEco\Zed\AmazonQuicksight\Persistence\Propel\Mapper\QuicksightAssetBundleImportJobMapper;
use SprykerEco\Zed\AmazonQuicksight\Persistence\Propel\Mapper\QuicksightUserMapper;

/**
 * @method \SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightConfig getConfig()
 * @method \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface getRepository()
 * @method \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightEntityManagerInterface getEntityManager()
 */
class AmazonQuicksightPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Persistence\Propel\Mapper\QuicksightUserMapper
     */
    public function createQuicksightUserMapper(): QuicksightUserMapper
    {
        return new QuicksightUserMapper();
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Persistence\Propel\Mapper\QuicksightAssetBundleImportJobMapper
     */
    public function createQuicksightAssetBundleImportJobMapper(): QuicksightAssetBundleImportJobMapper
    {
        return new QuicksightAssetBundleImportJobMapper($this->getUtilEncodingService());
    }

    /**
     * @return \Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightUserQuery
     */
    public function getQuicksightUserQuery(): SpyQuicksightUserQuery
    {
        return SpyQuicksightUserQuery::create();
    }

    /**
     * @return \Orm\Zed\AmazonQuicksight\Persistence\SpyQuicksightAssetBundleImportJobQuery
     */
    public function getQuicksightAssetBundleImportJobQuery(): SpyQuicksightAssetBundleImportJobQuery
    {
        return SpyQuicksightAssetBundleImportJobQuery::create();
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Dependency\Service\AmazonQuicksightToUtilEncodingServiceInterface
     */
    public function getUtilEncodingService(): AmazonQuicksightToUtilEncodingServiceInterface
    {
        return $this->getProvidedDependency(AmazonQuicksightDependencyProvider::SERVICE_UTIL_ENCODING);
    }
}
