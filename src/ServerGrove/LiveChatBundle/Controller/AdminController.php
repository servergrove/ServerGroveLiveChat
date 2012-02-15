<?php

namespace ServerGrove\LiveChatBundle\Controller;

use ServerGrove\LiveChatBundle\Document\CannedMessage;
use ServerGrove\LiveChatBundle\Form\CannedMessageType;
use ServerGrove\LiveChatBundle\Controller\BaseController;
use ServerGrove\LiveChatBundle\Document\Session as ChatSession;
use Doctrine\ODM\MongoDB\Mapping\Document;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of AdminController
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class AdminController extends BaseController
{
    const DEFAULT_PAGE_ITEMS_LENGTH = 20;

    /**
     * @Route("/console/close/{id}", name="sglc_admin_console_close")
     */
    public function closeChatAction($id)
    {
        if (($chat = $this->getChatSession($id)) !== false) {
            $chat->close();
            $this->getDocumentManager()->persist($chat);
            $this->getDocumentManager()->flush();
        }

        return $this->redirect($this->generateUrl('sglc_admin_console_sessions'));
    }

    /**
     * @Route("/console/current-visits.{_format}", name="sglc_admin_console_current_visits", defaults={"_format"="html"})
     * @Template
     */
    public function currentVisitsAction($_format)
    {
        if ($_format == 'json') {
            $visits = $this->getVisitRepository()->getLastVisitsArray();

            return new \Symfony\Component\HttpFoundation\Response(json_encode($visits));
        }

        throw new NotFoundHttpException('Not supported format');

        return array('visits' => $visits);
    }

    /**
     * @Route("/", name="sglc_admin_index")
     *
     * @return RedirectResponse
     */
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('sglc_admin_console_sessions'));
    }

    /**
     * @Route("/console/requested-chats.{_format}", name="sglc_admin_console_requested_chats", defaults={"_format"="html"})
     * @Template
     */
    public function requestedChatsAction($_format)
    {
        $response = new Response();

        $this->getSessionRepository()->closeSessions();

        if ($_format == 'json') {
            $response->headers->set('Content-type', 'application/json');
            $response->setContent(json_encode($this->getRequestedChatsArray()));

            return $response;
        }

        $chats = $this->getRequestedChats();

        return $this->render('ServerGroveLiveChatBundle:Admin:requestedChats.'.$_format.'.twig', array('chats' => $chats), $response);
    }

    /**
     * @Route("/console/sessions", name="sglc_admin_console_sessions")
     * @Template
     */
    public function requestsAction()
    {
        $this->getSessionRepository()->closeSessions();

        return array('chats' => $this->getRequestedChats());
    }

    /**
     * @Template
     * @return array
     */
    public function sessionsApiAction()
    {
        return array();
    }

    /**
     * @Route("/console/sessions-service.json", name="sglc_admin_console_sessions_service")
     */
    public function sessionsServiceAction()
    {
        $this->getSessionRepository()->closeSessions();

        $json = array();
        $json['requests'] = $this->getRequestedChatsArray();
        $json['count']['requests'] = count($json['requests']);
        $json['visits'] = $this->getVisitRepository()->getLastVisitsArray();
        $json['count']['visits'] = count($json['visits']);
        $json['count']['online_operators'] = $this->getOperatorRepository()->getOnlineOperatorsCount();

        return new Response(json_encode($json), 200, array('Content-type' => 'application/json'));
    }

    /**
     * @Route("/visitor/{id}", name="sglc_admin_visitor")
     * @Template
     */
    public function visitorAction($id)
    {
        $visitor = $this->getVisitorRepository()->find($id);

        if (!$visitor) {
            throw new NotFoundHttpException();
        }

        return array(
            'visitor' => $visitor,
            'visits'  => $this->getVisitRepository()->toArray($visitor->getVisits()
            )
        );
    }

    /**
     * @Route("/visitors/{page}", name="sglc_admin_visitors", defaults={"page"="1"})
     * @Template
     */
    public function visitorsAction($page)
    {
        return $this->simpleListAction($page, 'ServerGroveLiveChatBundle:Visitor', 'visitors');
    }

    /**
     * @Route("/visits/{page}", name="sglc_admin_visits", defaults={"page"="1"})
     * @Template
     */
    public function visitsAction($page)
    {
        return $this->simpleListAction($page, 'ServerGroveLiveChatBundle:Visit', 'visits');
    }

    /**
     * @Template
     * @return array
     */
    public function adminMessagesAction()
    {
        $messages = array();

        $error = $this->getSessionStorage()->getFlash('error', '');
        if (!empty($error)) {
            $messages[] = array(
                'type'    => 'error',
                'title'   => 'Error!',
                'message' => $error
            );
        }
        $success = $this->getSessionStorage()->getFlash('msg', '');
        if (!empty($success)) {
            $messages[] = array(
                'type'    => 'success',
                'title'   => 'Success!',
                'message' => $success
            );
        }

        return array('messages' => $messages);
    }

    private function simpleListAction($page, $documentName, $documentTemplateKey)
    {
        $length = self::DEFAULT_PAGE_ITEMS_LENGTH;
        $offset = ($page - 1) * $length;

        $pages = ceil($this->getDocumentManager()->getRepository($documentName)->findAll()->count() / $length);

        $documents = $this->getDocumentManager()->getRepository($documentName)->findSlice($offset, $length);

        return array(
            $documentTemplateKey => $documents,
            'page'               => $page,
            'pages'              => $pages
        );
    }

    /**
     * @return ServerGrove\LiveChatBundle\Document\Session
     */
    private function getChatSession($id)
    {
        return $this->getSessionRepository()->find($id);
    }

    /**
     * @return Doctrine\ODM\MongoDB\LoggableCursor
     */
    private function getRequestedChats()
    {
        return $this->getSessionRepository()->getRequestedChats();
    }

    /**
     * @return array
     */
    private function getRequestedChatsArray()
    {
        return $this->getSessionRepository()->getRequestedChatsArray();
    }

    /**
     * @return boolean
     */
    private function isLogged()
    {
        return $this->getSessionStorage()->get('_operator');
    }

}