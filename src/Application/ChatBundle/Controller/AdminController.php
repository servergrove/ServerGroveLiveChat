<?php

namespace Application\ChatBundle\Controller;

use Application\ChatBundle\Document\Operator\Department;

use Doctrine\ODM\MongoDB\Mapping\Document;

use Application\ChatBundle\Form\OperatorDepartmentForm;
use Application\ChatBundle\Form\OperatorForm;
use Symfony\Component\Security\Exception\UsernameNotFoundException;
use Symfony\Component\Security\SecurityContext;
use Symfony\Component\Form\PasswordField;
use Symfony\Component\Form\TextField;
use Application\ChatBundle\Document\Operator;
use Symfony\Component\Form\Form;
use Application\ChatBundle\Controller\BaseController;

/**
 * Description of AdminController
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class AdminController extends BaseController
{

    private function createLoginForm($operator = null)
    {
        $form = new Form('login', $operator, $this->get('validator'));
        $form->add(new TextField('email'));
        $form->add(new PasswordField('passwd'));

        return $form;
    }

    private function isLogged()
    {
        return $this->getHttpSession()->get('_operator');
    }

    private function checkLogin()
    {
        if (!$this->isLogged()) {
            return $this->forward('ChatBundle:Admin:login');
        }

        return null;
    }

    /**
     * @todo Search about security in Symfony2
     */
    public function checkLoginAction()
    {
        $form = $this->createLoginForm(new Operator());
        $form->bind($this->get('request')->request->get('login'));

        if (!$form->isValid()) {
            return $this->redirect($this->generateUrl("_security_login", array(
                'e' => __LINE__)));
        }
        try {
            /* @var $operator Application\ChatBundle\Document\Operator */
            $operator = $this->getDocumentManager()->getRepository('ChatBundle:Operator')->loadUserByUsername($form->get('email')->getDisplayedData());
            if (!$operator->encodePassword($form->get('passwd')->getDisplayedData(), $operator->getSalt())) {
                throw new UsernameNotFoundException('Invalid password');
            }

            $this->getHttpSession()->set('_operator', $operator->getId());
        } catch (UsernameNotFoundException $e) {
            $this->getHttpSession()->setFlash('_error', $e->getMessage());
            return $this->redirect($this->generateUrl("_security_login", array(
                'e' => __LINE__)));
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
        $errorMsg = $this->getHttpSession()->getFlash('_error');
        $form = $this->createLoginForm();

        return $this->renderTemplate('ChatBundle:Admin:login.twig.html', array(
            'form' => $form,
            'errorMsg' => $errorMsg));
    }

    public function logoutAction()
    {
        if (!is_null($response = $this->checkLogin())) {
            return $response;
        }
        return $this->redirect($this->generateUrl("sglc_admin_login"));
    }

    private function getRequestedChats()
    {
        return $this->getDocumentManager()->getRepository('ChatBundle:Session')->getRequestedChats();
    }

    public function sessionsAction()
    {
        if (!is_null($response = $this->checkLogin())) {
            return $response;
        }

        $this->getDocumentManager()->getRepository('ChatBundle:Session')->closeSessions();

        return $this->renderTemplate('ChatBundle:Admin:requests.twig.html', array(
            'chats' => $this->getRequestedChats()));
    }

    public function requestedChatsAction()
    {
        if (!$this->getRequest()->isXmlHttpRequest()) {
            return $this->redirect($this->generateUrl('sglc_admin_console_sessions'));
        }

        if (!is_null($response = $this->checkLogin())) {
            $this->getResponse()->setStatusCode(401);
            $this->getResponse()->setContent('');
            return $this->getResponse();
        }

        $this->getDocumentManager()->getRepository('ChatBundle:Session')->closeSessions();

        return $this->renderTemplate('ChatBundle:Admin:requestedChats.twig.html', array(
            'chats' => $this->getRequestedChats()));
    }

    /**
     * @return Application\ChatBundle\Document\Session
     */
    private function getChatSession($id)
    {
        return $this->getDocumentManager()->getRepository('ChatBundle:Session')->find($id);
    }

    public function closeChatAction($id)
    {
        if ($chat = $this->getChatSession($id)) {
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

        $operators = $this->getDocumentManager()->getRepository('ChatBundle:Operator')->findAll();
        $msg = $this->getHttpSession()->getFlash('msg', '');
        return $this->renderTemplate('ChatBundle:Admin:operators.twig.html', array(
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
            $department = $this->getDocumentManager()->find('ChatBundle:Operator\Department', $id);
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
                    $this->getHttpSession()->setFlash('msg', 'The department has been successfully updated');

                    return $this->redirect($this->generateUrl('sglc_admin_operator_departments'));
                }
                //}
                break;
            case 'DELETE':
                break;
        }

        return $this->renderTemplate('ChatBundle:Admin:operator-department.twig.html', array(
            'department' => $department,
            'form' => $form));
    }

    public function operatorDepartmentsAction()
    {
        $this->checkLogin();

        $departments = $this->getDocumentManager()->getRepository('ChatBundle:Operator\Department')->findAll();
        $msg = $this->getHttpSession()->getFlash('msg', '');

        return $this->renderTemplate('ChatBundle:Admin:operator-departments.twig.html', array(
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
            $operator = $this->getDocumentManager()->find('ChatBundle:Operator', $id);
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
                    $this->getHttpSession()->setFlash('msg', 'The operator has been successfully updated');

                    return $this->redirect($this->generateUrl('sglc_admin_operators'));
                }
                //}
                break;
            case 'DELETE':
                break;
        }

        return $this->renderTemplate('ChatBundle:Admin:operator.twig.html', array(
            'operator' => $operator,
            'form' => $form));
    }

}