<?php

namespace ServerGrove\LiveChatBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\Command;

/**
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
abstract class BaseCommand extends Command
{

    /**
     * @return Doctrine\ODM\MongoDB\DocumentManager
     */
    protected function getDocumentManager()
    {
        return $this->container->get('doctrine.odm.mongodb.document_manager');
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