<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Dependency\Facade;

use Generated\Shared\Transfer\MessageTransfer;

class AmazonQuicksightToMessengerFacadeBridge implements AmazonQuicksightToMessengerFacadeInterface
{
    /**
     * @var \Spryker\Zed\Messenger\Business\MessengerFacadeInterface
     */
    protected $messengerFacade;

    /**
     * @param \Spryker\Zed\Messenger\Business\MessengerFacadeInterface $messengerFacade
     */
    public function __construct($messengerFacade)
    {
        $this->messengerFacade = $messengerFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\MessageTransfer $message
     *
     * @return void
     */
    public function addErrorMessage(MessageTransfer $message): void
    {
        $this->messengerFacade->addErrorMessage($message);
    }
}
