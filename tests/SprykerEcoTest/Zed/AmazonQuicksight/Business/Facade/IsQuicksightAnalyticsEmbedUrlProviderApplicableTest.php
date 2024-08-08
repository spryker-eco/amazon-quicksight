<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\AmazonQuicksight\Business\Facade;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\AnalyticsEmbedUrlRequestTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Spryker\Shared\Kernel\Transfer\Exception\NullValueException;
use SprykerEcoTest\Zed\AmazonQuicksight\AmazonQuicksightBusinessTester;

class IsQuicksightAnalyticsEmbedUrlProviderApplicableTest extends Unit
{
    /**
     * @var \SprykerEcoTest\Zed\AmazonQuicksight\AmazonQuicksightBusinessTester
     */
    protected AmazonQuicksightBusinessTester $tester;

    /**
     * @return void
     */
    public function testReturnsTrueWhenQuicksightUserExistsInDb(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUser();
        $this->tester->haveQuicksightUser($userTransfer);
        $analyticsEmbedUrlRequestTransfer = (new AnalyticsEmbedUrlRequestTransfer())->setUser($userTransfer);

        // Act
        $isApplicable = $this->tester
            ->getFacade()
            ->isQuicksightAnalyticsEmbedUrlProviderApplicable($analyticsEmbedUrlRequestTransfer);

        // Assert
        $this->assertTrue($isApplicable);
    }

    /**
     * @return void
     */
    public function testReturnsFalseWhenQuicksightUserDoesNotExistInDb(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUser();
        $analyticsEmbedUrlRequestTransfer = (new AnalyticsEmbedUrlRequestTransfer())->setUser($userTransfer);

        // Act
        $isApplicable = $this->tester
            ->getFacade()
            ->isQuicksightAnalyticsEmbedUrlProviderApplicable($analyticsEmbedUrlRequestTransfer);

        // Assert
        $this->assertFalse($isApplicable);
    }

    /**
     * @return void
     */
    public function testThrowsExceptionWhenUserIsNotSetToAnalyticsEmbedUrlRequestTransfer(): void
    {
        // Arrange
        $analyticsEmbedUrlRequestTransfer = new AnalyticsEmbedUrlRequestTransfer();

        // Assert
        $this->expectException(NullValueException::class);

        // Act
        $this->tester
            ->getFacade()
            ->isQuicksightAnalyticsEmbedUrlProviderApplicable($analyticsEmbedUrlRequestTransfer);
    }

    /**
     * @return void
     */
    public function testThrowsExceptionWhenUserIdIsNotSetToAnalyticsEmbedUrlRequestTransfer(): void
    {
        // Arrange
        $analyticsEmbedUrlRequestTransfer = (new AnalyticsEmbedUrlRequestTransfer())->setUser(new UserTransfer());

        // Assert
        $this->expectException(NullValueException::class);

        // Act
        $this->tester
            ->getFacade()
            ->isQuicksightAnalyticsEmbedUrlProviderApplicable($analyticsEmbedUrlRequestTransfer);
    }
}
