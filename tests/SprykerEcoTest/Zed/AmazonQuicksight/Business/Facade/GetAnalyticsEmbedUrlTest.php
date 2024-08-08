<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\AmazonQuicksight\Business\Facade;

use Aws\QuickSight\Exception\QuickSightException;
use Aws\Result;
use Aws\ResultInterface;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\AnalyticsEmbedUrlRequestTransfer;
use Generated\Shared\Transfer\QuicksightUserTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Spryker\Shared\Kernel\Transfer\Exception\NullValueException;
use SprykerEco\Zed\AmazonQuicksight\AmazonQuicksightDependencyProvider;
use SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface;
use SprykerEcoTest\Zed\AmazonQuicksight\AmazonQuicksightBusinessTester;

class GetAnalyticsEmbedUrlTest extends Unit
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
    protected const ERROR_MESSAGE_EMBED_URL_GENERATION_FAILED = 'Failed to generate Embed URL user.';

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
            $this->getAwsQuicksightClientMockWithSuccessfulResponse(
                new Result([
                    'RequestId' => time(),
                    static::RESPONSE_KEY_EMBED_URL => static::EMBED_URL_TEST,
                ]),
            ),
        );

        // Act
        $analyticsEmbedUrlResponseTransfer = $this->tester
            ->getFacade()
            ->getAnalyticsEmbedUrl($analyticsEmbedUrlRequestTransfer);

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
            $this->getAwsQuicksightClientMockWithErrorResponse(
                $this->getQuicksightExceptionMock(static::ERROR_MESSAGE_GENERATE_EMBED_URL_FOR_REGISTERED_USER_FAILED),
            ),
        );

        // Act
        $analyticsEmbedUrlResponseTransfer = $this->tester
            ->getFacade()
            ->getAnalyticsEmbedUrl($analyticsEmbedUrlRequestTransfer);

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
            $this->getAwsQuicksightClientMockWithSuccessfulResponse(new Result()),
        );

        // Act
        $analyticsEmbedUrlResponseTransfer = $this->tester
            ->getFacade()
            ->getAnalyticsEmbedUrl($analyticsEmbedUrlRequestTransfer);

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
        $this->tester->getFacade()->getAnalyticsEmbedUrl($analyticsEmbedUrlRequestTransfer);
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

    /**
     * @param \Aws\ResultInterface $result
     *
     * @return \SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getAwsQuicksightClientMockWithSuccessfulResponse(
        ResultInterface $result
    ): AmazonQuicksightToAwsQuicksightClientInterface {
        $awsQuicksightClientMock = $this->getAwsQuicksightClientMock();
        $awsQuicksightClientMock->method('generateEmbedUrlForRegisteredUser')
            ->willReturn($result);

        return $awsQuicksightClientMock;
    }

    /**
     * @param \Aws\QuickSight\Exception\QuickSightException $quickSightException
     *
     * @return \SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getAwsQuicksightClientMockWithErrorResponse(
        QuickSightException $quickSightException
    ): AmazonQuicksightToAwsQuicksightClientInterface {
        $awsQuicksightClientMock = $this->getAwsQuicksightClientMock();
        $awsQuicksightClientMock->method('generateEmbedUrlForRegisteredUser')
            ->willThrowException($quickSightException);

        return $awsQuicksightClientMock;
    }

    /**
     * @return \SprykerEco\Zed\AmazonQuicksight\Dependency\External\AmazonQuicksightToAwsQuicksightClientInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getAwsQuicksightClientMock(): AmazonQuicksightToAwsQuicksightClientInterface
    {
        return $this->getMockBuilder(AmazonQuicksightToAwsQuicksightClientInterface::class)->getMock();
    }

    /**
     * @param string $message
     *
     * @return \Aws\QuickSight\Exception\QuickSightException|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getQuicksightExceptionMock(string $message): QuickSightException
    {
        $quicksightExceptionMock = $this->getMockBuilder(QuickSightException::class)
            ->disableOriginalConstructor()
            ->getMock();
        $quicksightExceptionMock->method('getAwsErrorMessage')->willReturn($message);

        return $quicksightExceptionMock;
    }
}
