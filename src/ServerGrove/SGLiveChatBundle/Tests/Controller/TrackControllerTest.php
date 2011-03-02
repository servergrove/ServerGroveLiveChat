<?php

namespace ServerGrove\SGLiveChatBundle\Tests\Controller;

use Symfony\Component\BrowserKit\Client;

use Symfony\Component\BrowserKit\Response;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

/**
 * TrackController test case.
 */
class TrackControllerTest extends WebTestCase
{

    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Tests TrackController->updateAction()
     */
    public function testUpdateActionGet()
    {
        $client = $this->createClient();
        $this->createSession($client);

        $crawler = $client->request('GET', '/js/sglivechat-tracker/update');
        $this->assertEquals('1', $client->getResponse()->getContent());
    }

    /**
     * Tests TrackController->updateAction()
     */
    public function testUpdateActionPost()
    {
        $client = $this->createClient();
        $this->createSession($client);

        $crawler = $client->request('POST', '/js/sglivechat-tracker/update');
        $this->assertEquals('1', $client->getResponse()->getContent());
    }

    /**
     * Tests TrackController->statusAction()
     */
    public function testIndexAction()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/js/sglivechat-tracker');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("drawStatusLink")')->count(), '"drawStatusLink" not found un content');
        $this->assertGreaterThan(0, $crawler->filter('html:contains("callUpdater")')->count(), '"callUpdater" not found un content');
        $this->assertGreaterThan(0, $crawler->filter('html:contains("var SGChatTracker =")')->count(), '"var SGChatTracker =" not found un content');
    }

    /**
     * Tests TrackController->indexAction()
     */
    public function testStatusAction()
    {
        $client = $this->createClient();
        $crawler = $this->createSession($client);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("drawStatusLink")')->count(), '"drawStatusLink" not found un content');
        $this->assertGreaterThan(0, $crawler->filter('html:contains("callUpdater")')->count(), '"callUpdater" not found un content');
        $this->assertGreaterThan(0, $crawler->filter('html:contains("var SGChatTracker =")')->count(), '"var SGChatTracker =" not found un content');
    }

    /**
     * Tests TrackController->resetAction()
     */
    public function testResetAction()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/js/sglivechat-tracker/reset');

        $this->assertNull($client->getRequest()->cookies->get('vtrid'));
        $this->assertNull($client->getRequest()->cookies->get('vsid'));
    }

    private function createSession(Client $client)
    {
        return $client->request('GET', '/js/sglivechat-tracker/status.html');
    }
}

