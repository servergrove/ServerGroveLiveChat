<?php

namespace ServerGrove\SGLiveChatBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ServerGrove\SGLiveChatBundle\Document\CannedMessage;
use Symfony\Component\Console\Input\InputArgument;
use MongoCursorException;

/**
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class AddCannedMessageCommand extends BaseCommand
{

    /**
     * @see Command
     */
    protected function configure()
    {
        $this->setDefinition(array(
            new InputArgument('title', InputArgument::REQUIRED, 'The operator name'),
            new InputArgument('content', InputArgument::REQUIRED, 'The operator e-mail')))->setName('sglivechat:admin:add-canned-message')->setDescription('Create new Canned Message');
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->getDocumentManager()->getSchemaManager()->ensureIndexes();
            $output->setDecorated(true);
            $cannedMessage = new CannedMessage();
            $cannedMessage->setTitle($input->getArgument('title'));
            $cannedMessage->setContent($input->getArgument('content'));

            $this->getDocumentManager()->persist($cannedMessage);
            $this->getDocumentManager()->flush(array('safe' => true));

            $output->write("Pronto", true);
        } catch (MongoCursorException $e) {
            $output->write($e->getMessage(), true);
        }
    }

}