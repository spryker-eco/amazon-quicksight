<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Communication\Controller;

use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfToken;

/**
 * @method \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface getRepository()
 * @method \SprykerEco\Zed\AmazonQuicksight\Business\AmazonQuicksightFacadeInterface getFacade()
 * @method \SprykerEco\Zed\AmazonQuicksight\Communication\AmazonQuicksightCommunicationFactory getFactory()
 */
class UserController extends AbstractController
{
    /**
     * @uses \Spryker\Zed\AnalyticsGui\Communication\Controller\AnalyticsController::indexAction()
     *
     * @var string
     */
    protected const URL_ANALYTICS = '/analytics-gui/analytics';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_CSRF_TOKEN_INVALID = 'CSRF token is not valid.';

    /**
     * @var string
     */
    protected const SUCCESS_MESSAGE_USERS_SYNCHRONIZED = 'Analytics users have been successfully synchronized.';

    /**
     * @uses \SprykerEco\Zed\AmazonQuicksight\Business\Expander\AnalyticsExpander::FIELD_NAME_TOKEN
     *
     * @var string
     */
    protected const FIELD_NAME_TOKEN = '_token';

    /**
     * @uses \SprykerEco\Zed\AmazonQuicksight\Business\Expander\AnalyticsExpander::FORM_NAME_SYNCHRONIZE_QUICKSIGHT_USERS
     *
     * @var string
     */
    protected const FORM_NAME_SYNCHRONIZE_QUICKSIGHT_USERS = 'synchronizeQuicksightUsersForm';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array<string, mixed>
     */
    public function synchronizeQuicksightUsersAction(Request $request): RedirectResponse|array
    {
        $tokenValue = (string)$request->get(static::FIELD_NAME_TOKEN);

        if (!$this->validateCsrfToken(static::FORM_NAME_SYNCHRONIZE_QUICKSIGHT_USERS, $tokenValue)) {
            $this->addErrorMessage(static::ERROR_MESSAGE_CSRF_TOKEN_INVALID);

            return $this->redirectResponse(static::URL_ANALYTICS);
        }

        $quicksightUserCollectionResponseTransfer = $this->getFacade()->createMatchedQuicksightUsers();

        if ($quicksightUserCollectionResponseTransfer->getErrors()->count() === 0) {
            $this->addSuccessMessage(static::SUCCESS_MESSAGE_USERS_SYNCHRONIZED);

            return $this->redirectResponse(static::URL_ANALYTICS);
        }

        foreach ($quicksightUserCollectionResponseTransfer->getErrors() as $errorTransfer) {
            $this->addErrorMessage($errorTransfer->getMessageOrFail());
        }

        return $this->redirectResponse(static::URL_ANALYTICS);
    }

    /**
     * @param string $tokenId
     * @param string $value
     *
     * @return bool
     */
    protected function validateCsrfToken(string $tokenId, string $value): bool
    {
        $csrfToken = new CsrfToken($tokenId, $value);

        return $this->getFactory()->getCsrfTokenManager()->isTokenValid($csrfToken);
    }
}
