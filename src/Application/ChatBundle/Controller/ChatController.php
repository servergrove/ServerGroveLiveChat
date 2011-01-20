<?php

namespace Application\ChatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Application\ChatBundle\Document\Operator\Rating;
use Application\ChatBundle\Document\Visit;
use Application\ChatBundle\Document\Visitor;
use Application\ChatBundle\Document\Session;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * Chat's main controller
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class ChatController extends Controller
{

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->get('request');
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->get('response');
    }

    /**
     * @return DocumentManager
     */
    public function getDocumentManager()
    {
        return $this->get('doctrine.odm.mongodb.document_manager');
    }

    /**
     * @return Visitor
     */
    public function createVisitor()
    {
        $visitor = new Visitor();
        $visitor->setAgent($_SERVER['HTTP_USER_AGENT']);
        $visitor->setKey(md5(time() . $visitor->getAgent() . rand(0, 100)));
        $visitor->setRemoteAddr($this->getRequest()->getClientIp());
        $visitor->setLanguages(\implode(';', $this->getRequest()->getLanguages()));

        return $visitor;
    }

    /**
     * @return Visitor
     */
    public function getVisitorByKey()
    {
        $key = $this->getRequest()->cookies->get('vtrid');
        $visitor = null;
        if (!is_null($key)) {
            $visitor = $this->getDocumentManager()->getRepository('ChatBundle:Visitor')->findOneBy(array('key' => $key));
        }

        if (!$visitor) {
            $visitor = $this->createVisitor();
            $this->getDocumentManager()->persist($visitor);
            $this->getDocumentManager()->flush();
            #$this->getResponse()->headers->setCookie(new Cookie('vtrid', $visitor->getKey(), mktime(0, 0, 0, 12, 31, 2020), '/'));
            setcookie('vtrid', $visitor->getKey(), mktime(0, 0, 0, 12, 31, 2020), '/'); # TODO Use $response()->headers->setCookie();
        }

        return $visitor;
    }

    public function createVisit(Visitor $visitor)
    {
        $visit = new Visit();
        $visit->setVisitor($visitor);
        $visit->setKey(md5(time() . $visitor->getAgent() . $visitor->getId()));
        #$visit->setLocalTime($local_time);

        return $visit;
    }

    /**
     * @return Visit
     */
    public function getVisitByKey(Visitor $visitor)
    {
        $key = $this->getRequest()->cookies->get('vsid');
        $visit = null;
        if (!is_null($key)) {
            $visit = $this->getDocumentManager()->getRepository('ChatBundle:Visit')->findOneBy(array('key' => $key));
        }

        if (!$visit) {
            $visit = $this->createVisit($visitor);
            $this->getDocumentManager()->persist($visit);
            $this->getDocumentManager()->flush();
            #$this->getResponse()->headers->setCookie(new \Symfony\Component\HttpFoundation\Cookie('vsid', $visit->getKey(), time() + 86400, '/'));
            setcookie('vsid', $visit->getKey(), time() + 86400, '/'); # TODO Use $response()->headers->setCookie();
        }

        return $visitor;
    }

    public function indexAction()
    {
        $visitor = $this->getVisitorByKey();

        if ($this->getRequest()->getMethod() == 'POST') {
            $visitor->setEmail($this->getRequest()->get('email'));
            $visitor->setName($this->getRequest()->get('name'));


            $session = new Session();
            $session->setRemoteAddr($visitor->getRemoteAddr());
            $session->setVisitorId($visitor->getId());
            $session->setVisitId($this->getVisitByKey($visitor)->getId());

            $this->getDocumentManager()->persist($session);
            $this->getDocumentManager()->flush();

            return $this->redirect($this->generateUrl('chat_load'));
        }

        /*
          $chatsession = new \Application\ChatBundle\Document\ChatSession();
          $chatsession->setVisitId(1);
          $chatsession->setRemoteAddr($_SERVER['REMOTE_ADDR']);

          $chatsession->addChatMessage("Hola ismael");

          $chatsession->addChatMessage("Como estas?");

          $dm = $this->get('doctrine.odm.mongodb.document_manager');
          $dm->persist($chatsession);
          $dm->flush();
         */
        return $this->render('ChatBundle:Chat:index.twig.html', array(), $this->getResponse());
    }

    public function loadAction()
    {
        return $this->render('ChatBundle:Chat:load.twig.html', array(), $this->getResponse());
    }

    public function doneAction()
    {
        if ($this->getRequest()->getMethod() == "POST") {
            return $this->render('ChatBundle:Chat:rated.twig.html', array(), $this->getResponse());
        }

        return $this->render('ChatBundle:Chat:done.twig.html', array(), $this->getResponse());
    }

}