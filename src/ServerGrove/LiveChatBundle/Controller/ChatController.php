<?php

namespace ServerGrove\LiveChatBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ServerGrove\LiveChatBundle\Chat\ChatRequest;
use ServerGrove\LiveChatBundle\Document\CannedMessage;
use ServerGrove\LiveChatBundle\Document\Operator;
use ServerGrove\LiveChatBundle\Document\Operator\Rating;
use ServerGrove\LiveChatBundle\Document\Session;
use ServerGrove\LiveChatBundle\Document\User;
use ServerGrove\LiveChatBundle\Document\Visit;
use ServerGrove\LiveChatBundle\Document\Visitor;
use ServerGrove\LiveChatBundle\Form\ChatRequestType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Swift_Message;

/**
 * Chat's main controller
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class ChatController extends PublicController
{

    /**
     * @Route("/sglivechat/{id}/accept", name="sglc_chat_accept")
     * @Template
     * @param $id
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
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

                return $this->redirect($this->generateUrl('sglc_chat_load', array('id' => $chatSession->getId())));
            }
        }

        return array(
            'chat' => $chatSession,
            'visitor' => $chatSession->getVisitor(),
            'messages' => $chatSession->getMessages(),
            'operator' => $operator
        );
    }

    /**
     * @Route("/sglivechat/{id}/invite/accept", name="sglc_chat_invite_accept")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
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

        return $this->redirect($this->generateUrl('sglc_chat_load', array('id' => $chatSession->getId())));
    }

    /**
     * @Route("/sglivechat/{id}/done", name="sglc_chat_done")
     * @Template
     * @return array|\Symfony\Bundle\FrameworkBundle\Controller\Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function doneAction()
    {
        if (!$chatSession = $this->getChatSessionForCurrentUser(false)) {
            $this->getSessionStorage()->setFlash('errorMsg', 'No chat found. Please start again.');
            return $this->redirect($this->generateUrl('sglc_chat_homepage'));
        }

        $visitor = $this->getVisitorByKey();

        if ($this->getRequest()->getMethod() != "POST") {
            $chatSession->close();
            $this->getDocumentManager()->flush();
            
            return array(
                'enableSendTranscripts' => $this->has('mailer'),
                'email' => $visitor->getEmail()
            );
        }

        $rating = $chatSession->getRating();
        $rating->setComments($this->getRequest()->get('comments'));
        $rating->setGrade($this->getRequest()->get('rating'));
        if ($chatSession->getOperator()) {
            $rating->setOperator($chatSession->getOperator());
        }
        $this->getDocumentManager()->persist($rating);
        $this->getDocumentManager()->flush();

        if ($this->getRequest()->get('transcripts', 0)) {
            $messages = $chatSession->getMessages();
            $contents = array();
            /* @var $message \ServerGrove\LiveChatBundle\Document\Message */
            foreach ($messages as $message) {
                $contents[] = sprintf('%s: %s', $message->getSender()->getName(), $message->getContent());
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
     * @Route("/sglivechat/faq", name="sglc_chat_faq")
     * @Template
     * @return array
     */
    public function faqAction()
    {
        return array();
    }

    /**
     * @Route("/sglivechat", name="sglc_chat_homepage")
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction()
    {
        $visitor = $this->getVisitorByKey();

        if ($this->getOperator()) {
            throw new NotFoundHttpException('No chat found.');
        }

        $chatRequest = new ChatRequest();

        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->createForm(new ChatRequestType(), $chatRequest);
        $response = new Response();
        if ('POST' == $this->getRequest()->getMethod()) {
            $form->bindRequest($this->getRequest());
            $chatRequest = $form->getData();

            if ($form->isValid()) {
                $dm = $this->getDocumentManager();

                $visitor->setEmail($chatRequest->getEmail());
                $visitor->setName($chatRequest->getName());
                $dm->persist($visitor);

                $visit = $this->getVisitByKey($visitor);

                /* @var $chatSession ServerGrove\LiveChatBundle\Document\Session */
                $chatSession = Session::create($visit, $chatRequest->getQuestion(), Session::STATUS_WAITING);
                $dm->persist($chatSession);
                $dm->flush();

                $this->getSessionStorage()->set('chatsession', $chatSession->getId());
                $this->cacheUserForSession($visitor, $chatSession);

                return $this->redirect($this->generateUrl('sglc_chat_load', array('id' => $chatSession->getId())));
            } else {
                $response->setStatusCode(403);
            }
        }

        return $this->render('ServerGroveLiveChatBundle:Chat:index.html.twig', array(
            'visitor' => $visitor,
            'errorMsg' => $this->getSessionStorage()->getFlash('errorMsg', null),
            'form' => $form->createView()
        ), $response);
    }

    /**
     * @Route("/sglivechat/{sessId}/invite", name="sglc_chat_invite")
     */
    public function inviteAction($sessId)
    {
        $operator = $this->getOperator();

        if (!$operator) {
            $this->getSessionStorage()->setFlash('errorMsg', 'Unauthorized access.');

            return $this->redirect($this->generateUrl('sglc_chat_homepage'));
        }

        if (!($visit = $this->getDocumentManager()->getRepository('ServerGroveLiveChatBundle:Visit')->find($sessId))) {
            throw new NotFoundHttpException('Visit not found');
        }

        /* @var $chatSession ServerGrove\LiveChatBundle\Document\Session */
        $chatSession = Session::create($visit, null, Session::STATUS_INVITE);
        $chatSession->setOperator($operator);

        # @todo Add canned message as first chat message
        $this->getDocumentManager()->persist($chatSession);
        $this->getDocumentManager()->flush();

        $this->cacheUserForSession($operator, $chatSession);

        return $this->redirect($this->generateUrl('sglc_chat_load', array('id' => $chatSession->getId())));
    }

    /**
     * @Route("/sglivechat/{id}/load", name="sglc_chat_load")
     * @Template
     */
    public function loadAction($id)
    {
        $operator = $this->getOperator();
        if (!($chatSession = $this->getChatSession($id))) {
            $this->getSessionStorage()->setFlash('errorMsg', 'No chat found. Session may have expired. Please start again.');

            return $this->redirect($this->generateUrl('sglc_chat_homepage'));
        }

        $arrCannedMessages = array();
        if ($operator) {
            if (($cannedMessages = $this->getCannedMessages()) !== false) {
                /* @var $cannedMessage ServerGrove\LiveChatBundle\Document\CannedMessage */
                foreach ($cannedMessages as $cannedMessage) {
                    $arrCannedMessages[] = $cannedMessage->renderContent(array(
                        'operator' => $operator,
                        'currtime' => date('H:i:s'),
                        'currdate' => date('m-d-Y')
                    ));
                }
            }
        }

        $this->getSessionStorage()->set('chatStatus' . $chatSession->getId(), '');
        $this->getSessionStorage()->set('lastMessage', 0);
        $user = $this->getUserForSession($chatSession);

        return array(
            'chat' => $chatSession,
            'canned' => $arrCannedMessages,
            'user' => $user,
            'isOperator' => 'Operator' == $user->getKind()
        );
    }

    /**
     * @Route("/sglivechat/{id}/messages.{_format}", name="sglc_chat_messages", requirements={"_method"="get"}, defaults={"_format"="html"})
     * @Template
     * @param $_format
     * @return array
     */
    public function messagesAction($_format)
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            throw new NotFoundHttpException();
        }

        if (!$chatSession = $this->getChatSessionForCurrentUser()) {
            throw new NotFoundHttpException('Chat session not found');
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

            /* @var $message \ServerGrove\LiveChatBundle\Document\Message */
            foreach ($messages as $message) {
                $json['messages'][] = array(
                    'content' => $message->getContent(),
                    'name' => $message->getSender()->getName(),
                    'dt' => $message->getCreatedAt(),
                    'isOperator' => $message->getSender() instanceof Operator
                );
            }

            if ($this->theOtherMemberIsTyping($chatSession)) {
                $user = $this->getUserForSession($chatSession);
                $json['action'] = $chatSession->getOtherMember($user)->getName() . ' is typing';
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
                } catch (\Exception $e) {

                }
            }

            return new Response(json_encode($json), 200, array('content-type' => 'application/json'));
        }

        return array('messages' => $messages);
    }

    /**
     * @Route("/sglivechat/{id}/invite/reject", name="sglc_chat_invite_reject")
     */
    public function rejectInviteAction($id)
    {
        $response = new \Symfony\Component\HttpFoundation\Response();
        if (!($chatSession = $this->getChatSession($id))) {
            $this->getSessionStorage()->setFlash('errorMsg', 'No chat found. Session may have expired. Please start again.');

            return $response;
        }

        $chatSession->cancel();
        $this->getDocumentManager()->persist($chatSession);
        $this->getDocumentManager()->flush();

        return $response;
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
            return $this->redirect($this->generateUrl('sglc_chat_homepage'));
        }

        $user = $this->getUserForSession($chatSession);
        $chatSession->addChatMessage(utf8_encode(urldecode($this->getRequest()->get('msg'))), $user);
        $this->userIsNotTyping($user, $chatSession);

        $this->getDocumentManager()->persist($chatSession);
        $this->getDocumentManager()->flush();

        return new Response($this->getRequest()->get('msg'));
    }

    /**
     * @Route("/sglivechat/status", name="sglc_chat_status", requirements={"_method"="get"})
     */
    public function statusAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            //throw new NotFoundHttpException();
        }

        $response = new Response();

        if (!$chatSession = $this->getChatSessionForCurrentUser()) {
            $this->getSessionStorage()->setFlash('errorMsg', 'No chat found. Session may have expired. Please start again.');
            $response->headers->set('Content-type', 'text/javascript');
            $response->setContent(sprintf('location.href = %s;', var_export($this->generateUrl('sglc_chat_homepage'), true)));

            return $response;
        }

        if ($chatSession->getOperator()
                && $chatSession->getStatusId() == Session::STATUS_IN_PROGRESS
                && $this->getSessionStorage()->get('chatStatus' . $chatSession->getId()) != 'started') {

            $this->getSessionStorage()->set('chatStatus' . $chatSession->getId(), 'started');
            $response->headers->set('Content-type', 'text/javascript');
            $response->setContent('Chat.get().start();');

            return $response;
        }

        if ($chatSession->getStatusId() == Session::STATUS_CLOSED || $chatSession->getStatusId() == Session::STATUS_CANCELED) {
            $this->getSessionStorage()->setFlash('errorMsg', 'Chat has been ' . $chatSession->getStatus());
            $response->headers->set('Content-type', 'text/javascript');

            $response->setContent(sprintf('location.href = %s;', var_export($this->generateUrl('sglc_chat_homepage'), true)));

            return $response;
        }

        $this->getDocumentManager()->persist($chatSession);
        $this->getDocumentManager()->flush();

        $response->headers->set('Content-type', 'application/javascript; charset=utf-8');

        return $response;
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

        return new Response('');
    }

    /**
     * @param \ServerGrove\LiveChatBundle\Document\User $user
     * @param \ServerGrove\LiveChatBundle\Document\Session $chatSession
     */
    private function cacheUserForSession(User $user, Session $chatSession)
    {
        $this->getSessionStorage()->set('userId-' . $chatSession->getId(), $user->getId());
        $this->getSessionStorage()->set('userKind-' . $chatSession->getId(), $user->getKind());
    }

    /**
     * @return \ServerGrove\LiveChatBundle\Cache\Manager
     */
    private function getCacheManager()
    {
        return $this->container->get('livechat.cache.manager');
    }

    /**
     * @return \ServerGrove\LiveChatBundle\Document\CannedMessage[]
     */
    private function getCannedMessages()
    {
        return $this->getDocumentManager()->getRepository('ServerGroveLiveChatBundle:CannedMessage')->findAll();
    }

    /**
     * @param string $id        Session id
     * @param boolean $finished Whether or not the session has finished
     *
     * @return \ServerGrove\LiveChatBundle\Document\Session
     */
    private function getChatSession($id, $finished = true)
    {
        /* @var $repository \ServerGrove\LiveChatBundle\Document\SessionRepository */
        $repository = $this->getDocumentManager()->getRepository('ServerGroveLiveChatBundle:Session');
        if ($finished) {
            return $repository->getSessionIfNotFinished($id);
        }

        return $repository->find($id);
    }

    /**
     * @param boolean $finished Whether or not the session has finished
     *
     * @return \ServerGrove\LiveChatBundle\Document\Session
     */
    private function getChatSessionForCurrentUser($finished = true)
    {
        $request = $this->getRequest();
        $session = $this->getSessionStorage();
        $id = $session->has('_operator') ? $request->get('id') : $session->get('chatsession');

        return $this->getChatSession($id, $finished);
    }

    /**
     * @param \ServerGrove\LiveChatBundle\Document\Session $chatSession
     * @return \ServerGrove\LiveChatBundle\Document\User
     */
    private function getUserForSession(Session $chatSession)
    {
        if (!$this->getSessionStorage()->has('userId-' . $chatSession->getId()) || !$this->getSessionStorage()->has('userKind-' . $chatSession->getId())) {
            throw new \Exception('No user stored');
        }

        $userId = $this->getSessionStorage()->get('userId-' . $chatSession->getId());
        $userKind = $this->getSessionStorage()->get('userKind-' . $chatSession->getId());

        return $this->getDocumentManager()->find('ServerGroveLiveChatBundle:' . ($userKind == 'Client' ? 'Visitor' : 'Operator'), $userId);
    }

    /**
     * @param \ServerGrove\LiveChatBundle\Document\User $user
     * @param \ServerGrove\LiveChatBundle\Document\Session $chatSession
     */
    private function userIsTyping(User $user, Session $chatSession)
    {
        $this->getCacheManager()->set('chat.' . $chatSession->getId() . '.' . strtolower($user->getKind()) . '.typing', true, 3);
    }

    /**
     * @param \ServerGrove\LiveChatBundle\Document\User $user
     * @param \ServerGrove\LiveChatBundle\Document\Session $chatSession
     */
    private function userIsNotTyping(User $user, Session $chatSession)
    {
        $this->getCacheManager()->remove('chat.' . $chatSession->getId() . '.' . strtolower($user->getKind()) . '.typing');
    }

    /**
     * @param \ServerGrove\LiveChatBundle\Document\Session $chatSession
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