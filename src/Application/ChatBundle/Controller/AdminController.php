<?php

namespace Application\ChatBundle\Controller;

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
            'chats' => $this->getRequestedChats())
        );
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
            'chats' => $this->getRequestedChats())
        );
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
        return $this->renderTemplate('ChatBundle:Admin:operators.twig.html');
    }

    public function operatorDepartmentAction()
    {
        $this->checkLogin();
        return $this->renderTemplate('ChatBundle:Admin:operator-department.twig.html');
    }

    /**
     *
     */
    public function operatorAction()
    {
        if (!is_null($response = $this->checkLogin())) {
            return $response;
        }

        $operator = $this->getDocumentManager()->find('ChatBundle:Operator', $this->getHttpSession()->get('_operator'));
        $form = null;

        switch ($this->getRequest()->getMethod()) {
            case 'POST':
                break;
            case 'PUT':
                break;
            case 'DELETE':
                break;
        }

        $form = new Form('operator', $operator, $this->get('validator'));
        return $this->renderTemplate('ChatBundle:Admin:operator.twig.html');
    }

    protected function addOperator()
    {
        if (!is_null($response = $this->checkLogin())) {
            return $response;
        }
    }

    protected function editOperator()
    {
        if (!is_null($response = $this->checkLogin())) {
            return $response;
        }
    }

    protected function removeOperator()
    {
        if (!is_null($response = $this->checkLogin())) {
            return $response;
        }
    }

}