<?php

namespace ServerGrove\SGLiveChatBundle\Controller;

use ServerGrove\SGLiveChatBundle\Admin\OperatorLogin;
use ServerGrove\SGLiveChatBundle\Controller\BaseController;
use ServerGrove\SGLiveChatBundle\Document\Session as ChatSession;
use ServerGrove\SGLiveChatBundle\Document\Operator;
use ServerGrove\SGLiveChatBundle\Document\Operator\Department;
use ServerGrove\SGLiveChatBundle\Form\OperatorDepartmentForm;
use ServerGrove\SGLiveChatBundle\Form\OperatorForm;
use ServerGrove\SGLiveChatBundle\Form\OperatorLoginForm;

use Symfony\Component\Form\Exception\FormException;
use Doctrine\ODM\MongoDB\Mapping\Document;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\SecurityContext;
use Symfony\Component\Form\PasswordField;
use Symfony\Component\Form\TextField;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Description of AdminController
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class AdminController extends BaseController
{

    /**
     * @param ServerGrove\SGLiveChatBundle\Admin\OperatorLogin $login
     * @return Symfony\Component\Form\Form
     */
    private function createLoginForm(OperatorLogin $login)
    {
        return OperatorLoginForm::create($this->get('form.context'), 'login');
    }

    private function isLogged()
    {
        return $this->getSessionStorage()->get('_operator');
    }

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
     * @todo Search about security in Symfony2
     */
    public function checkLoginAction()
    {
        $operatorLogin = new OperatorLogin();
        /* @var $form Form */
        $form = $this->createLoginForm($operatorLogin);
        $form->bind($this->getRequest(), $operatorLogin);

        try {
            if (!$form->isValid()) {
                throw new FormException('Invalid data');
            }

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

            return $this->redirect($this->generateUrl("_security_login", array('e' => __LINE__)));
        } catch (FormException $e) {
            $this->getSessionStorage()->setFlash('_error', $e->getMessage());

            return $this->redirect($this->generateUrl("_security_login", array('e' => __LINE__)));
        }

        return $this->redirect($this->generateUrl("sglc_admin_index"));
    }

    public function indexAction()
    {
        if (!is_null($response = $this->checkLogin())) {
            return $response;
        }

        return $this->redirect($this->generateUrl('sglc_admin_console_sessions'));
    }

    public function loginAction()
    {
        $errorMsg = $this->getSessionStorage()->getFlash('_error');
        if (!empty($errorMsg)) {
            $this->getResponse()->setStatusCode(401);
        }
        $form = $this->createLoginForm(new OperatorLogin());

        return $this->renderTemplate('SGLiveChatBundle:Admin:login.html.twig', array(
            'form' => $form,
            'errorMsg' => $errorMsg)
        );
    }

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

    private function getRequestedChats()
    {
        return $this->getDocumentManager()->getRepository('SGLiveChatBundle:Session')->getRequestedChats();
    }

    private function getRequestedChatsArray()
    {
        return $this->getDocumentManager()->getRepository('SGLiveChatBundle:Session')->getRequestedChatsArray();
    }

    public function sessionsAction()
    {
        if (!is_null($response = $this->checkLogin())) {
            return $response;
        }

        $this->getDocumentManager()->getRepository('SGLiveChatBundle:Session')->closeSessions();

        return $this->renderTemplate('SGLiveChatBundle:Admin:requests.html.twig', array(
            'chats' => $this->getRequestedChats()));
    }

    public function sessionsApiAction($_format)
    {
        return $this->renderTemplate('SGLiveChatBundle:Admin:Sessions.' . $_format . '.twig');
    }

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

        return $this->renderTemplate('SGLiveChatBundle:Admin:requestedChats.' . $_format . '.twig', array(
            'chats' => $chats));
    }

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

        return $this->renderTemplate('SGLiveChatBundle:Admin:currentVisits.' . $_format . '.twig', array(
            'visits' => $visits));
    }

    /**
     * @return ServerGrove\SGLiveChatBundle\Document\Session
     */
    private function getChatSession($id)
    {
        return $this->getDocumentManager()->getRepository('SGLiveChatBundle:Session')->find($id);
    }

    public function closeChatAction($id)
    {
        if (($chat = $this->getChatSession($id)) !== false) {
            $chat->close();
            $this->getDocumentManager()->persist($chat);
            $this->getDocumentManager()->flush();
        }

        return $this->redirect($this->generateUrl('sglc_admin_console_sessions'));
    }

    public function operatorsAction()
    {
        if (!is_null($response = $this->checkLogin())) {
            return $response;
        }

        $operators = $this->getDocumentManager()->getRepository('SGLiveChatBundle:Operator')->findAll();
        $msg = $this->getSessionStorage()->getFlash('msg', '');
        return $this->renderTemplate('SGLiveChatBundle:Admin:operators.html.twig', array(
            'operators' => $operators,
            'msg' => $msg));
    }

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

        $form = new OperatorDepartmentForm('department', $department, $this->get('validator'));

        switch ($this->getRequest()->getMethod()) {
            case 'POST':
            case 'PUT':
                $params = $this->getRequest()->request->get($form->getName());
                if (!empty($params['name'])) {
                    $department->setName($params['name']);
                    $department->setIsActive(isset($params['isActive']) && $params['isActive']);
                    $this->getDocumentManager()->persist($department);
                    $this->getDocumentManager()->flush();
                    $this->getSessionStorage()->setFlash('msg', 'The department has been successfully updated');

                    return $this->redirect($this->generateUrl('sglc_admin_operator_departments'));
                }
                //}
                break;
            case 'DELETE':
                break;
        }

        return $this->renderTemplate('SGLiveChatBundle:Admin:operator-department.html.twig', array(
            'department' => $department,
            'form' => $form));
    }

    public function operatorDepartmentsAction()
    {
        $this->checkLogin();

        $departments = $this->getDocumentManager()->getRepository('SGLiveChatBundle:Operator\Department')->findAll();
        $msg = $this->getSessionStorage()->getFlash('msg', '');

        return $this->renderTemplate('SGLiveChatBundle:Admin:operator-departments.html.twig', array(
            'departments' => $departments,
            'msg' => $msg));
    }

    /**
     *
     */
    public function operatorAction($id = null)
    {
        if (!is_null($response = $this->checkLogin())) {
            return $response;
        }

        $message = null;

        if ($id) {
            $operator = $this->getDocumentManager()->find('SGLiveChatBundle:Operator', $id);
        } else {
            $operator = new Operator();
        }

        $form = new OperatorForm('operator', $operator, $this->get('validator'));

        switch ($this->getRequest()->getMethod()) {
            case 'POST':
            case 'PUT':
                $params = $this->getRequest()->request->get($form->getName());
                if (!empty($params['name']) && !empty($params['email']['first']) && !empty($params['passwd']['first'])) {
                    $operator->setName($params['name']);
                    $operator->setEmail($params['email']['first']);
                    $operator->setPasswd($params['passwd']['first']);
                    $operator->setIsActive(isset($params['isActive']) && $params['isActive']);
                    $this->getDocumentManager()->persist($operator);
                    $this->getDocumentManager()->flush();
                    $this->getSessionStorage()->setFlash('msg', 'The operator has been successfully updated');

                    return $this->redirect($this->generateUrl('sglc_admin_operators'));
                }
                //}
                break;
            case 'DELETE':
                break;
        }

        return $this->renderTemplate('SGLiveChatBundle:Admin:operator.html.twig', array(
            'operator' => $operator,
            'form' => $form));
    }

}