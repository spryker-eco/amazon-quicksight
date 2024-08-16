<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\AmazonQuicksight\Communication\Console;

use Spryker\Zed\Kernel\Communication\Console\Console;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method \SprykerEco\Zed\AmazonQuicksight\Persistence\AmazonQuicksightRepositoryInterface getRepository()
 * @method \SprykerEco\Zed\AmazonQuicksight\Business\AmazonQuicksightFacadeInterface getFacade()
 * @method \SprykerEco\Zed\AmazonQuicksight\Communication\AmazonQuicksightCommunicationFactory getFactory()
 */
class SyncDeleteQuicksightUsersConsole extends Console
{
    /**
     * @var string
     */
    protected const COMMAND_NAME = 'quicksight-user:sync:delete';

    /**
     * @var string
     */
    protected const COMMAND_DESCRIPTION = 'Deletes registered quicksight users that can\'t be matched with backoffice users from persistence.';

    /**
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();

        $this->setName(static::COMMAND_NAME)
            ->setDescription(static::COMMAND_DESCRIPTION);
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $quicksightUserCollectionResponseTransfer = $this->getFacade()
            ->deleteRegisteredQuicksightUsersNotMatchedWithExistingUsers();

        if ($quicksightUserCollectionResponseTransfer->getErrors()->count() === 0) {
            return static::CODE_SUCCESS;
        }

        foreach ($quicksightUserCollectionResponseTransfer->getErrors() as $errorTransfer) {
            $this->output->writeln(
                sprintf('<error>%s</error>', $errorTransfer->getMessage()),
            );
        }

        return static::CODE_ERROR;
    }
}
