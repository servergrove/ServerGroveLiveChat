<?php

namespace ServerGrove\LiveChatBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ServerGrove\LiveChatBundle\Document\OperatorDepartment;
use ServerGrove\LiveChatBundle\Form\OperatorDepartmentType;

/**
 * OperatorDepartment controller.
 *
 * @Route("/operators/departments")
 */
class DepartmentController extends Controller
{
    /**
     * Lists all OperatorDepartment documents.
     *
     * @Route("/", name="sglc_admin_operators_departments")
     * @Template()
     */
    public function indexAction()
    {
        $dm = $this->getDocumentManager();

        $documents = $dm->getRepository('ServerGroveLiveChatBundle:OperatorDepartment')->findAll();

        return array('documents' => $documents);
    }

    /**
     * Finds and displays a OperatorDepartment document.
     *
     * @Route("/{id}/show", name="sglc_admin_operators_departments_show")
     * @Template()
     */
    public function showAction($id)
    {
        $dm = $this->getDocumentManager();

        $document = $dm->getRepository('ServerGroveLiveChatBundle:OperatorDepartment')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find OperatorDepartment document.');
        }

        var_dump($document->getOperators()->count());

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'document'      => $document,
            'delete_form'   => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new OperatorDepartment document.
     *
     * @Route("/new", name="sglc_admin_operators_departments_new")
     * @Template()
     */
    public function newAction()
    {
        $document = new OperatorDepartment();
        $form = $this->createForm(new OperatorDepartmentType(), $document);

        return array(
            'document' => $document,
            'form'     => $form->createView()
        );
    }

    /**
     * Creates a new OperatorDepartment document.
     *
     * @Route("/create", name="sglc_admin_operators_departments_create")
     * @Method("post")
     * @Template("ServerGroveLiveChatBundle:OperatorDepartment:new.html.twig")
     */
    public function createAction()
    {
        $document = new OperatorDepartment();
        $request = $this->getRequest();
        $form = $this->createForm(new OperatorDepartmentType(), $document);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $dm = $this->getDocumentManager();
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('sglc_admin_operators_departments_show', array('id' => $document->getId())));

        }

        return array(
            'document' => $document,
            'form'     => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing OperatorDepartment document.
     *
     * @Route("/{id}/edit", name="sglc_admin_operators_departments_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $dm = $this->getDocumentManager();

        $document = $dm->getRepository('ServerGroveLiveChatBundle:OperatorDepartment')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find OperatorDepartment document.');
        }

        $editForm = $this->createForm(new OperatorDepartmentType(), $document);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'document'    => $document,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing OperatorDepartment document.
     *
     * @Route("/{id}/update", name="sglc_admin_operators_departments_update")
     * @Method("post")
     * @Template("ServerGroveLiveChatBundle:OperatorDepartment:edit.html.twig")
     */
    public function updateAction($id)
    {
        $dm = $this->getDocumentManager();

        $document = $dm->getRepository('ServerGroveLiveChatBundle:OperatorDepartment')->find($id);

        if (!$document) {
            throw $this->createNotFoundException('Unable to find OperatorDepartment document.');
        }

        $editForm = $this->createForm(new OperatorDepartmentType(), $document);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $dm->persist($document);
            $dm->flush();

            return $this->redirect($this->generateUrl('sglc_admin_operators_departments_edit', array('id' => $id)));
        }

        return array(
            'document'    => $document,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a OperatorDepartment document.
     *
     * @Route("/{id}/delete", name="sglc_admin_operators_departments_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $dm = $this->getDocumentManager();
            $document = $dm->getRepository('ServerGroveLiveChatBundle:OperatorDepartment')->find($id);

            if (!$document) {
                throw $this->createNotFoundException('Unable to find OperatorDepartment document.');
            }

            $dm->remove($document);
            $dm->flush();
        }

        return $this->redirect($this->generateUrl('sglc_admin_operators_departments'));
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
     * @return \Doctrine\ODM\MongoDB\DocumentManager
     */
    private function getDocumentManager()
    {
        return $this->get('doctrine.odm.mongodb.document_manager');
    }
}
