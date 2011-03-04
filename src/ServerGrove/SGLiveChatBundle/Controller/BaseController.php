<?php

namespace ServerGrove\SGLiveChatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Session as SessionStorage;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * Chat's base controller
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
abstract class BaseController extends Controller
{

    private $request, $response, $session, $dm;

    /**
     * @return Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        if (is_null($this->request)) {
            $this->request = $this->get('request');
        }
        return $this->request;
    }

    /**
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        if (is_null($this->response)) {
            $this->response = new Response();
        }
        return $this->response;
    }

    /**
     * @return Symfony\Component\HttpFoundation\Session
     */
    public function getSessionStorage()
    {
        if (is_null($this->session)) {
            $this->session = $this->getRequest()->getSession();
        }
        return $this->session;
    }

    /**
     * @return Doctrine\ODM\MongoDB\DocumentManager
     */
    public function getDocumentManager()
    {
        if (is_null($this->dm)) {
            $this->dm = $this->get('doctrine.odm.mongodb.document_manager');
        }
        return $this->dm;
    }

    /**
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function renderTemplate($view, array $parameters = array())
    {
        return $this->render($view, $parameters, $this->getResponse());
    }

    /**
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function redirect($url, $status = 302)
    {
        $this->getResponse()->setRedirect($url, $status);
        return $this->getResponse();
    }

    /**
     * @return ServerGrove\SGLiveChatBundle\Document\Operator
     */
    protected function getOperator()
    {
        if (!$this->getSessionStorage()->has('_operator')) {
            return null;
        }
        return $this->getDocumentManager()->find('SGLiveChatBundle:Operator', $this->getSessionStorage()->get('_operator'));
    }

    /**
     * @return Symfony\Bundle\ZendBundle\Logger\Logger
     */
    protected function getLogger()
    {
        return $this->container->get('logger');
    }

}