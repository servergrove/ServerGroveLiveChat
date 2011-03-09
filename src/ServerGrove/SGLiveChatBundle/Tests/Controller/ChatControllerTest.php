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

    private function getDefaultLoginParams()
    {
        return array(
            'chat' => array(
                'name' => 'Ismael',
                'email' => 'ismael@servergrove.com',
                'question' => 'This is my comment'));
    }

    private function getDefaultLoginCompleteNameParams()
    {
        return array(
            'chat[name]' => 'Ismael',
            'chat[email]' => 'ismael@servergrove.com',
            'chat[question]' => 'This is my comment');
    }

    protected function setUp()
    {
        parent::setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testIndexGet()
    {
        /* @var $client Symfony\Bundle\FrameworkBundle\Client */
        $client = $this->createClient();

        /* @var $crawler Symfony\Component\DomCrawler\Crawler */
        $crawler = $client->request('GET', '/sglivechat');

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'GET response not successful: ' . $client->getResponse()->getContent());

        $this->assertGreaterThan(0, $crawler->filter('html:contains("Question")')->count(), 'HTML not contains "Question"');

        $client->submit($crawler->selectButton('Start Chat')->form(), $this->getDefaultLoginCompleteNameParams());
        $this->assertTrue($client->getResponse()->isRedirect(), 'Is not redirecting');
        unset($client, $crawler);
    }

    public function testIndexPost()
    {
        /* @var $client Symfony\Bundle\FrameworkBundle\Client */
        $client = $this->createClient();

        /* @var $crawler Symfony\Component\DomCrawler\Crawler */
        $crawler = $client->request('GET', '/sglivechat');

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'GET response not successful: ' . $client->getResponse()->getContent());

        $client->submit($crawler->selectButton('Start Chat')->form(), $this->getDefaultLoginCompleteNameParams());
        $this->assertTrue($client->getResponse()->isRedirect(), 'Is not redirecting');

        $crawler = $client->followRedirect();

        $this->assertTrue($client->getResponse()->isRedirect(), 'Is not redirecting' . $client->getResponse()->getContent());
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

        $this->assertTrue($client->getResponse()->isRedirect(), 'Is not redirecting' . $client->getResponse()->getContent());
    }

    private function createSession(Client $client)
    {
        /* @var $crawler Symfony\Component\DomCrawler\Crawler */
        $crawler = $client->request('GET', '/sglivechat');
        $client->submit($crawler->selectButton('Start Chat')->form(), $this->getDefaultLoginCompleteNameParams());
    }

}