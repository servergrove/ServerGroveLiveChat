<?php

namespace ServerGrove\LiveChatBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ServerGrove\LiveChatBundle\Document\Operator;
use ServerGrove\LiveChatBundle\Form\OperatorType;
use ServerGrove\LiveChatBundle\Form\OperatorEditType;
use ServerGrove\LiveChatBundle\Form\OperatorPasswordType;

/**
 * Operator controller.
 *
 * @Route("/operators")
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class OperatorController extends Controller
{
    /**
     * Lists all Operator documents.
     *
     * @Route("/", name="sglc_admin_operators")
     * @Template()
     */
    public function indexAction()
    {
        $dm = $this->getDocumentManager();

        $documents = $dm->getRepository('ServerGroveLiveChatBundle:Operator')->findAll();

        return array('documents' => $documents);
    }

    /**
     * Finds and displays a Operator document.
     *
     * @Route("/{id}/show", name="sglc_admin_operators_show")
     * @Template()
     */
    public function showAction($id)
    {
        $dm = $this->getDocumentManager();

        $document = $dm->getRepository('ServerGroveLiveChatBundle:Operator')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find Operator document.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'document'    => $document,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Operator document.
     *
     * @Route("/new", name="sglc_admin_operators_new")
     * @Template()
     */
    public function newAction()
    {
        $document = new Operator();
        $form = $this->createForm(new OperatorType(), $document);

        return array(
            'document' => $document,
            'form'     => $form->createView()
        );
    }

    /**
     * Creates a new Operator document.
     *
     * @Route("/create", name="sglc_admin_operators_create")
     * @Method("post")
     * @Template("ServerGroveLiveChatBundle:Admin:Operator/new.html.twig")
     */
    public function createAction()
    {
        $document = new Operator();
        $request = $this->getRequest();
        $form = $this->createForm(new OperatorType(), $document);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $dm = $this->getDocumentManager();
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('sglc_admin_operators_show', array('id' => $document->getId())));
        }

        return array(
            'document' => $document,
            'form'     => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Operator document.
     *
     * @Route("/{id}/edit", name="sglc_admin_operators_edit")
     * @Route("/{id}/password/edit", name="sglc_admin_operators_password_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $dm = $this->getDocumentManager();

        $document = $dm->getRepository('ServerGroveLiveChatBundle:Operator')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find Operator document.');
        }

        $isEditingPassword = 'sglc_admin_operators_password_edit' == $this->getRequest()->get('_route');
        $type = $isEditingPassword ? new OperatorPasswordType() : new OperatorEditType();
        $editForm = $this->createForm($type, $document);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'document'    => $document,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'post_route'  => ('sglc_admin_operators'.($isEditingPassword ? '_password' : '').'_update')
        );
    }

    /**
     * Edits an existing Operator document.
     *
     * @Route("/{id}/update", name="sglc_admin_operators_update")
     * @Route("/{id}/password/update", name="sglc_admin_operators_password_update")
     * @Method("post")
     * @Template("ServerGroveLiveChatBundle:Admin:Operator/edit.html.twig")
     */
    public function updateAction($id)
    {
        $dm = $this->getDocumentManager();

        /** @var $document Operator */
        $document = $dm->getRepository('ServerGroveLiveChatBundle:Operator')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find Operator document.');
        }

        $request = $this->getRequest();
        $isEditingPassword = 'sglc_admin_operators_password_update' == $request->get('_route');

        $type = $isEditingPassword ? new OperatorPasswordType() : new OperatorEditType();
        $editForm = $this->createForm($type, $document);
        $deleteForm = $this->createDeleteForm($id);

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            if ($isEditingPassword) {
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($document);
                $password = $encoder->encodePassword($document->getPasswd(), $document->getSalt());
                $document->setPasswd($password);
            }

            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl($isEditingPassword ? 'sglc_admin_operators_password_edit' : 'sglc_admin_operators_edit', array('id' => $id)));
        }

        return array(
            'document'    => $document,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'post_route'  => ('sglc_admin_operators'.($isEditingPassword ? '_password' : '').'_update')
        );
    }

    /**
     * Deletes a Operator document.
     *
     * @Route("/{id}/delete", name="sglc_admin_operators_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $dm = $this->getDocumentManager();
            $document = $dm->getRepository('ServerGroveLiveChatBundle:Operator')->find($id);

            if (!$document) {
                throw $this->createNotFoundException('Unable to find Operator document.');
            }

            $dm->remove($document);
            $dm->flush();
        }

        return $this->redirect($this->generateUrl('admin_operators'));
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
