<?php

namespace ServerGrove\LiveChatBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use ServerGrove\LiveChatBundle\Document\Session;

/**
 * Class ApiController
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 * @Route("/api")
 */
class ApiController extends Controller
{

    /**
     * @Route("/active-sessions.{_format}", name="sglc_admin_api_active_sessions", defaults={"_format"="json"})
     */
    public function activeSessionsAction()
    {
        /** @var $sessions \Doctrine\ODM\MongoDB\LoggableCursor */
        $sessions = $this->getDocumentManager()->getRepository('ServerGroveLiveChatBundle:Session')->getRequestedChatsArray();

        $router = $this->get('router');
        $operator = $this->get('security.context')->getToken()->getUser();

        $sessions = array_map(function($session) use($router, $operator)
        {
            if ($session['acceptable']) {
                $session['acceptUrl'] = $router->generate('sglc_admin_sessions_accept', array(
                    'id'       => $session['id'],
                    'operator' => $operator->getId()
                ), true);
            }

            if ($session['inProgress']) {
                $session['loadUrl'] = $router->generate('sglc_admin_sessions_load', array(
                    'id'       => $session['id'],
                    'operator' => $operator->getId()
                ), true);
                $session['closeUrl'] = $router->generate('sglc_admin_api_sessions_close', array('id' => $session['id']), true);
            }

            return $session;
        }, $sessions);

        return array(
            'result' => true,
            'rsp'    => $sessions
        );
    }

    /**
     * @Route("/{id}/close.{_format}", name="sglc_admin_api_sessions_close", defaults={"_format"="json"})
     * @Method("get")
     *
     * @param $id
     */
    public function closeClose($id)
    {
        $dm = $this->getDocumentManager();

        $document = $dm->getRepository('ServerGroveLiveChatBundle:Session')->find($id);

        if (!$document) {
            return array(
                'result' => false,
                'msg'    => 'Unable to find Session'
            );
        }

        $document->setStatusId(Session::STATUS_CLOSED);

        $dm->persist($document);
        $dm->flush();

        return array();
    }

    /**
     * @return \Doctrine\ODM\MongoDB\DocumentManager
     */
    private function getDocumentManager()
    {
        return $this->get('doctrine.odm.mongodb.default_document_manager');
    }
}
