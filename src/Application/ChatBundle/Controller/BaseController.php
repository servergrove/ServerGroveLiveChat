<?php
namespace Application\ChatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Session as HttpSession;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * Chat's base controller
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
abstract class BaseController extends Controller
{

    /**
     * @return Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->get('request');
    }

    /**
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        return $this->get('response');
    }

    /**
     * @return Symfony\Component\HttpFoundation\Session
     */
    public function getHttpSession()
    {
        return $this->getRequest()->getSession();
    }

    /**
     * @return Doctrine\ODM\MongoDB\DocumentManager
     */
    public function getDocumentManager()
    {
        return $this->get('doctrine.odm.mongodb.document_manager');
    }
}