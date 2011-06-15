<?php

namespace ServerGrove\SGLiveChatBundle\Controller;

use ServerGrove\SGLiveChatBundle\Document\CannedMessage;
use ServerGrove\SGLiveChatBundle\Form\CannedMessageType;
use ServerGrove\SGLiveChatBundle\Form\OperatorType;
use ServerGrove\SGLiveChatBundle\Form\OperatorDepartmentType;
use ServerGrove\SGLiveChatBundle\Form\OperatorLoginType;
use ServerGrove\SGLiveChatBundle\Admin\OperatorLogin;
use ServerGrove\SGLiveChatBundle\Controller\BaseController;
use ServerGrove\SGLiveChatBundle\Document\Session as ChatSession;
use ServerGrove\SGLiveChatBundle\Document\Operator;
use ServerGrove\SGLiveChatBundle\Document\Operator\Department;
use Symfony\Component\Form\Exception\FormException;
use Doctrine\ODM\MongoDB\Mapping\Document;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\SecurityContext;
use Symfony\Component\Form\PasswordField;
use Symfony\Component\Form\TextField;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Description of AdminController
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class AdminController extends BaseController
{
    const DEFAULT_PAGE_ITEMS_LENGTH = 20;

    /**
     * @Route("/admin/sglivechat/canned-message", name="sglc_admin_canned_message")
     * @Route("/admin/sglivechat/canned-message/{id}", name="sglc_admin_canned_message_edit")
     */
    public function cannedMessageAction($id = null)
    {
        if (!is_null($response = $this->checkLogin())) {
            return $response;
        }

        if ($id) {
            $cannedMessage = $this->getDocumentManager()->find('SGLiveChatBundle:CannedMessage', $id);
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

                    return new RedirectResponse($this->generateUrl('sglc_admin_canned_messages'));
                }

                break;
            case 'DELETE':
                break;
        }

        return $this->renderTemplate('SGLiveChatBundle:Admin:canned-message.html.twig', array(
            'cannedMessage' => $cannedMessage,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/admin/sglivechat/canned-messages/{page}", name="sglc_admin_canned_messages", defaults={"page"="1"})
     */
    public function cannedMessagesAction($page)
    {
        return $this->simpleListAction($page, 'SGLiveChatBundle:CannedMessage', 'cannedMessages', 'canned-messages');
    }

    /**
     * @Route("/admin/sglivechat/console/chat-session/{id}", name="sglc_admin_chat_session")
     */
    public function chatSessionAction($id)
    {
        if (!is_null($response = $this->checkLogin())) {
            return $response;
        }

        $chatSession = $this->getDocumentManager()->find('SGLiveChatBundle:Session', $id);

        if (!$chatSession) {
            throw new NotFoundHttpException();
        }

        return $this->renderTemplate('SGLiveChatBundle:Admin:chat-session.html.twig', array('session' => $chatSession));
    }

    /**
     * @Route("/admin/sglivechat/console/chat-sessions/{page}", name="sglc_admin_chat_sessions", defaults={"page"="1"})
     */
    public function chatSessionsAction($page)
    {
        return $this->simpleListAction($page, 'SGLiveChatBundle:Session', 'sessions', 'chat-sessions');
    }

    /**
     * @Route("/admin/sglivechat/login/check", name="_security_check", requirements={"_method"="post"})
     *
     * @todo Search about security in Symfony2
     */
    public function checkLoginAction()
    {
        /* @var $form Form */
        $form = $this->createLoginForm();
        $form->bindRequest($this->getRequest());

        try {
            if (!$form->isValid()) {
                throw new FormException('Invalid data');
            }

            $operatorLogin = $form->getData();

            $email = $operatorLogin->getEmail();
            $passwd = $operatorLogin->getPasswd();

            /* @var $operator ServerGrove\SGLiveChatBundle\Document\Operator */
            $operator = $this->getDocumentManager()->getRepository('SGLiveChatBundle:Operator')->loadUserByUsername($email);

            if ($operator->getPasswd() != $operator->encodePassword($passwd, $operator->getSalt())) {
                throw new UsernameNotFoundException('Invalid password');
            }

            $this->getSessionStorage()->set('_operator', $operator->getId());
            $operator->setIsOnline(true);
            $this->getDocumentManager()->persist($operator);
            $this->getDocumentManager()->flush();
        } catch (UsernameNotFoundException $e) {
            $this->getSessionStorage()->setFlash('_error', $e->getMessage());

            return new RedirectResponse($this->generateUrl("_security_login", array('e' => __LINE__)));
        } catch (FormException $e) {
            $this->getSessionStorage()->setFlash('_error', $e->getMessage());

            return new RedirectResponse($this->generateUrl("_security_login", array('e' => __LINE__)));
        }

        return new RedirectResponse($this->generateUrl("sglc_admin_index"));
    }

    /**
     * @Route("/admin/sglivechat/console/close/{id}", name="sglc_admin_console_close")
     */
    public function closeChatAction($id)
    {
        if (($chat = $this->getChatSession($id)) !== false) {
            $chat->close();
            $this->getDocumentManager()->persist($chat);
            $this->getDocumentManager()->flush();
        }

        return new RedirectResponse($this->generateUrl('sglc_admin_console_sessions'));
    }

    /**
     * @Route("/admin/sglivechat/console/current-visits.{_format}", name="sglc_admin_console_current_visits", defaults={"_format"="html"})
     */
    public function currentVisitsAction($_format)
    {
        if (!is_null($response = $this->checkLogin())) {
            $this->getResponse()->setStatusCode(401);
            $this->getResponse()->setContent('');
            return $this->getResponse();
        }

        if ($_format == 'json') {
            $visits = $this->getDocumentManager()->getRepository('SGLiveChatBundle:Visit')->getLastVisitsArray();
            $this->getResponse()->setContent(json_encode($visits));

            return $this->getResponse();
        }

        throw new NotFoundHttpException('Not supported format');

        return $this->renderTemplate('SGLiveChatBundle:Admin:currentVisits.' . $_format . '.twig', array('visits' => $visits));
    }

    /**
     * @Route("/admin/sglivechat", name="sglc_admin_index")
     * 
     * @return RedirectResponse
     */
    public function indexAction()
    {
        if (!is_null($response = $this->checkLogin())) {
            return $response;
        }

        return new RedirectResponse($this->generateUrl('sglc_admin_console_sessions'));
    }

    /**
     * @Route("/admin/sglivechat/login", name="_security_login")
     */
    public function loginAction()
    {
        $errorMsg = $this->getSessionStorage()->getFlash('_error');
        if (!empty($errorMsg)) {
            $this->getResponse()->setStatusCode(401);
        }
        $form = $this->createLoginForm();

        return $this->renderTemplate('SGLiveChatBundle:Admin:login.html.twig', array(
            'form' => $form->createView(),
            'errorMsg' => $errorMsg
        ));
    }

    /**
     * @Route("/admin/sglivechat/logout", name="sglc_admin_logout")
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
        return new RedirectResponse($this->generateUrl("_security_login"));
    }

    /**
     * @Route("/admin/sglivechat/operator/department", name="sglc_admin_operator_department")
     * @Route("/admin/sglivechat/operator/department/{id}", name="sglc_admin_operator_department_edit")
     */
    public function operatorDepartmentAction($id = null)
    {
        if (!is_null($response = $this->checkLogin())) {
            return $response;
        }

        $message = null;

        if ($id) {
            $department = $this->getDocumentManager()->find('SGLiveChatBundle:Operator\Department', $id);
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

                    return new RedirectResponse($this->generateUrl('sglc_admin_operator_departments'));
                }

                break;
            case 'DELETE':
                break;
        }

        return $this->renderTemplate('SGLiveChatBundle:Admin:operator-department.html.twig', array(
            'department' => $department,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/admin/sglivechat/operator/departments/{page}", name="sglc_admin_operator_departments", defaults={"page"="1"})
     */
    public function operatorDepartmentsAction($page)
    {
        return $this->simpleListAction($page, 'SGLiveChatBundle:Operator\Department', 'departments', 'operator-departments');
    }

    /**
     * @Route("/admin/sglivechat/operator", name="sglc_admin_operator")
     * @Route("/admin/sglivechat/operator/{id}", name="sglc_admin_operator_edit")
     */
    public function operatorAction($id = null)
    {
        if (!is_null($response = $this->checkLogin())) {
            return $response;
        }

        $message = null;

        if ($id) {
            $operator = $this->getDocumentManager()->find('SGLiveChatBundle:Operator', $id);
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

                    return new RedirectResponse($this->generateUrl('sglc_admin_operators'));
                }

                break;
            case 'DELETE':
                break;
        }

        return $this->renderTemplate('SGLiveChatBundle:Admin:operator.html.twig', array(
            'operator' => $operator,
            'form' => $form->createView(),
            'edit' => $edit
        ));
    }

    /**
     * @Route("/admin/sglivechat/operators/{page}", name="sglc_admin_operators", defaults={"page"="1"})
     */
    public function operatorsAction($page)
    {
        return $this->simpleListAction($page, 'SGLiveChatBundle:Operator', 'operators');
    }

    /**
     * @Route("/admin/sglivechat/console/requested-chats.{_format}", name="sglc_admin_console_requested_chats", defaults={"_format"="html"})
     */
    public function requestedChatsAction($_format)
    {
        if (!is_null($response = $this->checkLogin())) {
            $this->getResponse()->setStatusCode(401);
            $this->getResponse()->setContent('');
            return $this->getResponse();
        }

        $this->getDocumentManager()->getRepository('SGLiveChatBundle:Session')->closeSessions();

        if ($_format == 'json') {
            $this->getResponse()->headers->set('Content-type', 'application/json');
            $this->getResponse()->setContent(json_encode($this->getRequestedChatsArray()));

            return $this->getResponse();
        }

        $chats = $this->getRequestedChats();

        return $this->renderTemplate('SGLiveChatBundle:Admin:requestedChats.' . $_format . '.twig', array('chats' => $chats));
    }

    /**
     * @Route("/admin/sglivechat/console/sessions", name="sglc_admin_console_sessions")
     */
    public function sessionsAction()
    {
        if (!is_null($response = $this->checkLogin())) {
            return $response;
        }

        $this->getDocumentManager()->getRepository('SGLiveChatBundle:Session')->closeSessions();

        return $this->renderTemplate('SGLiveChatBundle:Admin:requests.html.twig', array('chats' => $this->getRequestedChats()));
    }

    public function sessionsApiAction($_format)
    {
        return $this->renderTemplate('SGLiveChatBundle:Admin:Sessions.' . $_format . '.twig');
    }

    /**
     * @Route("/admin/sglivechat/console/sessions-service.json", name="sglc_admin_console_sessions_service")
     */
    public function sessionsServiceAction()
    {
        if (!is_null($response = $this->checkLogin())) {
            $this->getResponse()->setStatusCode(401);
            $this->getResponse()->setContent('');
            return $this->getResponse();
        }

        $this->getDocumentManager()->getRepository('SGLiveChatBundle:Session')->closeSessions();

        $this->getResponse()->headers->set('Content-type', 'application/json');

        $json = array();
        $json['requests'] = $this->getRequestedChatsArray();
        $json['count']['requests'] = count($json['requests']);
        $json['visits'] = $this->getDocumentManager()->getRepository('SGLiveChatBundle:Visit')->getLastVisitsArray();
        $json['count']['visits'] = count($json['visits']);
        $json['count']['online_operators'] = $this->getDocumentManager()->getRepository('SGLiveChatBundle:Operator')->getOnlineOperatorsCount();

        $this->getResponse()->setContent(json_encode($json));

        return $this->getResponse();
    }

    /**
     * @Route("/admin/sglivechat/visitor/{id}", name="sglc_admin_visitor")
     */
    public function visitorAction($id)
    {
        if (!is_null($response = $this->checkLogin())) {
            return $response;
        }

        $visitor = $this->getDocumentManager()->find('SGLiveChatBundle:Visitor', $id);

        if (!$visitor) {
            throw new NotFoundHttpException();
        }

        return $this->renderTemplate('SGLiveChatBundle:Admin:visitor.html.twig', array('visitor' => $visitor, 'visits' => $this->getDocumentManager()->getRepository('SGLiveChatBundle:Visit')->toArray($visitor->getVisits())));
    }

    /**
     * @Route("/admin/sglivechat/visitors/{page}", name="sglc_admin_visitors", defaults={"page"="1"})
     */
    public function visitorsAction($page)
    {
        return $this->simpleListAction($page, 'SGLiveChatBundle:Visitor', 'visitors');
    }

    /**
     * @Route("/admin/sglivechat/visits/{page}", name="sglc_admin_visits", defaults={"page"="1"})
     */
    public function visitsAction($page)
    {
        return $this->simpleListAction($page, 'SGLiveChatBundle:Visit', 'visits');
    }

    private function simpleListAction($page, $documentName, $documentTemplateKey, $template = null)
    {
        if (!is_null($response = $this->checkLogin())) {
            return $response;
        }

        if (is_null($template)) {
            $template = $documentTemplateKey;
        }

        $template = 'SGLiveChatBundle:Admin:' . $template . '.html.twig';

        $length = self::DEFAULT_PAGE_ITEMS_LENGTH;
        $offset = ($page - 1) * $length;

        $pages = ceil($this->getDocumentManager()->getRepository($documentName)->findAll()->count() / $length);

        $documents = $this->getDocumentManager()->getRepository($documentName)->findSlice($offset, $length);

        $msg = $this->getSessionStorage()->getFlash('msg', '');
        return $this->renderTemplate($template, array(
            $documentTemplateKey => $documents,
            'msg' => $msg,
            'page' => $page,
            'pages' => $pages
        ));
    }

    /**
     * @return Symfony\Component\HttpFoundation\Response
     */
    private function checkLogin()
    {
        if (!$this->isLogged()) {
            return $this->forward('SGLiveChatBundle:Admin:login');
        }

        $operator = $this->getOperator();
        if (!$operator) {
            return $this->forward('SGLiveChatBundle:Admin:logout');
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
     * @return ServerGrove\SGLiveChatBundle\Document\Session
     */
    private function getChatSession($id)
    {
        return $this->getDocumentManager()->getRepository('SGLiveChatBundle:Session')->find($id);
    }

    /**
     * @return Doctrine\ODM\MongoDB\LoggableCursor
     */
    private function getRequestedChats()
    {
        return $this->getDocumentManager()->getRepository('SGLiveChatBundle:Session')->getRequestedChats();
    }

    /**
     * @return array
     */
    private function getRequestedChatsArray()
    {
        return $this->getDocumentManager()->getRepository('SGLiveChatBundle:Session')->getRequestedChatsArray();
    }

    /**
     * @return boolean
     */
    private function isLogged()
    {
        return $this->getSessionStorage()->get('_operator');
    }

}