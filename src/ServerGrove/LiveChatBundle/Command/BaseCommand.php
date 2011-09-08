<?php

namespace ServerGrove\LiveChatBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
abstract class BaseCommand extends ContainerAwareCommand
{

    /**
     * @return Doctrine\ODM\MongoDB\DocumentManager
     */
    protected function getDocumentManager()
    {
        return $this->getContainer()->get('doctrine.odm.mongodb.document_manager');
    }

    protected function getOperatorRepository()
    {
        return $this->getDocumentManager()->getRepository('ServerGroveLiveChatBundle:Operator');
    }

    protected function getAdministratorRepository()
    {
        return $this->getDocumentManager()->getRepository('ServerGroveLiveChatBundle:Administrator');
    }
}