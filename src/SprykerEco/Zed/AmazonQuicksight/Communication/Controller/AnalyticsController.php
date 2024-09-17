<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Communication\Controller;

use Generated\Shared\Transfer\EnableQuicksightAnalyticsRequestTransfer;
use Generated\Shared\Transfer\ResetQuicksightAnalyticsRequestTransfer;
use Generated\Shared\Transfer\UserConditionsTransfer;
use Generated\Shared\Transfer\UserCriteriaTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \SprykerEco\Zed\AmazonQuicksight\Communication\AmazonQuicksightCommunicationFactory getFactory()
 * @method \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface getRepository()
 * @method \SprykerEco\Zed\AmazonQuicksight\Business\AmazonQuicksightFacadeInterface getFacade()
 */
class AnalyticsController extends AbstractController
{
    /**
     * @uses \Spryker\Zed\AnalyticsGui\Communication\Controller\AnalyticsController::indexAction()
     *
     * @var string
     */
    protected const URL_ANALYTICS = '/analytics-gui/analytics';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array<string, mixed>
     */
    public function enableAction(Request $request): RedirectResponse|array
    {
        $enableAnalyticsForm = $this->getFactory()->getEnableAnalyticsForm();
        $enableAnalyticsForm->handleRequest($request);

        if (!$enableAnalyticsForm->isSubmitted()) {
            return $this->viewResponse([
                'enableAnalyticsForm' => $enableAnalyticsForm->createView(),
            ]);
        }

        if (!$enableAnalyticsForm->isValid()) {
            $this->addErrorMessage('CSRF token is not valid');
        }

        $enableQuicksightAnalyticsResponseTransfer = $this->getFacade()->enableAnalytics(
            (new EnableQuicksightAnalyticsRequestTransfer())
                ->setAssetBundleImportJobId($this->getFactory()->getConfig()->getDefaultAssetBundleImportJobId())
                ->setUser($this->findCurrentUser()),
        );

        foreach ($enableQuicksightAnalyticsResponseTransfer->getErrors() as $errorTransfer) {
            $this->addErrorMessage($errorTransfer->getMessageOrFail());
        }

        return $this->redirectResponse(static::URL_ANALYTICS);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array<string, mixed>
     */
    public function resetAction(Request $request): RedirectResponse|array
    {
        $resetAnalyticsForm = $this->getFactory()->getResetAnalyticsForm();
        $resetAnalyticsForm->handleRequest($request);

        if (!$resetAnalyticsForm->isSubmitted()) {
            return $this->viewResponse([
                'resetAnalyticsForm' => $resetAnalyticsForm->createView(),
            ]);
        }

        if (!$resetAnalyticsForm->isValid()) {
            $this->addErrorMessage('CSRF token is not valid');
        }

        $resetQuicksightAnalyticsResponseTransfer = $this->getFacade()->resetAnalytics(
            (new ResetQuicksightAnalyticsRequestTransfer())
                ->setAssetBundleImportJobId($this->getFactory()->getConfig()->getDefaultAssetBundleImportJobId())
                ->setUser($this->findCurrentUser()),
        );

        foreach ($resetQuicksightAnalyticsResponseTransfer->getErrors() as $errorTransfer) {
            $this->addErrorMessage($errorTransfer->getMessageOrFail());
        }

        return $this->redirectResponse(static::URL_ANALYTICS);
    }

    /**
     * @return \Generated\Shared\Transfer\UserTransfer|null
     */
    protected function findCurrentUser(): ?UserTransfer
    {
        $userFacade = $this->getFactory()->getUserFacade();
        $userTransfer = $userFacade->getCurrentUser();
        $userCollectionTransfer = $userFacade->getUserCollection(
            (new UserCriteriaTransfer())->setUserConditions(
                (new UserConditionsTransfer())->addIdUser($userTransfer->getIdUserOrFail()),
            ),
        );

        return $userCollectionTransfer->getUsers()->getIterator()->current();
    }
}
