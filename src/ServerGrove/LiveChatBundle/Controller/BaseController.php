<?php

namespace ServerGrove\LiveChatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session as SessionStorage;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * Chat's base controller
 *
 * @method \ServerGrove\LiveChatBundle\Document\OperatorRepository getOperatorRepository
 * @method \ServerGrove\LiveChatBundle\Document\Operator\DepartmentRepository getOperatorDepartmentRepository
 * @method \ServerGrove\LiveChatBundle\Document\CannedMessageRepository getCannedMessageRepository
 * @method \ServerGrove\LiveChatBundle\Document\SessionRepository getSessionRepository
 * @method \ServerGrove\LiveChatBundle\Document\Operator\RatingRepository getRatingRepository
 * @method \ServerGrove\LiveChatBundle\Document\VisitRepository getVisitRepository
 * @method \ServerGrove\LiveChatBundle\Document\VisitorRepository getVisitorRepository
 * @method \ServerGrove\LiveChatBundle\Document\VisitLinkRepository getVisitLinkRepository
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
abstract class BaseController extends Controller
{

    private $request, $dm;

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->get('request');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Session
     */
    public function getSessionStorage()
    {
        return $this->getRequest()->getSession();
    }

    /**
     * @return \Doctrine\ODM\MongoDB\DocumentManager
     */
    public function getDocumentManager()
    {
        if (is_null($this->dm)) {
            $this->dm = $this->get('doctrine.odm.mongodb.document_manager');
        }

        return $this->dm;
    }

    public function __call($name, $args)
    {
        $out = null;
        if (preg_match('/^get([\w]+)Repository$/', $name, $out)) {
            $repository = $this->getDocumentManager()->getRepository(sprintf('ServerGroveLiveChatBundle:%s', $out[1]));
            if ($repository instanceof \Doctrine\ODM\MongoDB\DocumentRepository) {
                return $repository;
            }
        }

        throw new \BadFunctionCallException(sprintf('Call to non existent function "%s->%s()"', get_class($this), $name));
    }

    /**
     * @return \ServerGrove\LiveChatBundle\Document\Operator
     */
    protected function getOperator()
    {
        if (!$this->getSessionStorage()->has('_operator')) {
            return null;
        }

        return $this->getOperatorRepository()->find($this->getSessionStorage()->get('_operator'));
    }

    /**
     * @return \Symfony\Bundle\ZendBundle\Logger\Logger
     */
    protected function getLogger()
    {
        return $this->container->get('logger');
    }

}