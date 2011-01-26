<?php

namespace Application\ChatBundle\Command;

use Application\ChatBundle\Document\Operator;

use Application\ChatBundle\Document\Administrator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Bundle\FrameworkBundle\Command\Command;

/**
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class AdminCommand extends Command
{

    /**
     * @see Command
     */
    protected function configure()
    {
        $this->setDefinition(array(new InputArgument('password', InputArgument::REQUIRED, 'The admin password'), new InputOption('old', 'o', InputOption::VALUE_OPTIONAL, 'The admin old password')))->setName('livechat:admin:change-password');
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $admin = $this->getAdministratorRepository()->findOneBy(array('name' => 'Administrator'));

        if (!$admin) {
            $admin = new Administrator();
            $admin->setName('Administrator');
        } else if (!$input->getOption('old')) {
            throw new \InvalidArgumentException('Admin user already exists. You must provide old password with --old="mypassword"');
        } else  if ($admin->getPasswd() != md5($input->getOption('old'))) {
            throw new \InvalidArgumentException('Wrong password');
        }

        $admin->setPasswd($input->getArgument('password'));

        $this->getDocumentManager()->persist($admin);
        $this->getDocumentManager()->flush();
    }

    /**
     * @return Doctrine\ODM\MongoDB\DocumentManager
     */
    private function getDocumentManager()
    {
        return $this->container->get('doctrine.odm.mongodb.document_manager');
    }

    private function getOperatorRepository()
    {
        return $this->getDocumentManager()->getRepository('ChatBundle:Operator');
    }

    private function getAdministratorRepository()
    {
        return $this->getDocumentManager()->getRepository('ChatBundle:Administrator');
    }

}