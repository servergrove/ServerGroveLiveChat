<?php

namespace Application\ChatBundle\Controller;

use Application\ChatBundle\Controller\BaseController;

/**
 * Chat's tracker controller
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class TrackController extends BaseController
{

    public function indexAction()
    {
        //$this->getResponse()->headers->set('Content-type', 'text/js');
        return $this->renderTemplate('ChatBundle:Track:index.twig.html');
    }

    public function updateAction()
    {
        $this->getResponse()->setContent(1);
        return $this->getResponse();
    }

    public function statusAction($_format)
    {
        $online = false;

        return $this->renderTemplate('ChatBundle:Track:status.twig.' . $_format, array('online' => $online));
    }
}
