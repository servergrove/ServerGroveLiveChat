<?php

namespace ServerGrove\LiveChatBundle\Controller;

use ServerGrove\LiveChatBundle\Document\CannedMessage;
use ServerGrove\LiveChatBundle\Form\CannedMessageType;
use ServerGrove\LiveChatBundle\Form\OperatorType;
use ServerGrove\LiveChatBundle\Form\OperatorDepartmentType;
use ServerGrove\LiveChatBundle\Form\OperatorLoginType;
use ServerGrove\LiveChatBundle\Admin\OperatorLogin;
use ServerGrove\LiveChatBundle\Controller\BaseController;
use ServerGrove\LiveChatBundle\Document\Session as ChatSession;
use ServerGrove\LiveChatBundle\Document\Operator;
use ServerGrove\LiveChatBundle\Document\Operator\Department;
use Symfony\Component\Form\Exception\FormException;
use Doctrine\ODM\MongoDB\Mapping\Document;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Form\PasswordField;
use Symfony\Component\Form\TextField;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of AdminController
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class AdminController extends BaseController
{
    const DEFAULT_PAGE_ITEMS_LENGTH = 20;

    /**
     * @Route("/canned-message", name="sglc_admin_canned_message")
     * @Route("/canned-message/{id}", name="sglc_admin_canned_message_edit")
     * @Template
     */
    public function cannedMessageAction($id = null)
    {
        if (!is_null($response = $this->checkLogin())) {
            return $response;
        }

        if ($id) {
            $cannedMessage = $this->getCannedMessageRepository()->find($id);
            if (!$cannedMessage) {
                throw new NotFoundHttpException('Non existent canned-message');
            }
        } else {
            $cannedMessage = new CannedMessage();
        }

        /* @var $form Symfony\Component\Form\Form */
        $form = $this->get('form.factory')->create(new CannedMessageType());
        $form->setData($cannedMessage);

        switch ($this->getRequest()->getMethod()) {
            case 'POST':
            case 'PUT':
                $form->bindRequest($this->getRequest());
                if ($form->isValid()) {
                    $this->getDocumentManager()->persist($cannedMessage);
                    $this->getDocumentManager()->flush();
                    $this->getSessionStorage()->setFlash('msg', 'The canned message has been successfully updated');

                    return $this->redirect($this->generateUrl('sglc_admin_canned_messages'));
                }

                break;
            case 'DELETE':
                break;
        }

        return array(
            'cannedMessage' => $cannedMessage,
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/canned-messages/{page}", name="sglc_admin_canned_messages", defaults={"page"="1"})
     * @Template
     */
    public function cannedMessagesAction($page)
    {
        return $this->simpleListAction($page, 'ServerGroveLiveChatBundle:CannedMessage', 'cannedMessages', 'canned-messages');
    }

    /**
     * @Route("/console/chat-session/{id}", name="sglc_admin_chat_session")
     * @Template
     */
    public function chatSessionAction($id)
    {
        if (!is_null($response = $this->checkLogin())) {
            return $response;
        }

        $chatSession = $this->getDocumentManager()->find('ServerGroveLiveChatBundle:Session', $id);

        if (!$chatSession) {
            throw new NotFoundHttpException();
        }

        return array('session' => $chatSession);
    }

    /**
     * @Route("/console/chat-sessions/{page}", name="sglc_admin_chat_sessions", defaults={"page"="1"})
     * @Template
     */
    public function chatSessionsAction($page)
    {
        return $this->simpleListAction($page, 'ServerGroveLiveChatBundle:Session', 'sessions', 'chat-sessions');
    }

    /**
     * @Route("/console/close/{id}", name="sglc_admin_console_close")
     */
    public function closeChatAction($id)
    {
        if (($chat = $this->getChatSession($id)) !== false) {
            $chat->close();
            $this->getDocumentManager()->persist($chat);
            $this->getDocumentManager()->flush();
        }

        return $this->redirect($this->generateUrl('sglc_admin_console_sessions'));
    }

    /**
     * @Route("/console/current-visits.{_format}", name="sglc_admin_console_current_visits", defaults={"_format"="html"})
     * @Template
     */
    public function currentVisitsAction($_format)
    {
        if (!is_null($response = $this->checkLogin())) {
            $response->setStatusCode(401);
            $response->setContent('');
            return $response;
        }

        if ($_format == 'json') {
            $visits = $this->getVisitRepository()->getLastVisitsArray();

            return new \Symfony\Component\HttpFoundation\Response(json_encode($visits));
        }

        throw new NotFoundHttpException('Not supported format');

        return array('visits' => $visits);
    }

    /**
     * @Route("/", name="sglc_admin_index")
     *
     * @return RedirectResponse
     */
    public function indexAction()
    {
        if (!is_null($response = $this->checkLogin())) {
            return $response;
        }

        return $this->redirect($this->generateUrl('sglc_admin_console_sessions'));
    }

    /**
     * @Route("/login", name="_security_login", requirements={"_method"="get"})
     * @Route("/login/check", name="_security_check", requirements={"_method"="post"})
     * @Template
     */
    public function loginAction()
    {
        $response = new Response();
        $errorMsg = null;
        if (!empty($errorMsg)) {
            $response->setStatusCode(401);
        }
        $form = $this->createLoginForm();

        if ('POST' == $this->getRequest()->getMethod()) {
            $form->bindRequest($this->getRequest());

            try {
                if ($form->isValid()) {
                    $operatorLogin = $form->getData();

                    $email = $operatorLogin->getEmail();
                    $passwd = $operatorLogin->getPasswd();

                    /* @var $operator ServerGrove\LiveChatBundle\Document\Operator */
                    $operator = $this->getOperatorRepository()->loadUserByUsername($email);

                    if ($operator->getPasswd() != $operator->encodePassword($passwd, $operator->getSalt())) {
                        throw new UsernameNotFoundException('Invalid password');
                    }

                    $this->getSessionStorage()->set('_operator', $operator->getId());
                    $operator->setIsOnline(true);
                    $this->getDocumentManager()->persist($operator);
                    $this->getDocumentManager()->flush();

                    return $this->redirect($this->generateUrl("sglc_admin_index"));
                }
            } catch (UsernameNotFoundException $e) {

                $response->setStatusCode(401);
                $errorMsg = $e->getMessage();
            }
        }

        return $this->render('ServerGroveLiveChatBundle:Admin:login.html.twig', array(
                                                                                     'form' => $form->createView(),
                                                                                     'errorMsg' => $errorMsg
                                                                                ), $response);
    }

    /**
     * @Route("/logout", name="sglc_admin_logout")
     */
    public function logoutAction()
    {
        if ($this->isLogged()) {
            $operator = $this->getOperator();
            if ($operator) {
                $operator->setIsOnline(false);
                $this->getDocumentManager()->persist($operator);
                $this->getDocumentManager()->flush();
            }
        }

        $this->getSessionStorage()->remove('_operator');

        if (!is_null($response = $this->checkLogin())) {
            return $response;
        }
        return $this->redirect($this->generateUrl("_security_login"));
    }

    /**
     * @Route("/operator/department", name="sglc_admin_operator_department")
     * @Route("/operator/department/{id}", name="sglc_admin_operator_department_edit")
     * @Template
     */
    public function operatorDepartmentAction($id = null)
    {
        if (!is_null($response = $this->checkLogin())) {
            return $response;
        }

        $message = null;

        if ($id) {
            $department = $this->getDocumentManager()->find('ServerGroveLiveChatBundle:Operator\Department', $id);
        } else {
            $department = new Department();
        }

        $form = $this->get('form.factory')->create(new OperatorDepartmentType());
        $form->setData($department);

        switch ($this->getRequest()->getMethod()) {
            case 'POST':
            case 'PUT':
                $form->bindRequest($this->getRequest());
                if ($form->isValid()) {
                    $this->getDocumentManager()->persist($department);
                    $this->getDocumentManager()->flush();
                    $this->getSessionStorage()->setFlash('msg', 'The department has been successfully updated');

                    return $this->redirect($this->generateUrl('sglc_admin_operator_departments'));
                }

                break;
            case 'DELETE':
                break;
        }

        return array(
            'department' => $department,
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/operator/departments/{page}", name="sglc_admin_operator_departments", defaults={"page"="1"})
     * @Template
     */
    public function operatorDepartmentsAction($page)
    {
        return $this->simpleListAction($page, 'ServerGroveLiveChatBundle:Operator\Department', 'departments', 'operator-departments');
    }

    /**
     * @Route("/operator", name="sglc_admin_operator")
     * @Route("/operator/{id}", name="sglc_admin_operator_edit")
     * @Template
     */
    public function operatorAction($id = null)
    {
        if (!is_null($response = $this->checkLogin())) {
            return $response;
        }

        $message = null;

        if ($id) {
            $operator = $this->getDocumentManager()->find('ServerGroveLiveChatBundle:Operator', $id);
            $edit = true;
        } else {
            $operator = new Operator();
            $edit = false;
        }

        /* @var $form Symfony\Component\Form\Form */
        $form = $this->get('form.factory')->create(new OperatorType($this->getDocumentManager(), $edit));
        $form->setData($operator);

        switch ($this->getRequest()->getMethod()) {
            case 'POST':
            case 'PUT':

                $form->bindRequest($this->getRequest());

                if ($form->isValid()) {
                    $this->getDocumentManager()->persist($operator);
                    $this->getDocumentManager()->flush();
                    $this->getSessionStorage()->setFlash('msg', 'The operator has been successfully updated');

                    return $this->redirect($this->generateUrl('sglc_admin_operators'));
                }

                break;
            case 'DELETE':
                break;
        }

        return array(
            'operator' => $operator,
            'form' => $form->createView(),
            'edit' => $edit
        );
    }

    /**
     * @Route("/operators/{page}", name="sglc_admin_operators", defaults={"page"="1"})
     * @Template
     */
    public function operatorsAction($page)
    {
        return $this->simpleListAction($page, 'ServerGroveLiveChatBundle:Operator', 'operators');
    }

    /**
     * @Route("/console/requested-chats.{_format}", name="sglc_admin_console_requested_chats", defaults={"_format"="html"})
     * @Template
     */
    public function requestedChatsAction($_format)
    {
        if (!is_null($response = $this->checkLogin())) {
            $response->setStatusCode(401);
            $response->setContent('');
            return $response;
        }

        $response = new Response();

        $this->getSessionRepository()->closeSessions();

        if ($_format == 'json') {
            $response->headers->set('Content-type', 'application/json');
            $response->setContent(json_encode($this->getRequestedChatsArray()));

            return $response;
        }

        $chats = $this->getRequestedChats();

        return $this->render('ServerGroveLiveChatBundle:Admin:requestedChats.' . $_format . '.twig', array('chats' => $chats), $response);
    }

    /**
     * @Route("/console/sessions", name="sglc_admin_console_sessions")
     * @Template
     */
    public function requestsAction()
    {
        if (!is_null($response = $this->checkLogin())) {
            return $response;
        }

        $this->getSessionRepository()->closeSessions();

        return array('chats' => $this->getRequestedChats());
    }

    /**
     * @Template
     * @return array
     */
    public function sessionsApiAction()
    {
        return array();
    }

    /**
     * @Route("/console/sessions-service.json", name="sglc_admin_console_sessions_service")
     */
    public function sessionsServiceAction()
    {
        if (!is_null($response = $this->checkLogin())) {
            return $response;
        }

        $this->getSessionRepository()->closeSessions();

        $json = array();
        $json['requests'] = $this->getRequestedChatsArray();
        $json['count']['requests'] = count($json['requests']);
        $json['visits'] = $this->getVisitRepository()->getLastVisitsArray();
        $json['count']['visits'] = count($json['visits']);
        $json['count']['online_operators'] = $this->getOperatorRepository()->getOnlineOperatorsCount();

        return new Response(json_encode($json), 200, array('Content-type' => 'application/json'));
    }

    /**
     * @Route("/visitor/{id}", name="sglc_admin_visitor")
     * @Template
     */
    public function visitorAction($id)
    {
        if (!is_null($response = $this->checkLogin())) {
            return $response;
        }

        $visitor = $this->getVisitorRepository()->find($id);

        if (!$visitor) {
            throw new NotFoundHttpException();
        }

        return array(
            'visitor' => $visitor,
            'visits' => $this->getVisitRepository()->toArray($visitor->getVisits()
            ));
    }

    /**
     * @Route("/visitors/{page}", name="sglc_admin_visitors", defaults={"page"="1"})
     * @Template
     */
    public function visitorsAction($page)
    {
        return $this->simpleListAction($page, 'ServerGroveLiveChatBundle:Visitor', 'visitors');
    }

    /**
     * @Route("/visits/{page}", name="sglc_admin_visits", defaults={"page"="1"})
     * @Template
     */
    public function visitsAction($page)
    {
        return $this->simpleListAction($page, 'ServerGroveLiveChatBundle:Visit', 'visits');
    }

    /**
     * @Template
     * @return array
     */
    public function adminMessagesAction()
    {
        $messages = array();

        $success = $this->getSessionStorage()->getFlash('msg', '');
        if (!empty($success)) {
            $messages[] = array(
                'type'    => 'success',
                'title'   => 'Success!',
                'message' => $success
            );
        }

        return array('messages' => $messages);
    }

    private function simpleListAction($page, $documentName, $documentTemplateKey)
    {
        if (!is_null($response = $this->checkLogin())) {
            return $response;
        }

        $length = self::DEFAULT_PAGE_ITEMS_LENGTH;
        $offset = ($page - 1) * $length;

        $pages = ceil($this->getDocumentManager()->getRepository($documentName)->findAll()->count() / $length);

        $documents = $this->getDocumentManager()->getRepository($documentName)->findSlice($offset, $length);

        return array(
            $documentTemplateKey => $documents,
            'page'               => $page,
            'pages'              => $pages
        );
    }

    /**
     * @return Symfony\Component\HttpFoundation\Response
     */
    private function checkLogin()
    {
        if (!$this->isLogged()) {
            return $this->forward('ServerGroveLiveChatBundle:Admin:login');
        }

        $operator = $this->getOperator();
        if (!$operator) {
            return $this->forward('ServerGroveLiveChatBundle:Admin:logout');
        }
        $operator->setIsOnline(true);
        $this->getDocumentManager()->persist($operator);
        $this->getDocumentManager()->flush();

        return null;
    }

    /**
     * @return Symfony\Component\Form\Form
     */
    private function createLoginForm()
    {
        return $this->get('form.factory')->create(new OperatorLoginType());
    }

    /**
     * @return ServerGrove\LiveChatBundle\Document\Session
     */
    private function getChatSession($id)
    {
        return $this->getSessionRepository()->find($id);
    }

    /**
     * @return Doctrine\ODM\MongoDB\LoggableCursor
     */
    private function getRequestedChats()
    {
        return $this->getSessionRepository()->getRequestedChats();
    }

    /**
     * @return array
     */
    private function getRequestedChatsArray()
    {
        return $this->getSessionRepository()->getRequestedChatsArray();
    }

    /**
     * @return boolean
     */
    private function isLogged()
    {
        return $this->getSessionStorage()->get('_operator');
    }

}