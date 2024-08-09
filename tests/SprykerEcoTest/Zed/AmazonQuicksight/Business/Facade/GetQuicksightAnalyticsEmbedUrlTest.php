<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\AmazonQuicksight\Business\Facade;

use Aws\Result;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\AnalyticsEmbedUrlRequestTransfer;
use Generated\Shared\Transfer\QuicksightUserTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Spryker\Shared\Kernel\Transfer\Exception\NullValueException;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightDependencyProvider;
use SprykerEcoTest\Zed\AmazonQuicksight\AmazonQuicksightBusinessTester;

class GetQuicksightAnalyticsEmbedUrlTest extends Unit
{
    /*
     * @var string
     */
    /**
     * @var string
     */
    protected const ERROR_MESSAGE_GENERATE_EMBED_URL_FOR_REGISTERED_USER_FAILED = 'error message';

    /**
     * @uses \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClient::ERROR_MESSAGE_EMBED_URL_GENERATION_FAILED
     *
     * @var string
     */
    protected const ERROR_MESSAGE_EMBED_URL_GENERATION_FAILED = 'Failed to generate embed URL.';

    /**
     * @uses \SprykerEco\Zed\AmazonQuicksight\Business\ApiClient\AmazonQuicksightApiClient::RESPONSE_KEY_EMBED_URL
     *
     * @var string
     */
    protected const RESPONSE_KEY_EMBED_URL = 'EmbedUrl';

    /**
     * @var string
     */
    protected const EMBED_URL_TEST = 'test-embed-url';

    /**
     * @var \SprykerEcoTest\Zed\AmazonQuicksight\AmazonQuicksightBusinessTester
     */
    protected AmazonQuicksightBusinessTester $tester;

    /**
     * @return void
     */
    public function testReturnsEmbedUrl(): void
    {
        // Arrange
        $analyticsEmbedUrlRequestTransfer = $this->tester->createValidAnalyticsEmbedUrlRequestTransfer();
        $this->tester->setDependency(
            AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT,
            $this->tester->getAwsQuicksightClientMockWithSuccessfulResponse(
                new Result([
                    'RequestId' => time(),
                    static::RESPONSE_KEY_EMBED_URL => static::EMBED_URL_TEST,
                ]),
                'generateEmbedUrlForRegisteredUser',
            ),
        );

        // Act
        $analyticsEmbedUrlResponseTransfer = $this->tester
            ->getFacade()
            ->getQuicksightAnalyticsEmbedUrl($analyticsEmbedUrlRequestTransfer);

        // Assert
        $this->assertNotNull($analyticsEmbedUrlResponseTransfer->getEmbedUrl());
        $this->assertSame(static::EMBED_URL_TEST, $analyticsEmbedUrlResponseTransfer->getEmbedUrlOrFail()->getUrl());
        $this->assertCount(0, $analyticsEmbedUrlResponseTransfer->getErrors());
    }

    /**
     * @return void
     */
    public function testReturnsErrorWhenUserQuicksightClientThrowsException(): void
    {
        // Arrange
        $analyticsEmbedUrlRequestTransfer = $this->tester->createValidAnalyticsEmbedUrlRequestTransfer();
        $this->tester->setDependency(
            AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT,
            $this->tester->getAwsQuicksightClientMockWithErrorResponse(
                $this->tester->getQuicksightExceptionMock(static::ERROR_MESSAGE_GENERATE_EMBED_URL_FOR_REGISTERED_USER_FAILED),
                'generateEmbedUrlForRegisteredUser',
            ),
        );

        // Act
        $analyticsEmbedUrlResponseTransfer = $this->tester
            ->getFacade()
            ->getQuicksightAnalyticsEmbedUrl($analyticsEmbedUrlRequestTransfer);

        // Assert
        $this->assertNull($analyticsEmbedUrlResponseTransfer->getEmbedUrl());
        $this->assertCount(1, $analyticsEmbedUrlResponseTransfer->getErrors());
        $this->assertSame(
            static::ERROR_MESSAGE_GENERATE_EMBED_URL_FOR_REGISTERED_USER_FAILED,
            $analyticsEmbedUrlResponseTransfer->getErrors()->getIterator()->current()->getMessage(),
        );
    }

    /**
     * @return void
     */
    public function testReturnsErrorWhenQuicksightClientResponseDoesNotContainEmbedUrlKey(): void
    {
        // Arrange
        $analyticsEmbedUrlRequestTransfer = $this->tester->createValidAnalyticsEmbedUrlRequestTransfer();
        $this->tester->setDependency(
            AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT,
            $this->tester->getAwsQuicksightClientMockWithSuccessfulResponse(new Result(), 'generateEmbedUrlForRegisteredUser'),
        );

        // Act
        $analyticsEmbedUrlResponseTransfer = $this->tester
            ->getFacade()
            ->getQuicksightAnalyticsEmbedUrl($analyticsEmbedUrlRequestTransfer);

        // Assert
        $this->assertNull($analyticsEmbedUrlResponseTransfer->getEmbedUrl());
        $this->assertCount(1, $analyticsEmbedUrlResponseTransfer->getErrors());
        $this->assertSame(
            static::ERROR_MESSAGE_EMBED_URL_GENERATION_FAILED,
            $analyticsEmbedUrlResponseTransfer->getErrors()->getIterator()->current()->getMessage(),
        );
    }

    /**
     * @dataProvider throwsExceptionWhenRequiredPropertiesAreNotSetDataProvider
     *
     * @param \Generated\Shared\Transfer\AnalyticsEmbedUrlRequestTransfer $analyticsEmbedUrlRequestTransfer
     *
     * @return void
     */
    public function testThrowsExceptionWhenRequiredPropertiesAreNotSet(
        AnalyticsEmbedUrlRequestTransfer $analyticsEmbedUrlRequestTransfer
    ): void {
        // Assert
        $this->expectException(NullValueException::class);

        // Act
        $this->tester->getFacade()->getQuicksightAnalyticsEmbedUrl($analyticsEmbedUrlRequestTransfer);
    }

    /**
     * @return array<string, list<\Generated\Shared\Transfer\AnalyticsEmbedUrlRequestTransfer>>
     */
    public function throwsExceptionWhenRequiredPropertiesAreNotSetDataProvider(): array
    {
        return [
            'When user is not set' => [new AnalyticsEmbedUrlRequestTransfer()],
            'When Quicksight user is not set' => [(new AnalyticsEmbedUrlRequestTransfer())->setUser(new UserTransfer())],
            'When Quicksight user ARN is not set' => [
                (new AnalyticsEmbedUrlRequestTransfer())
                    ->setUser((new UserTransfer())->setQuicksightUser(new QuicksightUserTransfer())),
            ],
        ];
    }
}
