<?php

namespace ServerGrove\LiveChatBundle\Tests\Controller;

use Symfony\Component\BrowserKit\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\DomCrawler\Form;

/**
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class ChatControllerTest extends ControllerTest
{

    public function testIndexGet()
    {
        /* @var $client \Symfony\Bundle\FrameworkBundle\Client */
        $client = self::createClient();

        /* @var $crawler \Symfony\Component\DomCrawler\Crawler */
        $crawler = $client->request('GET', '/sglivechat');

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'GET response not successful: ' . $this->getErrorMessage($client->getResponse()));
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Question")')->count(), 'HTML not contains "Question"');

        $form = $crawler->selectButton('Start Chat')->form();
        $this->fillLoginFormFields($form);

        $client->submit($form);

        $this->assertTrue($client->getResponse()->isRedirect(), 'Is not redirecting: ' . $this->getErrorMessage($client->getResponse()));

        unset($client, $crawler);
    }

    public function testIndexPost()
    {
        /* @var $client Symfony\Bundle\FrameworkBundle\Client */
        $client = self::createClient();

        /* @var $crawler Symfony\Component\DomCrawler\Crawler */
        $crawler = $client->request('GET', '/sglivechat');

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'GET response not successful: ' . $this->getErrorMessage($client->getResponse()));

        $form = $crawler->selectButton('Start Chat')->form();
        $this->fillLoginFormFields($form);

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect(), 'Is not redirecting: ' . $this->getErrorMessage($client->getResponse()));

        $crawler = $client->followRedirect();

        $this->assertTrue($crawler->filter('a:contains("submit a support ticket")')->count() > 0, $this->getErrorMessage($client->getResponse()));
        unset($client, $crawler);
    }

    public function testFaq()
    {
        /* @var $client Symfony\Bundle\FrameworkBundle\Client */
        $client = self::createClient();
        $client->request('GET', '/sglivechat/faq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'GET response not successful: ' . $this->getErrorMessage($client->getResponse()));
    }

    public function testInvite()
    {
        $this->markTestIncomplete('Implement admin controller test first');
    }

    /**
     * @return void
     */
    public function testLoad()
    {
        /* @var $client Symfony\Bundle\FrameworkBundle\Client */
        $client = self::createClient();

        /* @var $crawler Symfony\Component\DomCrawler\Crawler */
        $crawler = $client->request('GET', '/sglivechat/123whatever321/load');

        $this->assertTrue($client->getResponse()->isRedirect(), 'Is not redirecting');

        $this->createSession($client);

        /* @var $crawler Symfony\Component\DomCrawler\Crawler */
        $crawler = $client->request('GET', '/sglivechat/' . $client->getRequest()->getSession()->get('chatsession') . '/load');

        $this->assertTrue($crawler->filter('a:contains("submit a support ticket")')->count() > 0);
    }

    /**
     * @param \Symfony\Component\BrowserKit\Client $client
     * @return void
     */
    private function createSession(Client $client)
    {
        /* @var $crawler \Symfony\Component\DomCrawler\Crawler */
        $crawler = $client->request('GET', '/sglivechat');

        $form = $crawler->selectButton('Start Chat')->form();
        $this->fillLoginFormFields($form);

        $client->submit($form);
    }

    /**
     * @param \Symfony\Component\DomCrawler\Form $form
     * @return void
     */
    private function fillLoginFormFields(Form $form)
    {
        $form['chat_request[name]'] = 'Ismael Ambrosi';
        $form['chat_request[email]'] = 'ismael@servergrove.com';
        $form['chat_request[question]'] = 'This is my question';
    }
}