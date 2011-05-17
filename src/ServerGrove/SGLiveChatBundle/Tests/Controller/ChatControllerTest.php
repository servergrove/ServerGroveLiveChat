<?php

namespace ServerGrove\SGLiveChatBundle\Tests\Controller;

use Symfony\Component\BrowserKit\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class ChatControllerTest extends WebTestCase
{

    public function testIndexGet()
    {
        /* @var $client Symfony\Bundle\FrameworkBundle\Client */
        $client = $this->createClient();

        /* @var $crawler Symfony\Component\DomCrawler\Crawler */
        $crawler = $client->request('GET', '/sglivechat');

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'GET response not successful: ' . $client->getResponse()->getContent());

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Question")')->count(), 'HTML not contains "Question"');

        $form = $crawler->selectButton('Start Chat')->form();
        $this->fillLoginFormFields($form);

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirection(), 'Is not redirecting');
        
        unset($client, $crawler);
    }

    public function testIndexPost()
    {
        /* @var $client Symfony\Bundle\FrameworkBundle\Client */
        $client = $this->createClient();

        /* @var $crawler Symfony\Component\DomCrawler\Crawler */
        $crawler = $client->request('GET', '/sglivechat');

        $form = $crawler->selectButton('Start Chat')->form();
        $this->fillLoginFormFields($form);

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect(), 'Is not redirecting');

        $crawler = $client->followRedirect();        
        
        $this->assertTrue($crawler->filter('a:contains("submit a support ticket")')->count() > 0);
        unset($client, $crawler);
    }

    public function testInvite()
    {
        $this->markTestIncomplete('Implement admin controller test first');
    }

    public function testLoad()
    {
        /* @var $client Symfony\Bundle\FrameworkBundle\Client */
        $client = $this->createClient();

        /* @var $crawler Symfony\Component\DomCrawler\Crawler */
        $crawler = $client->request('GET', '/sglivechat/123whatever321/load');
                
        $this->assertTrue($client->getResponse()->isRedirect(), 'Is not redirecting');

        $this->createSession($client);

        /* @var $crawler Symfony\Component\DomCrawler\Crawler */
        $crawler = $client->request('GET', '/sglivechat/' . $client->getRequest()->getSession()->get('chatsession') . '/load');

        $this->assertTrue($crawler->filter('a:contains("submit a support ticket")')->count() > 0);
    }

    protected function setUp()
    {
        parent::setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    private function createSession(Client $client)
    {
        /* @var $crawler Symfony\Component\DomCrawler\Crawler */
        $crawler = $client->request('GET', '/sglivechat');

        $form = $crawler->selectButton('Start Chat')->form();
        $this->fillLoginFormFields($form);

        $client->submit($form);
    }

    private function fillLoginFormFields($form)
    {
        $form['chatrequest[name]'] = 'Ismael Ambrosi';
        $form['chatrequest[email]'] = 'ismael@servergrove.com';
        $form['chatrequest[question]'] = 'This is my question';
    }
}