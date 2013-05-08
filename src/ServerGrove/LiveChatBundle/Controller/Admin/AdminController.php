<?php

namespace ServerGrove\LiveChatBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Session\Session;
use ServerGrove\LiveChatBundle\Form\OperatorLoginType;

/**
 * Class AdminController
 *
 * @author Ismael Ambrosi <ismael@servergrove.com>
 */
class AdminController extends Controller
{
    /**
     * @Route("/login", name="_security_login")
     * @Method("get")
     * @Template
     *
     * @return array
     */
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = new Session();
        $session->start();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $session->getFlashBag()->add('error', $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR));
        } else {
            //$session->getFlashBag()->add('error', $session->get(SecurityContext::AUTHENTICATION_ERROR));
        }

        return array(
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'form'          => $this->createLoginForm()->createView()
        );
    }

    /**
     * @Route("/login-check", name="_security_check")
     * @Method("post")
     * @Template
     * @return array
     */
    public final function loginCheckAction()
    {
        return array();
    }

    /**
     * @Route("/logout", name="sglc_admin_logout")
     * @return array
     */
    public final function logoutAction()
    {
        return array();
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    private function createLoginForm()
    {
        return $this->get('form.factory')->create(new OperatorLoginType());
    }

}
