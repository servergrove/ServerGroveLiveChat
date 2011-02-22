<?php

namespace ServerGrove\SGLiveChatBundle\Tests\Controller;

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
     * Tests TrackController->indexAction()
     */
    public function testIndex()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/js/sglivechat-tracker');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("callUpdater")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("drawStatusLink")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("var SGChatTracker =")')->count());
    }

    /**
     * Tests TrackController->updateAction()
     */
    public function testUpdateAction()
    {
        $this->markTestIncomplete("updateAction test not implemented");
    }

    /**
     * Tests TrackController->statusAction()
     */
    public function testStatusAction()
    {
        $this->markTestIncomplete("statusAction test not implemented");
    }

    /**
     * Tests TrackController->resetAction()
     */
    public function testResetAction()
    {
        $this->markTestIncomplete("resetAction test not implemented");
    }

}

