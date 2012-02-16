<?php

namespace ServerGrove\LiveChatBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ServerGrove\LiveChatBundle\Document\Session;

/**
 * Session controller.
 *
 * @Route("/sessions")
 */
class SessionController extends Controller
{
    /**
     * Lists all Session documents.
     *
     * @Route("/", name="sglc_admin_sessions")
     * @Template()
     */
    public function indexAction()
    {
        $dm = $this->getDocumentManager();

        $documents = $dm->getRepository('ServerGroveLiveChatBundle:Session')->findAll();

        return array('documents' => $documents);
    }

    /**
     * Finds and displays a Session document.
     *
     * @Route("/{id}/show", name="sglc_admin_sessions_show")
     * @Template()
     */
    public function showAction($id)
    {
        $dm = $this->getDocumentManager();

        $document = $dm->getRepository('ServerGroveLiveChatBundle:Session')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find Session document.');
        }

        return array(
            'document' => $document,
        );
    }

    /**
     * @Route("/{id}/accept", name="sglc_admin_sessions_accept")
     * @Method("get")
     *
     * @param $id
     */
    public function acceptAction($id)
    {
        $dm = $this->getDocumentManager();

        $document = $dm->getRepository('ServerGroveLiveChatBundle:Session')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find Session document.');
        }

        $request = $this->getRequest();
        if ($request->query->has('operator')) {
            $operator = $dm->getRepository('ServerGroveLiveChatBundle:Operator')->find($request->query->get('operator'));

            if (!$operator) {
                throw $this->createNotFoundException('Unable to find Operator document.');
            }
        } else {
            $operator = $this->get('security.context')->getToken()->getUser();
        }

        $document->setStatusId(Session::STATUS_IN_PROGRESS);
        $document->setOperator($operator);

        $dm->persist($document);
        $dm->flush();

        return $this->forward('ServerGroveLiveChatBundle:Admin/Session:load', array('id' => $id));
    }

    /**
     * @Route("/{id}/close.{_format}", name="sglc_admin_sessions_close", defaults={"_format"="html"})
     * @Method("get")
     *
     * @param $id
     */
    public function closeClose($id)
    {
        $result = $this->forward('ServerGroveLiveChatBundle:Admin/Api:closeSession');

        return array();
    }

    /**
     * @Route("/{id}/{operator}/load", name="sglc_admin_sessions_load")
     * @Method("get")
     *
     * @param $id
     */
    public function loadAction($id, $operator)
    {
        $dm = $this->getDocumentManager();

        $document = $dm->getRepository('ServerGroveLiveChatBundle:Session')->find($id);

        if (!$document) {
            return array(
                'result' => false,
                'msg'    => 'Unable to find Session'
            );
        }

        return array('document' => $document);
    }

    /**
     * Returns the DocumentManager
     *
     * @return DocumentManager
     */
    private function getDocumentManager()
    {
        return $this->get('doctrine.odm.mongodb.document_manager');
    }
}
