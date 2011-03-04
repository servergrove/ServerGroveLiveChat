<?php

namespace ServerGrove\SGLiveChatBundle\Controller;

use ServerGrove\SGLiveChatBundle\Document\User;
use ServerGrove\SGLiveChatBundle\Document\Operator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Swift_Message;
use ServerGrove\SGLiveChatBundle\Document\Operator\Rating;
use ServerGrove\SGLiveChatBundle\Document\Visit;
use ServerGrove\SGLiveChatBundle\Document\Visitor;
use ServerGrove\SGLiveChatBundle\Document\Session;
use ServerGrove\SGLiveChatBundle\Document\CannedMessage;
use Exception;

/**
 * Chat's main controller
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class ChatController extends PublicController
{

    /**
     * @return ServerGrove\SGLiveChatBundle\Document\Session
     */
    private function getChatSession($id)
    {
        return $this->getDocumentManager()->getRepository('SGLiveChatBundle:Session')->getSessionIfNotFinished($id);
    }

    /**
     * @return ServerGrove\SGLiveChatBundle\Document\Session
     */
    public function getChatSessionForCurrentUser()
    {
        return $this->getChatSession($this->getSessionStorage()->has('_operator') ? $this->getRequest()->get('id') : $this->getSessionStorage()->get('chatsession'));
    }

    /**
     * @return ServerGrove\SGLiveChatBundle\Document\CannedMessage[]
     */
    private function getCannedMessages()
    {
        return $this->getDocumentManager()->getRepository('SGLiveChatBundle:CannedMessage')->findAll();
    }

    private function cacheUserForSession(User $user, Session $chatSession)
    {
        $this->getSessionStorage()->set('userId-' . $chatSession->getId(), $user->getId());
        $this->getSessionStorage()->set('userKind-' . $chatSession->getId(), $user->getKind());
    }

    /**
     * @param Session $chatSession
     * @return User
     */
    private function getUserForSession(Session $chatSession)
    {
        if (!$this->getSessionStorage()->has('userId-' . $chatSession->getId()) || !$this->getSessionStorage()->has('userKind-' . $chatSession->getId())) {
            throw new Exception('No user stored');
        }

        $userId = $this->getSessionStorage()->get('userId-' . $chatSession->getId());
        $userKind = $this->getSessionStorage()->get('userKind-' . $chatSession->getId());
        return $this->getDocumentManager()->find('SGLiveChatBundle:' . ($userKind == 'Guest' ? 'Visitor' : 'Operator'), $userId);
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

            /* @var $chatSession ServerGrove\SGLiveChatBundle\Document\Session */
            $chatSession = new Session();
            $chatSession->setRemoteAddr($visitor->getRemoteAddr());
            $chatSession->setVisitor($visitor);

            $visit = $this->getVisitByKey($visitor);

            $chatSession->setVisit($visit);
            $chatSession->setStatusId(Session::STATUS_WAITING);
            $chatSession->setQuestion($this->getRequest()->get('question'));
            $this->getDocumentManager()->persist($chatSession);

            $this->getDocumentManager()->flush();

            $this->getSessionStorage()->set('chatsession', $chatSession->getId());
            $this->cacheUserForSession($visitor, $chatSession);

            return $this->redirect($this->generateUrl('sglc_chat_load', array(
                'id' => $chatSession->getId())));
        }

        return $this->renderTemplate('SGLiveChatBundle:Chat:index.html.twig', array(
            'visitor' => $visitor,
            'errorMsg' => $this->getSessionStorage()->getFlash('errorMsg', null)));
    }

    public function inviteAction($sessId)
    {
        $operator = $this->getOperator();

        if (!$operator) {
            $this->getSessionStorage()->setFlash('errorMsg', 'Unauthorized access.');

            return $this->redirect($this->generateUrl('sglc_chat_homepage'));
        }

        if (!($visit = $this->getDocumentManager()->getRepository('SGLiveChatBundle:Visit')->find($sessId))) {
            $this->getResponse()->setContent('Visit not found');

            return $this->getReponse();
        }

        $visitor = $visit->getVisitor();

        /* @var $chatSession ServerGrove\SGLiveChatBundle\Document\Session */
        $chatSession = new Session();
        $chatSession->setRemoteAddr($visitor->getRemoteAddr());

        $chatSession->setVisitor($visitor);
        $chatSession->setVisit($visit);
        $chatSession->setOperator($operator);

        $chatSession->setStatusId(Session::STATUS_INVITE);

        # @todo Add canned message as first chat message
        $this->getDocumentManager()->persist($chatSession);
        $this->getDocumentManager()->flush();

        $this->cacheUserForSession($operator, $chatSession);

        return $this->redirect($this->generateUrl('sglc_chat_load', array(
            'id' => $chatSession->getId())));
    }

    public function acceptInviteAction($id)
    {
        if (!($chatSession = $this->getChatSession($id))) {
            $this->getSessionStorage()->setFlash('errorMsg', 'No chat found. Session may have expired. Please start again.');

            return $this->redirect($this->generateUrl('sglc_chat_homepage'));
        }

        if ($chatSession->getStatusId() != Session::STATUS_INVITE) {
            $this->getSessionStorage()->setFlash('errorMsg', 'Invitation has expired or canceled. You can start a new chat now.');

            return $this->redirect($this->generateUrl('sglc_chat_homepage'));
        }

        $operator = $this->getOperator();
        $this->cacheUserForSession($operator ? $operator : $chatSession->getVisitor(), $chatSession);

        $this->getSessionStorage()->set('chatsession', $chatSession->getId());

        $chatSession->start();
        $this->getDocumentManager()->persist($chatSession);
        $this->getDocumentManager()->flush();

        return $this->redirect($this->generateUrl('sglc_chat_load', array(
            'id' => $chatSession->getId())));
    }

    public function rejectInviteAction($id)
    {
        if (!($chatSession = $this->getChatSession($id))) {
            $this->getSessionStorage()->setFlash('errorMsg', 'No chat found. Session may have expired. Please start again.');

            return $this->getResponse();
        }

        $chatSession->cancel();
        $this->getDocumentManager()->persist($chatSession);
        $this->getDocumentManager()->flush();

        return $this->getResponse();
    }

    public function acceptAction($id)
    {
        $operator = $this->getOperator();

        if (!$operator) {
            $this->getSessionStorage()->setFlash('errorMsg', 'Unauthorized access.');

            return $this->redirect($this->generateUrl('sglc_chat_homepage'));
        }

        if (!($chatSession = $this->getChatSession($id))) {
            $this->getSessionStorage()->setFlash('errorMsg', 'Chat not found');

            return $this->redirect($this->generateUrl('sglc_chat_homepage'));
        }

        if ($this->getRequest()->getMethod() == 'POST') {
            if (!$chatSession->getOperator() || $chatSession->getOperator()->getId() == $operator->getId()) {
                $chatSession->setOperator($operator);
                $chatSession->addChatMessage('You are now connected with ' . $operator->getName(), $operator);
                $chatSession->start();

                $this->getDocumentManager()->persist($chatSession);
                $this->getDocumentManager()->flush();

                $this->cacheUserForSession($operator, $chatSession);

                return $this->redirect($this->generateUrl('sglc_chat_load', array(
                    'id' => $chatSession->getId())));
            }
        }

        return $this->renderTemplate('SGLiveChatBundle:Chat:accept.html.twig', array(
            'chat' => $chatSession,
            'visitor' => $chatSession->getVisitor(),
            'messages' => $chatSession->getMessages(),
            'operator' => $operator));
    }

    /**
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function loadAction($id)
    {
        $operator = $this->getOperator();
        if (!($chatSession = $this->getChatSessionForCurrentUser())) {
            $this->getSessionStorage()->setFlash('errorMsg', 'No chat found. Session may have expired. Please start again.');
            return $this->redirect($this->generateUrl('sglc_chat_homepage'));
        }

        $arrCannedMessages = array();
        if ($operator) {
            if (($cannedMessages = $this->getCannedMessages()) !== false) {
                /* @var $cannedMessage ServerGrove\SGLiveChatBundle\Document\CannedMessage */
                foreach ($cannedMessages as $cannedMessage) {
                    $arrCannedMessages[] = $cannedMessage->renderContent(array(
                        'operator' => $operator,
                        'currtime' => date('H:i:s'),
                        'currdate' => date('m-d-Y')));
                }
            }
        }

        $this->getSessionStorage()->set('chatStatus' . $chatSession->getId(), '');

        $this->getSessionStorage()->set('lastMessage', 0);

        $user = $this->getUserForSession($chatSession);
        return $this->renderTemplate('SGLiveChatBundle:Chat:load.html.twig', array(
            'chat' => $chatSession,
            'canned' => $arrCannedMessages,
            'user' => $user,
            'isOperator' => $user->getKind() == 'Operator'));
    }

    /**
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function faqAction()
    {
        return $this->renderTemplate('SGLiveChatBundle:Chat:faq.html.twig');
    }

    public function sendAction($id)
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        if (!$chatSession = $this->getChatSession($id)) {
            $this->getSessionStorage()->setFlash('errorMsg', 'No chat found. Session may have expired. Please start again.');
            return $this->redirect($this->generateUrl('sglc_chat_homepage'));
        }

        $user = $this->getUserForSession($chatSession);
        $chatSession->addChatMessage(utf8_encode(urldecode($this->getRequest()->get('msg'))), $user);
        $this->userIsNotTyping($user, $chatSession);

        $this->getDocumentManager()->persist($chatSession);
        $this->getDocumentManager()->flush();

        $this->getResponse()->setContent($this->getRequest()->get('msg'));

        return $this->getResponse();
    }

    public function messagesAction($_format)
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            //throw new NotFoundHttpException();
        }

        $all = (bool) $this->getRequest()->get('all');

        if (!$chatSession = $this->getChatSessionForCurrentUser()) {
            $this->getResponse()->setStatusCode(404, 'Chat session not found');
            return $this->getResponse();
        }

        $messages = $chatSession->getMessages();

        $this->getDocumentManager()->persist($chatSession);
        $this->getDocumentManager()->flush();

        if ($this->getSessionStorage()->has('lastMessage')) {
            $last = $this->getSessionStorage()->get('lastMessage');
        } else {
            $last = 0;
        }
        $this->getSessionStorage()->set('lastMessage', count($messages));

        if ($last) {
            $messages = array_slice($messages->toArray(), $last);
        }

        if ($_format == 'json') {
            $json = array();
            foreach ($messages as $m) {
                $json['messages'][] = array(
                    'content' => $m->getContent(),
                    'name' => $m->getSender()->getKind(),
                    'dt' => $m->getCreatedAt(),
                    'isOperator' => $m->getSender() instanceof Operator);
            }

            if ($this->theOtherMemberIsTyping($chatSession)) {
                $user = $this->getUserForSession($chatSession);
                $json['action'] = $chatSession->getOtherMember($user)->getKind() . ' is typing';
            }

            $this->getResponse()->setContent(json_encode($json));
            return $this->getResponse();
        }

        return $this->renderTemplate('SGLiveChatBundle:Chat:messages.' . $_format . '.twig', array(
            'messages' => $messages));
    }

    /**
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function doneAction()
    {
        if (!$chatSession = $this->getChatSessionForCurrentUser()) {
            $this->getSessionStorage()->setFlash('errorMsg', 'No chat found. Session may have expired. Please start again.');
            return $this->redirect($this->generateUrl('sglc_chat_homepage'));
        }

        $visitor = $this->getVisitorByKey();

        if ($this->getRequest()->getMethod() != "POST") {
            $chatSession->close();
            $this->getDocumentManager()->flush();
            return $this->renderTemplate('SGLiveChatBundle:Chat:done.html.twig', array(
                'enableSendTranscripts' => $this->has('mailer'),
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
            /* @var $message ServerGrove\SGLiveChatBundle\Document\Message */
            foreach ($messages as $message) {
                $contents[] = sprintf('%s: %s', $message->getSender()->getKind(), $message->getContent());
            }

            $mailer = $this->get('mailer');
            $message = Swift_Message::newInstance()->setSubject('Transcripts for: ' . $chatSession->getQuestion())->setFrom(array(
                'help@servergrove.com' => 'ServerGrove Support'))->setTo($this->getRequest()->get('email'))->setBody(implode(PHP_EOL, $contents));
            $mailer->send($message);
        }

        return $this->render('SGLiveChatBundle:Chat:rated.html.twig', array(
            'transcripts' => $this->getRequest()->get('transcripts', 0),
            'email' => $this->getRequest()->get('email')));
    }

    public function statusAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        if (!$chatSession = $this->getChatSessionForCurrentUser()) {
            $this->getSessionStorage()->setFlash('errorMsg', 'No chat found. Session may have expired. Please start again.');
            $this->getResponse()->headers->set('Content-type', 'text/javascript');
            $this->getResponse()->setContent(sprintf('location.href = %s;', var_export($this->generateUrl('sglc_chat_homepage'), true)));

            return $this->getResponse();
        }

        if ($chatSession->getOperator() && $chatSession->getStatusId() == Session::STATUS_IN_PROGRESS && $this->getSessionStorage()->get('chatStatus' . $chatSession->getId()) != 'started') {
            $this->getSessionStorage()->set('chatStatus' . $chatSession->getId(), 'started');
            $this->getResponse()->headers->set('Content-type', 'text/javascript');
            $this->getResponse()->setContent('Chat.get().start();');

            return $this->getResponse();
        }

        if ($chatSession->getStatusId() == Session::STATUS_CLOSED || $chatSession->getStatusId() == Session::STATUS_CANCELED) {
            $this->getSessionStorage()->setFlash('errorMsg', 'Chat has been ' . $chatSession->getStatus());
            $this->getResponse()->headers->set('Content-type', 'text/javascript');

            $this->getResponse()->setContent(sprintf('location.href = %s;', var_export($this->generateUrl('sglc_chat_homepage'), true)));

            return $this->getResponse();
        }

        $this->getDocumentManager()->persist($chatSession);
        $this->getDocumentManager()->flush();

        $this->getResponse()->headers->set('Content-type', 'application/javascript; charset=utf-8');

        return $this->getResponse();
    }

    public function userActionAction($id, $action)
    {
        if (!$chatSession = $this->getChatSessionForCurrentUser()) {
            throw new NotFoundHttpException();
        }

        $user = $this->getUserForSession($chatSession);

        switch ($action) {
            case 'typing':
                $this->userIsTyping($user, $chatSession);
                break;
        }

        return $this->getResponse();
    }

    private function userIsTyping(User $user, Session $chatSession)
    {
        $this->getCacheManager()->set('chat.' . $chatSession->getId() . '.' . strtolower($user->getKind()) . '.typing', true, 3);
    }

    private function userIsNotTyping(User $user, Session $chatSession)
    {
        $this->getCacheManager()->remove('chat.' . $chatSession->getId() . '.' . strtolower($user->getKind()) . '.typing');
    }

    private function theOtherMemberIsTyping(Session $chatSession)
    {
        $user = $this->getUserForSession($chatSession);
        return $this->getCacheManager()->has('chat.' . $chatSession->getId() . '.' . strtolower($chatSession->getOtherMember($user)->getKind()) . '.typing');
    }

    /**
     * @return ServerGrove\SGLiveChatBundle\Cache\Manager
     */
    private function getCacheManager()
    {
        return $this->container->get('livechat.cache.manager');
    }

}