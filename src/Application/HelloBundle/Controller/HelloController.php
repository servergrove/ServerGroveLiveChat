<?php

namespace Application\HelloBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HelloController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('HelloBundle:Hello:index.twig.html', array('name' => $name));

        // render a PHP template instead
        // return $this->render('HelloBundle:Hello:index.php.html', array('name' => $name));
    }
}
