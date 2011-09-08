<?php

namespace ServerGrove\LiveChatBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class ControllerTest extends WebTestCase
{
    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return string
     */
    protected function getErrorMessage(Response $response)
    {
        switch ($response->headers->get('content-type')) {
            case 'application/json':
                $json = json_decode($response->getContent());
                if (is_array($json) && count($json) == 1 && !isset($json['message'])) {
                    $json = current($json);
                }
                if (isset($json->message)) {
                    return $json->message;
                }
                break;
            case 'text/plain':
                if (preg_match('/\[message\](.*)/', $response->getContent(), $o)) {
                    return wordwrap(html_entity_decode(trim($o[1])), 100);
                }
                return $response->getContent();
            case 'text/xml':
                $doc = new \DOMDocument();
                $doc->loadXML($response->getContent());
                if ($doc->getElementsByTagName('error')->length > 0 && $doc->getElementsByTagName('exception')->length > 0) {
                    return trim($doc->getElementsByTagName('exception')->item(0)->attributes->getNamedItem('message')->nodeValue);
                }
                return $response->getContent();
            default:
                if (500 == $response->getStatusCode() && strpos($response->getContent(), '<strong>500</strong>') !== false) {
                    $doc = new \DOMDocument();
                    $doc->loadHTML($response->getContent());
                    return trim($doc->getElementsByTagName('h1')->item(0)->nodeValue);
                } else if (200 != $response->getStatusCode()) {
                    return current(explode("\r\n", $response->__toString()));
                }
        }

        return null;
    }
}
