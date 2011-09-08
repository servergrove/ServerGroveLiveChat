<?php

namespace ServerGrove\LiveChatBundle\Controller;

use ServerGrove\LiveChatBundle\Chat\ChatRequest;
use ServerGrove\LiveChatBundle\Document\Operator;
use ServerGrove\LiveChatBundle\Document\Operator\Rating;
use ServerGrove\LiveChatBundle\Document\User;
use ServerGrove\LiveChatBundle\Document\Visit;
use ServerGrove\LiveChatBundle\Document\Visitor;
use ServerGrove\LiveChatBundle\Document\Session;
use ServerGrove\LiveChatBundle\Document\CannedMessage;
use ServerGrove\LiveChatBundle\Form\ChatRequestType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Swift_Message;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Chat's main controller
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class ChatController extends PublicController
{

    /**
     * @Route("/sglivechat/{id}/accept", name="sglc_chat_accept")
     */
    public function acceptAction($id)
    {
        $operator = $this->getOperator();

        if (!$operator) {
            $this->getSessionStorage()->setFlash('errorMsg', 'Unauthorized access.');

            return new RedirectResponse($this->generateUrl('sglc_chat_homepage'));
        }

        if (!($chatSession = $this->getChatSession($id))) {
            $this->getSessionStorage()->setFlash('errorMsg', 'Chat not found');

            return new RedirectResponse($this->generateUrl('sglc_chat_homepage'));
        }

        if ($this->getRequest()->getMethod() == 'POST') {
            if (!$chatSession->getOperator() || $chatSession->getOperator()->getId() == $operator->getId()) {
                $chatSession->setOperator($operator);
                $chatSession->addChatMessage('You are now connected with ' . $operator->getName(), $operator);
                $chatSession->start();

                $this->getDocumentManager()->persist($chatSession);
                $this->getDocumentManager()->flush();

                $this->cacheUserForSession($operator, $chatSession);

                return new RedirectResponse($this->generateUrl('sglc_chat_load', array('id' => $chatSession->getId())));
            }
        }

        return $this->renderTemplate('ServerGroveLiveChatBundle:Chat:accept.html.twig', array(
            'chat' => $chatSession,
            'visitor' => $chatSession->getVisitor(),
            'messages' => $chatSession->getMessages(),
            'operator' => $operator)
        );
    }

    /**
     * @Route("/sglivechat/{id}/invite/accept", name="sglc_chat_invite_accept")
     */
    public function acceptInviteAction($id)
    {
        if (!($chatSession = $this->getChatSession($id))) {
            $this->getSessionStorage()->setFlash('errorMsg', 'No chat found. Session may have expired. Please start again.');

            return new RedirectResponse($this->generateUrl('sglc_chat_homepage'));
        }

        if ($chatSession->getStatusId() != Session::STATUS_INVITE) {
            $this->getSessionStorage()->setFlash('errorMsg', 'Invitation has expired or canceled. You can start a new chat now.');

            return new RedirectResponse($this->generateUrl('sglc_chat_homepage'));
        }

        $operator = $this->getOperator();
        $this->cacheUserForSession($operator ? $operator : $chatSession->getVisitor(), $chatSession);

        $this->getSessionStorage()->set('chatsession', $chatSession->getId());

        $chatSession->start();
        $this->getDocumentManager()->persist($chatSession);
        $this->getDocumentManager()->flush();

        return new RedirectResponse($this->generateUrl('sglc_chat_load', array(
                    'id' => $chatSession->getId())));
    }

    /**
     * @Route("/sglivechat/{id}/done", name="sglc_chat_done")
     */
    public function doneAction()
    {
        if (!$chatSession = $this->getChatSessionForCurrentUser(false)) {
            $this->getSessionStorage()->setFlash('errorMsg', 'No chat found. Please start again.');
            return new RedirectResponse($this->generateUrl('sglc_chat_homepage'));
        }

        $visitor = $this->getVisitorByKey();

        if ($this->getRequest()->getMethod() != "POST") {
            $chatSession->close();
            $this->getDocumentManager()->flush();
            return $this->renderTemplate('ServerGroveLiveChatBundle:Chat:done.html.twig', array(
                'enableSendTranscripts' => $this->has('mailer'),
                'email' => $visitor->getEmail())
            );
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
            /* @var $message ServerGrove\LiveChatBundle\Document\Message */
            foreach ($messages as $message) {
                $contents[] = sprintf('%s: %s', $message->getSender()->getKind(), $message->getContent());
            }

            $mailer = $this->get('mailer');
            $message = Swift_Message::newInstance()
                    ->setSubject('Transcripts for: ' . $chatSession->getQuestion())
                    ->setFrom(array('help@servergrove.com' => 'ServerGrove Support'))
                    ->setTo($this->getRequest()->get('email'))
                    ->setBody(implode(PHP_EOL, $contents));
            $mailer->send($message);
        }

        return $this->render('ServerGroveLiveChatBundle:Chat:rated.html.twig', array(
            'transcripts' => $this->getRequest()->get('transcripts', 0),
            'email' => $this->getRequest()->get('email')));
    }

    /**
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function faqAction()
    {
        return $this->renderTemplate('ServerGroveLiveChatBundle:Chat:faq.html.twig');
    }

    /**
     * @Route("/sglivechat", name="sglc_chat_homepage")
     */
    public function indexAction()
    {
        $visitor = $this->getVisitorByKey();

        if ($this->getOperator()) {
            $this->getResponse()->setContent('No chat found.');

            return $this->getResponse();
        }

        /* @var $form \ServerGrove\LiveChatBundle\Form\ChatRequest */
        $form = $this->get('form.factory')->create(new ChatRequestType());

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());
            $chatRequest = $form->getData();

            if ($form->isValid()) {
                $visitor->setEmail($chatRequest->getEmail());
                $visitor->setName($chatRequest->getName());
                $this->getDocumentManager()->persist($visitor);

                /* @var $chatSession ServerGrove\LiveChatBundle\Document\Session */
                $chatSession = new Session();
                $chatSession->setRemoteAddr($visitor->getRemoteAddr());
                $chatSession->setVisitor($visitor);

                $visit = $this->getVisitByKey($visitor);

                $chatSession->setVisit($visit);
                $chatSession->setStatusId(Session::STATUS_WAITING);
                $chatSession->setQuestion($chatRequest->getQuestion());
                $this->getDocumentManager()->persist($chatSession);

                $this->getDocumentManager()->flush();

                $this->getSessionStorage()->set('chatsession', $chatSession->getId());
                $this->cacheUserForSession($visitor, $chatSession);

                return new RedirectResponse($this->generateUrl('sglc_chat_load', array(
                            'id' => $chatSession->getId())));
            } else {
                $this->getResponse()->setStatusCode(403);
            }
        }

        return $this->renderTemplate('ServerGroveLiveChatBundle:Chat:index.html.twig', array(
            'visitor' => $visitor,
            'errorMsg' => $this->getSessionStorage()->getFlash('errorMsg', null),
            'form' => $form->createView()));
    }

    /**
     * @Route("/sglivechat/{sessId}/invite", name="sglc_chat_invite")
     */
    public function inviteAction($sessId)
    {
        $operator = $this->getOperator();

        if (!$operator) {
            $this->getSessionStorage()->setFlash('errorMsg', 'Unauthorized access.');

            return new RedirectResponse($this->generateUrl('sglc_chat_homepage'));
        }

        if (!($visit = $this->getDocumentManager()->getRepository('ServerGroveLiveChatBundle:Visit')->find($sessId))) {
            $this->getResponse()->setContent('Visit not found');

            return $this->getReponse();
        }

        $visitor = $visit->getVisitor();

        /* @var $chatSession ServerGrove\LiveChatBundle\Document\Session */
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

        return new RedirectResponse($this->generateUrl('sglc_chat_load', array('id' => $chatSession->getId())));
    }

    /**
     * @Route("/sglivechat/{id}/load", name="sglc_chat_load")
     */
    public function loadAction($id)
    {
        $operator = $this->getOperator();
        if (!($chatSession = $this->getChatSession($id))) {
            $this->getSessionStorage()->setFlash('errorMsg', 'No chat found. Session may have expired. Please start again.');
            return new RedirectResponse($this->generateUrl('sglc_chat_homepage'));
        }

        $arrCannedMessages = array();
        if ($operator) {
            if (($cannedMessages = $this->getCannedMessages()) !== false) {
                /* @var $cannedMessage ServerGrove\LiveChatBundle\Document\CannedMessage */
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
        return $this->renderTemplate('ServerGroveLiveChatBundle:Chat:load.html.twig', array(
            'chat' => $chatSession,
            'canned' => $arrCannedMessages,
            'user' => $user,
            'isOperator' => $user->getKind() == 'Operator')
        );
    }

    /**
     * @Route("/sglivechat/{id}/messages.{_format}", name="sglc_chat_messages", requirements={"_method"="get"}, defaults={"_format"="html"})
     */
    public function messagesAction($_format)
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
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
            $messages = $messages->slice($last);
        }

        if ($_format == 'json') {
            $json = array();
            foreach ($messages as $m) {
                $json['messages'][] = array(
                    'content' => $m->getContent(),
                    'name' => $m->getSender()->getKind(),
                    'dt' => $m->getCreatedAt(),
                    'isOperator' => $m->getSender() instanceof Operator
                );
            }

            if ($this->theOtherMemberIsTyping($chatSession)) {
                $user = $this->getUserForSession($chatSession);
                $json['action'] = $chatSession->getOtherMember($user)->getKind() . ' is typing';
            }

            $user = $this->getUserForSession($chatSession);

            if ($user->getKind() == 'Operator') {
                try {
                    $otherUser = $chatSession->getOtherMember($user);
                    if ($otherUser->getLastVisit()
                            && $otherUser->getLastVisit()->getLastHit()
                            && $otherUser->getLastVisit()->getLastHit()->getVisitLink()) {
                        $json['current_hit'] = $otherUser->getLastVisit()->getLastHit()->getVisitLink()->getUrl();
                    }
                } catch (Exception $e) {

                }
            }

            $this->getResponse()->setContent(json_encode($json));
            return $this->getResponse();
        }

        return $this->renderTemplate('ServerGroveLiveChatBundle:Chat:messages.' . $_format . '.twig', array(
            'messages' => $messages)
        );
    }

    /**
     * @Route("/sglivechat/{id}/invite/reject", name="sglc_chat_invite_reject")
     */
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

    /**
     * @Route("/sglivechat/{id}/send", name="sglc_chat_send", requirements={"_method"="post"})
     */
    public function sendAction($id)
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        if (!$chatSession = $this->getChatSession($id)) {
            $this->getSessionStorage()->setFlash('errorMsg', 'No chat found. Session may have expired. Please start again.');
            return new RedirectResponse($this->generateUrl('sglc_chat_homepage'));
        }

        $user = $this->getUserForSession($chatSession);
        $chatSession->addChatMessage(utf8_encode(urldecode($this->getRequest()->get('msg'))), $user);
        $this->userIsNotTyping($user, $chatSession);

        $this->getDocumentManager()->persist($chatSession);
        $this->getDocumentManager()->flush();

        $this->getResponse()->setContent($this->getRequest()->get('msg'));

        return $this->getResponse();
    }

    /**
     * @Route("/sglivechat/status", name="sglc_chat_status", requirements={"_method"="get"})
     */
    public function statusAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            //throw new NotFoundHttpException();
        }

        if (!$chatSession = $this->getChatSessionForCurrentUser()) {
            $this->getSessionStorage()->setFlash('errorMsg', 'No chat found. Session may have expired. Please start again.');
            $this->getResponse()->headers->set('Content-type', 'text/javascript');
            $this->getResponse()->setContent(sprintf('location.href = %s;', var_export($this->generateUrl('sglc_chat_homepage'), true)));

            return $this->getResponse();
        }

        if ($chatSession->getOperator()
                && $chatSession->getStatusId() == Session::STATUS_IN_PROGRESS
                && $this->getSessionStorage()->get('chatStatus' . $chatSession->getId()) != 'started') {

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

    /**
     * @Route("/sglivechat/{id}/user/action/{action}", name="sglc_chat_user_action")
     */
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

    /**
     * @param User $user
     * @param Session $chatSession
     */
    private function cacheUserForSession(User $user, Session $chatSession)
    {
        $this->getSessionStorage()->set('userId-' . $chatSession->getId(), $user->getId());
        $this->getSessionStorage()->set('userKind-' . $chatSession->getId(), $user->getKind());
    }

    /**
     * @return ServerGrove\LiveChatBundle\Cache\Manager
     */
    private function getCacheManager()
    {
        return $this->container->get('livechat.cache.manager');
    }

    /**
     * @return ServerGrove\LiveChatBundle\Document\CannedMessage[]
     */
    private function getCannedMessages()
    {
        return $this->getDocumentManager()->getRepository('ServerGroveLiveChatBundle:CannedMessage')->findAll();
    }

    /**
     * @param string $id        Session id
     * @param boolean $finished Whether or not the session has finished
     *
     * @return ServerGrove\LiveChatBundle\Document\Session
     */
    private function getChatSession($id, $finished = true)
    {
        if ($finished) {
            return $this->getDocumentManager()->getRepository('ServerGroveLiveChatBundle:Session')->getSessionIfNotFinished($id);
        }

        return $this->getDocumentManager()->getRepository('ServerGroveLiveChatBundle:Session')->find($id);
    }

    /**
     * @param boolean $finished Whether or not the session has finished
     *
     * @return ServerGrove\LiveChatBundle\Document\Session
     */
    private function getChatSessionForCurrentUser($finished = true)
    {
        $request = $this->getRequest();
        $session = $this->getSessionStorage();
        $id = $session->has('_operator') ? $request->get('id') : $session->get('chatsession');

        return $this->getChatSession($id, $finished);
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

        return $this->getDocumentManager()->find('ServerGroveLiveChatBundle:' . ($userKind == 'Client' ? 'Visitor' : 'Operator'), $userId);
    }

    /**
     * @param User $user
     * @param Session $chatSession
     */
    private function userIsTyping(User $user, Session $chatSession)
    {
        $this->getCacheManager()->set('chat.' . $chatSession->getId() . '.' . strtolower($user->getKind()) . '.typing', true, 3);
    }

    /**
     * @param User $user
     * @param Session $chatSession
     */
    private function userIsNotTyping(User $user, Session $chatSession)
    {
        $this->getCacheManager()->remove('chat.' . $chatSession->getId() . '.' . strtolower($user->getKind()) . '.typing');
    }

    /**
     * @param Session $chatSession
     * @return boolean
     */
    private function theOtherMemberIsTyping(Session $chatSession)
    {
        $user = $this->getUserForSession($chatSession);
        $otherUser = $chatSession->getOtherMember($user);
        $key = strtr('chat.:id.:kind.typing', array(
            ':id' => $chatSession->getId(),
            ':kind' => strtolower($otherUser->getKind())
                ));

        return $this->getCacheManager()->has($key);
    }

}