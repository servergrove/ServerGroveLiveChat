<?php

namespace Application\ChatBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Swift_Message;
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
class ChatController extends PublicController
{

    /**
     * @return Application\ChatBundle\Document\Session
     */
    private function getChatSession($id)
    {
        return $this->getDocumentManager()->getRepository('ChatBundle:Session')->getSessionIfNotFinished($id);
    }

    /**
     * @return Application\ChatBundle\Document\Session
     */
    public function getChatSessionForCurrentUser()
    {
        return $this->getChatSession($this->getHttpSession()->has('_operator') ? $this->getRequest()->get('id') : $this->getHttpSession()->get('chatsession'));
    }

    /**
     * @return Application\ChatBundle\Document\CannedMessage[]
     */
    private function getCannedMessages()
    {
        return $this->getDocumentManager()->getRepository('ChatBundle:CannedMessage')->findAll();
    }

    /**
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $visitor = $this->getVisitorByKey();

        if ($this->getOperator()) {
            $this->getResponse()->setContent('No chat found.');

            return $this->getResponse();
        }

        if ($this->getRequest()->getMethod() == 'POST') {
            $visitor->setEmail($this->getRequest()->get('email'));
            $visitor->setName($this->getRequest()->get('name'));
            $this->getDocumentManager()->persist($visitor);

            /* @var $chatSession Application\ChatBundle\Document\Session */
            $chatSession = new ChatSession();
            $chatSession->setRemoteAddr($visitor->getRemoteAddr());
            $chatSession->setVisitor($visitor);

            $visit = $this->getVisitByKey($visitor);

            $chatSession->setVisit($visit);
            $chatSession->setStatusId(ChatSession::STATUS_WAITING);
            $chatSession->setQuestion($this->getRequest()->get('question'));
            $this->getDocumentManager()->persist($chatSession);

            $this->getDocumentManager()->flush();

            $this->getHttpSession()->set('chatsession', $chatSession->getId());

            return $this->redirect($this->generateUrl('sglc_chat_load'));
        }

        return $this->renderTemplate('ChatBundle:Chat:index.twig.html', array(
            'visitor' => $visitor,
            'errorMsg' => $this->getHttpSession()->getFlash('errorMsg', null)));
    }

    public function acceptAction($id)
    {
        $operator = $this->getOperator();

        if (!$operator) {
            $this->getHttpSession()->setFlash('errorMsg', 'Unauthorized access.');

            return $this->redirect($this->generateUrl('sglc_chat_homepage'));
        }

        if (!($chatSession = $this->getChatSession($id))) {
            $this->getHttpSession()->setFlash('errorMsg', 'Chat not found');

            return $this->redirect($this->generateUrl('sglc_chat_homepage'));
        }

        if ($this->getRequest()->getMethod() == 'POST') {
            if (!$chatSession->getOperator() || $chatSession->getOperator()->getId() == $operator->getId()) {
                $chatSession->setOperator($operator);
                $chatSession->addChatMessage('You are now connected with ' . $operator->getName(), $operator);
                $chatSession->start();

                $this->getDocumentManager()->persist($chatSession);
                $this->getDocumentManager()->flush();

                return $this->redirect($this->generateUrl('sglc_chat_load', array(
                            'id' => $chatSession->getId())));
            }
        }

        return $this->renderTemplate('ChatBundle:Chat:accept.twig.html',
                array(
                    'chat' => $chatSession,
                    'visitor' => $chatSession->getVisitor(),
                    'messages' => $chatSession->getMessages(),
                    'operator' => $operator));
    }

    /**
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function loadAction()
    {
        $operator = $this->getOperator();
        if (!($chatSession = $this->getChatSessionForCurrentUser())) {
            $this->getHttpSession()->setFlash('errorMsg', 'No chat found. Session may have expired. Please start again.');
            return $this->redirect($this->generateUrl('sglc_chat_homepage'));
        }

        $arrCannedMessages = array();
        if ($operator) {
            if (($cannedMessages = $this->getCannedMessages()) !== false) {
                /* @var $cannedMessage Application\ChatBundle\Document\CannedMessage */
                foreach ($cannedMessages as $cannedMessage) {
                    $arrCannedMessages[] = $cannedMessage->renderContent(array(
                                'operator' => $operator,
                                'currtime' => date('H:i:s'),
                                'currdate' => date('m-d-Y')));
                }
            }
        }

        $this->getHttpSession()->set('chatStatus' . $chatSession->getId(), '');

        $this->getHttpSession()->set('lastMessageId', '');

        return $this->renderTemplate('ChatBundle:Chat:load.twig.html', array(
            'chat' => $chatSession,
            'canned' => $arrCannedMessages,
            'operator' => $operator));
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
            return $this->redirect($this->generateUrl('sglc_chat_homepage'));
        }

        $operator = $this->getOperator();
        $chatSession->addChatMessage($this->getRequest()->get('msg'), $operator);

        $this->getDocumentManager()->persist($chatSession);
        $this->getDocumentManager()->flush();

        return $this->renderTemplate('ChatBundle:Chat:send.twig.html', array(
            'texto' => $this->getRequest()->get('msg')));
    }

    public function messagesAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            //throw new NotFoundHttpException();
        }

        if (!$chatSession = $this->getChatSessionForCurrentUser()) {
            $this->getResponse()->setContent('No chat session found. <a href="' . $this->generateUrl('sglc_chat_homepage') . '">Please start a new chat</a>.<br />');
            return $this->getResponse();
        }

        $messages = $chatSession->getMessages();

        $this->getDocumentManager()->persist($chatSession);
        $this->getDocumentManager()->flush();

        $this->getHttpSession()->set('lastMessageId', $messages[count($messages) - 1]->getId());

        return $this->renderTemplate('ChatBundle:Chat:messages.twig.html', array(
            'messages' => $messages));
    }

    /**
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function doneAction()
    {
        if (!$chatSession = $this->getChatSessionForCurrentUser()) {
            $this->getHttpSession()->setFlash('errorMsg', 'No chat found. Session may have expired. Please start again.');
            return $this->redirect($this->generateUrl('sglc_chat_homepage'));
        }

        $visitor = $this->getVisitorByKey();

        if ($this->getRequest()->getMethod() != "POST") {
            $chatSession->close();

            return $this->renderTemplate('ChatBundle:Chat:done.twig.html', array(
                'email' => $visitor->getEmail()));
        }

        $rating = new Rating();
        $rating->setComments($this->getRequest()->get('comments'));
        $rating->setGrade($this->getRequest()->get('rating'));
        $rating->setSession($chatSession);
        if ($chatSession->getOperator()) {
            $rating->setOperator($chatSession->getOperator());
        }

        $this->getDocumentManager()->persist($rating);
        $this->getDocumentManager()->flush();

        if ($this->getRequest()->get('transcripts', 0)) {
            $messages = $chatSession->getMessages();
            $contents = array();
            /* @var $message Application\ChatBundle\Document\Message */
            foreach ($messages as $message) {
                $contents[] = sprintf('%s: %s', $message->getOperator() ? 'Operator' : 'User', $message->getContent());
            }

            $mailer = $this->get('mailer');
            $message = Swift_Message::newInstance()
                            ->setSubject('Transcripts for: ' . $chatSession->getQuestion())
                            ->setFrom(array('help@servergrove.com' => 'ServerGrove Support'))
                            ->setTo($this->getRequest()->get('email'))
                            ->setBody(implode(PHP_EOL, $contents));
            $mailer->send($message);
        }

        return $this->render('ChatBundle:Chat:rated.twig.html', array('transcripts' => $this->getRequest()->get('transcripts', 0), 'email' => $this->getRequest()->get('email')));
    }

    public function statusAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        if (!$chatSession = $this->getChatSessionForCurrentUser()) {
            $this->getHttpSession()->setFlash('errorMsg', 'No chat found. Session may have expired. Please start again.');
            $this->getResponse()->headers->set('Content-type', 'text/javascript');
            $this->getResponse()->setContent(sprintf('location.href = %s;', var_export($this->generateUrl('sglc_chat_homepage'), true)));

            return $this->getResponse();
        }

        if ($chatSession->getOperator() && $chatSession->getStatusId() == ChatSession::STATUS_IN_PROGRESS && $this->getHttpSession()->get('chatStatus' . $chatSession->getId()) != 'started') {
            $this->getHttpSession()->set('chatStatus' . $chatSession->getId(), 'started');
            $this->getResponse()->headers->set('Content-type', 'text/javascript');
            $this->getResponse()->setContent('Chat.get().start()');

            return $this->getResponse();
        }

        if ($chatSession->getStatusId() == ChatSession::STATUS_CLOSED || $chatSession->getStatusId() == ChatSession::STATUS_CANCELED) {
            $this->getHttpSession()->setFlash('errorMsg', 'Chat has been ' . $chatSession->getStatus());
            $this->getResponse()->headers->set('Content-type', 'text/javascript');

            $this->getResponse()->setContent(sprintf('location.href = %s;', var_export($this->generateUrl('sglc_chat_homepage'), true)));

            return $this->getResponse();
        }

        $this->getDocumentManager()->persist($chatSession);
        $this->getDocumentManager()->flush();

        $this->getResponse()->headers->set('Content-type', 'application/json; charset=utf-8');

        return $this->getResponse();
    }

}