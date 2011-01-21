<?php

namespace Application\ChatBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Application\ChatBundle\Document\Operator\Rating;
use Application\ChatBundle\Document\Visit;
use Application\ChatBundle\Document\Visitor;
use Application\ChatBundle\Document\Session as ChatSession;
use Application\ChatBundle\Document\CannedMessage;

/**
 * Chat's main controller
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class ChatController extends BaseController
{

    /**
     * @return Application\ChatBundle\Document\Visitor
     */
    public function createVisitor()
    {
        $visitor = new Visitor();
        $visitor->setAgent($_SERVER['HTTP_USER_AGENT']);
        $visitor->setKey(md5(time() . $visitor->getAgent() . rand(0, 100)));
        $visitor->setRemoteAddr($this->getRequest()->getClientIp());
        $visitor->setLanguages(implode(';', $this->getRequest()->getLanguages()));

        return $visitor;
    }

    /**
     * @return Application\ChatBundle\Document\Visitor
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

    /**
     * @return Application\ChatBundle\Document\Visit
     */
    public function createVisit(Visitor $visitor)
    {
        $visit = new Visit();
        $visit->setVisitor($visitor);
        $visit->setKey(md5(time() . $visitor->getAgent() . $visitor->getId()));
        #$visit->setLocalTime($local_time);


        return $visit;
    }

    /**
     * @return Application\ChatBundle\Document\Visit
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

    /**
     * @return Application\ChatBundle\Document\Session
     */
    public function getChatSession($id)
    {
        return $this->getDocumentManager()->getRepository('ChatBundle:Session')->find($id);
    }

    /**
     * @return Application\ChatBundle\Document\Operator
     */
    public function getOperator()
    {
        if (!$this->getHttpSession()->has('operator')) {
            return null;
        }
        return $this->getDocumentManager()->getRepository('ChatBundle:Operator')->find($this->getHttpSession()->get('operator'));
    }

    /**
     * @return Application\ChatBundle\Document\CannedMessage[]
     */
    public function getCannedMessages()
    {
        return $this->getDocumentManager()->getRepository('ChatBundle:CannedMessage')->findAll();
    }

    /**
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $visitor = $this->getVisitorByKey();


        if ($this->getRequest()->getMethod() == 'POST') {
            $visitor->setEmail($this->getRequest()->get('email'));
            $visitor->setName($this->getRequest()->get('name'));

            $this->getDocumentManager()->persist($visitor);
            $this->getDocumentManager()->flush();

            /* @var $chatSession Application\ChatBundle\Document\Session */
            $chatSession = new ChatSession();
            $chatSession->setRemoteAddr($visitor->getRemoteAddr());
            $chatSession->setVisitorId($visitor->getId());
            $chatSession->setVisitId($this->getVisitByKey($visitor)->getId());

            $this->getDocumentManager()->persist($chatSession);
            $this->getDocumentManager()->flush();

            $this->getHttpSession()->set('chatsession', $chatSession->getId());

            return $this->redirect($this->generateUrl('chat_load'));
        }

        return $this->renderTemplate('ChatBundle:Chat:index.twig.html', array('visitor' => $visitor));
    }

    public function getChatSessionForCurrentUser()
    {
        return $this->getChatSession($this->getHttpSession()->has('operator') ? $this->getRequest()->get('id') : $this->getHttpSession()->get('chatsession'));
    }

    /**
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function loadAction()
    {
        $operator = $this->getOperator();

        if (!$chatSession = $this->getChatSessionForCurrentUser()) {
            $this->getHttpSession()->setFlash('errorMsg', 'No chat found. Session may have expired. Please start again.');
            return $this->redirect($this->generateUrl('chat_homepage'));
        }

        $arrCannedMessages = array();
        if ($operator) {
            if (($cannedMessages = $this->getCannedMessages()) !== false) {
                /* @var $cannedMessage Application\ChatBundle\Document\CannedMessage */
                foreach ($cannedMessages as $cannedMessage) {
                    $arrCannedMessages[] = $cannedMessage->renderContent(array(
                                'operator' => $operator,
                                'currtime' => date('H:i:s'),
                                'currdate' => date('m-d-Y'),
                            ));
                }
            }
        }

        return $this->renderTemplate('ChatBundle:Chat:load.twig.html', array('chat' => $chatSession, 'canned' => $arrCannedMessages, 'operator' => $operator));
    }

    /**
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function faqAction()
    {
        return $this->renderTemplate('ChatBundle:Chat:faq.twig.html');
    }

    public function sendAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        if (!$chatSession = $this->getChatSessionForCurrentUser()) {
            $this->getHttpSession()->setFlash('errorMsg', 'No chat found. Session may have expired. Please start again.');
            return $this->redirect($this->generateUrl('chat_homepage'));
        }

        $operator = $this->getOperator();
        $chatOperatorId = $operator ? $operator->getId() : null;
        $chatSession->addChatMessage($this->getRequest()->get('msg'), $chatOperatorId);

        $this->getDocumentManager()->persist($chatSession);
        $this->getDocumentManager()->flush();

        return $this->renderTemplate('ChatBundle:Chat:send.twig.html', array('texto' => $this->getRequest()->get('msg')));
    }

    public function messagesAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        if (!$chatSession = $this->getChatSessionForCurrentUser()) {
            $this->getHttpSession()->setFlash('errorMsg', 'No chat found. Session may have expired. Please start again.');
            throw new NotFoundHttpException();
        }

        $messages = $chatSession->getMessages();

        return $this->renderTemplate('ChatBundle:Chat:messages.twig.html', array('messages' => $messages));
    }

    /**
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function doneAction()
    {
        if (!$chatSession = $this->getChatSessionForCurrentUser()) {
            $this->getHttpSession()->setFlash('errorMsg', 'No chat found. Session may have expired. Please start again.');
            return $this->redirect($this->generateUrl('chat_homepage'));
        }

        $visitor = $this->getVisitorByKey();

        if ($this->getRequest()->getMethod() != "POST") {
            return $this->renderTemplate('ChatBundle:Chat:done.twig.html', array('email' => $visitor->getEmail()));
        }

        return $this->render('ChatBundle:Chat:rated.twig.html');
    }

    /**
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function renderTemplate($view, array $parameters = array())
    {
        return $this->render($view, $parameters, $this->getResponse());
    }

}