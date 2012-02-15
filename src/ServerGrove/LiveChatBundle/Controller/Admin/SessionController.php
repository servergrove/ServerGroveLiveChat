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
     * Returns the DocumentManager
     *
     * @return DocumentManager
     */
    private function getDocumentManager()
    {
        return $this->get('doctrine.odm.mongodb.document_manager');
    }
}
