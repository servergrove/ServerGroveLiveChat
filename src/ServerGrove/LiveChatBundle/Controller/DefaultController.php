<?php

namespace ServerGrove\LiveChatBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ServerGroveLiveChatBundle:Default:index.html.twig');
    }
}
