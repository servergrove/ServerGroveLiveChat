<?php

namespace ServerGrove\LiveChatBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @return \Symfony\Bundle\FrameworkBundle\Controller\RedirectResponse
     */
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('sglc_chat_homepage'));
    }

    /**
     * @Route("/test.html", name="sglc_test")
     * @Template
     * @return array
     */
    public function testAction()
    {
        return array();
    }
}
