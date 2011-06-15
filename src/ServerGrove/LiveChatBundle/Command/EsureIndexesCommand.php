<?php
namespace ServerGrove\LiveChatBundle\Command;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputArgument;

class EsureIndexesCommand extends BaseCommand
{

    /**
     * @see Command
     */
    protected function configure()
    {
        $this->setDefinition(array())->setName('sglivechat:ensure-indexes');
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getDocumentManager()->getSchemaManager()->ensureIndexes();
    }
}