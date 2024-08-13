<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\AmazonQuicksight\Business\Facade;

use Aws\Result;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\AnalyticsCollectionTransfer;
use Generated\Shared\Transfer\AnalyticsRequestTransfer;
use Generated\Shared\Transfer\QuicksightUserTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Spryker\Shared\Kernel\Transfer\Exception\NullValueException;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightDependencyProvider;
use SprykerEcoTest\Zed\AmazonQuicksight\AmazonQuicksightBusinessTester;
use Twig\Environment;

class ExpandAnalyticsCollectionWithQuicksightAnalyticsTest extends Unit
{
    /**
     * @uses \Spryker\Zed\Twig\Communication\Plugin\Application\TwigApplicationPlugin::SERVICE_TWIG
     *
     * @var string
     */
    protected const SERVICE_TWIG = 'twig';

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
    protected function setUp(): void
    {
        parent::setUp();

        $this->tester->getContainer()->set(static::SERVICE_TWIG, $this->getTwigMock());
    }

    /**
     * @return void
     */
    public function testDoesNotExpandCollectionWhenQuicksightUserNotFound(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUser();
        $analyticsRequestTransfer = (new AnalyticsRequestTransfer())->setUser($userTransfer);

        // Act
        $analyticsCollectionTransfer = $this->tester
            ->getFacade()
            ->expandAnalyticsCollectionWithQuicksightAnalytics($analyticsRequestTransfer, new AnalyticsCollectionTransfer());

        // Assert
        $this->assertCount(0, $analyticsCollectionTransfer->getAnalyticsList());
    }

    /**
     * @return void
     */
    public function testExpandsCollectionWithGeneratedEmbedUrl(): void
    {
        // Arrange
        $analyticsRequestTransfer = $this->tester->haveAnalyticsRequestWithUser();
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
        $analyticsCollectionTransfer = $this->tester
            ->getFacade()
            ->expandAnalyticsCollectionWithQuicksightAnalytics($analyticsRequestTransfer, new AnalyticsCollectionTransfer());

        // Assert
        $this->assertCount(1, $analyticsCollectionTransfer->getAnalyticsList());
        $this->assertSame(
            static::EMBED_URL_TEST,
            $analyticsCollectionTransfer->getAnalyticsList()->getIterator()->current()->getContent(),
        );
    }

    /**
     * @return void
     */
    public function testExpandsCollectionWhenQuicksightClientThrowsException(): void
    {
        // Arrange
        $analyticsRequestTransfer = $this->tester->haveAnalyticsRequestWithUser();
        $this->tester->setDependency(
            AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT,
            $this->tester->getAwsQuicksightClientMockWithErrorResponse(
                $this->tester->getQuicksightExceptionMock(static::ERROR_MESSAGE_GENERATE_EMBED_URL_FOR_REGISTERED_USER_FAILED),
                'generateEmbedUrlForRegisteredUser',
            ),
        );

        // Act
        $analyticsCollectionTransfer = $this->tester
            ->getFacade()
            ->expandAnalyticsCollectionWithQuicksightAnalytics($analyticsRequestTransfer, new AnalyticsCollectionTransfer());

        // Assert
        $this->assertCount(1, $analyticsCollectionTransfer->getAnalyticsList());
        $this->assertSame(
            static::ERROR_MESSAGE_GENERATE_EMBED_URL_FOR_REGISTERED_USER_FAILED,
            $analyticsCollectionTransfer->getAnalyticsList()->getIterator()->current()->getContent(),
        );
    }

    /**
     * @return void
     */
    public function testExpandsCollectionWhenQuicksightClientResponseDoesNotContainEmbedUrlKey(): void
    {
        // Arrange
        $analyticsRequestTransfer = $this->tester->haveAnalyticsRequestWithUser();
        $this->tester->setDependency(
            AmazonQuicksightDependencyProvider::AWS_QUICKSIGHT_CLIENT,
            $this->tester->getAwsQuicksightClientMockWithSuccessfulResponse(new Result(), 'generateEmbedUrlForRegisteredUser'),
        );

        // Act
        $analyticsCollectionTransfer = $this->tester
            ->getFacade()
            ->expandAnalyticsCollectionWithQuicksightAnalytics($analyticsRequestTransfer, new AnalyticsCollectionTransfer());

        // Assert
        $this->assertCount(1, $analyticsCollectionTransfer->getAnalyticsList());
        $this->assertSame(
            static::ERROR_MESSAGE_EMBED_URL_GENERATION_FAILED,
            $analyticsCollectionTransfer->getAnalyticsList()->getIterator()->current()->getContent(),
        );
    }

    /**
     * @dataProvider throwsExceptionWhenRequiredPropertiesAreNotSetDataProvider
     *
     * @param \Generated\Shared\Transfer\AnalyticsRequestTransfer $analyticsRequestTransfer
     *
     * @return void
     */
    public function testThrowsExceptionWhenRequiredPropertiesAreNotSet(
        AnalyticsRequestTransfer $analyticsRequestTransfer
    ): void {
        // Arrange
        $this->tester->mockFactoryMethod('getRepository', $this->tester->getAmazonQuicksightRepositoryMock());

        // Assert
        $this->expectException(NullValueException::class);

        // Act
        $this->tester
            ->getFacade()
            ->expandAnalyticsCollectionWithQuicksightAnalytics($analyticsRequestTransfer, new AnalyticsCollectionTransfer());
    }

    /**
     * @return array<string, list<\Generated\Shared\Transfer\AnalyticsEmbedUrlRequestTransfer>>
     */
    public function throwsExceptionWhenRequiredPropertiesAreNotSetDataProvider(): array
    {
        return [
            'When user is not set' => [new AnalyticsRequestTransfer()],
            'When user ID is not set' => [
                (new AnalyticsRequestTransfer())
                    ->setUser((new UserTransfer())->setQuicksightUser((new QuicksightUserTransfer())->setArn('arn'))),
            ],
            'When Quicksight user is not set' => [
                (new AnalyticsRequestTransfer())->setUser((new UserTransfer())->setIdUser(1)),
            ],
            'When Quicksight user ARN is not set' => [
                (new AnalyticsRequestTransfer())
                    ->setUser((new UserTransfer())->setQuicksightUser(new QuicksightUserTransfer())->setIdUser(1)),
            ],
        ];
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Twig\Environment
     */
    protected function getTwigMock(): Environment
    {
        $twigMock = $this->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()
            ->getMock();
        $twigMock->method('render')
            ->willReturnCallback(function ($template, array $context = []) {
                if (!$context['quicksightGenerateEmbedUrlResponse']) {
                    return '';
                }

                /** @var \Generated\Shared\Transfer\QuicksightGenerateEmbedUrlResponseTransfer $quicksightGenerateEmbedUrlResponseTransfer */
                $quicksightGenerateEmbedUrlResponseTransfer = $context['quicksightGenerateEmbedUrlResponse'];

                if ($quicksightGenerateEmbedUrlResponseTransfer->getErrors()->count()) {
                    foreach ($quicksightGenerateEmbedUrlResponseTransfer->getErrors() as $errorTransfer) {
                        return $errorTransfer->getMessage();
                    }
                }

                if ($quicksightGenerateEmbedUrlResponseTransfer->getEmbedUrl()) {
                    return $quicksightGenerateEmbedUrlResponseTransfer->getEmbedUrlOrFail()->getUrl();
                }

                return '';
            });

        return $twigMock;
    }
}
