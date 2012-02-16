<?php

namespace ServerGrove\LiveChatBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiListener
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class ApiListener
{
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        if ($event->hasResponse()) {
            return;
        }

        $apiPrefix = 'sglc_admin_api_';
        if (substr($event->getRequest()->get('_route'), 0, strlen($apiPrefix)) != $apiPrefix) {
            return;
        }

        $result = $event->getControllerResult();

        if (!is_array($result)) {
            return;
        }

        $response = new Response();

        switch ($event->getRequest()->get('_format')) {
            case 'json':
                $response->setContent(json_encode($result));
                $response->headers->set('Content-type', 'application/json');
                break;
            case 'html':
                ob_start();
                var_dump($result);
                $content = ob_get_clean();
                $response->setContent($content);
                break;
            case 'txt':
                $response->headers->set('Content-type', 'text/plain');
                $response->setContent(var_export($result, true));
                break;
            default:
                throw new HttpException(403, 'Invalid format');
        }

        $event->setResponse($response);
    }
}
