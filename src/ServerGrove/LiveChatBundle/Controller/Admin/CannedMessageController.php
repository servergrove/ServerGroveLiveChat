<?php

namespace ServerGrove\LiveChatBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ServerGrove\LiveChatBundle\Document\CannedMessage;
use ServerGrove\LiveChatBundle\Form\CannedMessageType;

/**
 * CannedMessage controller.
 *
 * @Route("/canned-messages")
 */
class CannedMessageController extends Controller
{
    /**
     * Lists all CannedMessage documents.
     *
     * @Route("/", name="sglc_admin_canned_messages")
     * @Template()
     */
    public function indexAction()
    {
        $dm = $this->getDocumentManager();

        $documents = $dm->getRepository('ServerGroveLiveChatBundle:CannedMessage')->findAll();

        return array('documents' => $documents);
    }

    /**
     * Finds and displays a CannedMessage document.
     *
     * @Route("/{id}/show", name="sglc_admin_canned_messages_show")
     * @Template()
     */
    public function showAction($id)
    {
        $dm = $this->getDocumentManager();

        $document = $dm->getRepository('ServerGroveLiveChatBundle:CannedMessage')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find CannedMessage document.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'document'    => $document,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new CannedMessage document.
     *
     * @Route("/new", name="sglc_admin_canned_messages_new")
     * @Template()
     */
    public function newAction()
    {
        $document = new CannedMessage();
        $form = $this->createForm(new CannedMessageType(), $document);

        return array(
            'document' => $document,
            'form'     => $form->createView()
        );
    }

    /**
     * Creates a new CannedMessage document.
     *
     * @Route("/create", name="sglc_admin_canned_messages_create")
     * @Method("post")
     * @Template("ServerGroveLiveChatBundle:Admin/CannedMessage:new.html.twig")
     */
    public function createAction()
    {
        $document = new CannedMessage();
        $request = $this->getRequest();
        $form = $this->createForm(new CannedMessageType(), $document);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $dm = $this->getDocumentManager();
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('sglc_admin_canned_messages_show', array('id' => $document->getId())));
        }

        return array(
            'document' => $document,
            'form'     => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing CannedMessage document.
     *
     * @Route("/{id}/edit", name="sglc_admin_canned_messages_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $dm = $this->getDocumentManager();

        $document = $dm->getRepository('ServerGroveLiveChatBundle:CannedMessage')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find CannedMessage document.');
        }

        $editForm = $this->createForm(new CannedMessageType(), $document);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'document'    => $document,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing CannedMessage document.
     *
     * @Route("/{id}/update", name="sglc_admin_canned_messages_update")
     * @Method("post")
     * @Template("ServerGroveLiveChatBundle:Admin/CannedMessage:edit.html.twig")
     */
    public function updateAction($id)
    {
        $dm = $this->getDocumentManager();

        $document = $dm->getRepository('ServerGroveLiveChatBundle:CannedMessage')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find CannedMessage document.');
        }

        $editForm = $this->createForm(new CannedMessageType(), $document);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('sglc_admin_canned_messages_edit', array('id' => $id)));
        }

        return array(
            'document'    => $document,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a CannedMessage document.
     *
     * @Route("/{id}/delete", name="sglc_admin_canned_messages_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $dm = $this->getDocumentManager();
            $document = $dm->getRepository('ServerGroveLiveChatBundle:CannedMessage')->find($id);

            if (!$document) {
                throw $this->createNotFoundException('Unable to find CannedMessage document.');
            }

            $dm->remove($document);
            $dm->flush();
        }

        return $this->redirect($this->generateUrl('sglc_admin_canned_messages'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm();
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
