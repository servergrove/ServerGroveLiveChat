<?php

namespace ServerGrove\LiveChatBundle\Tests\Controller;

use Symfony\Component\BrowserKit\Client;
use Symfony\Component\DomCrawler\Form;

/**
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class ChatControllerTest extends ControllerTest
{

    public function testIndexAction()
    {
        /* @var $client \Symfony\Bundle\FrameworkBundle\Client */
        $client = self::createClient();

        /* @var $crawler \Symfony\Component\DomCrawler\Crawler */
        $crawler = $client->request('GET', $this->getUrl('sglc_chat_homepage'));

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'GET response not successful: ' . $this->getErrorMessage($client->getResponse()));
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Question")')->count(), 'HTML not contains "Question"');

        $form = $crawler->selectButton('Start Chat')->form();
        $this->fillLoginFormFields($form);

        $client->submit($form);

        $this->assertTrue($client->getResponse()->isRedirect(), 'Is not redirecting: ' . $this->getErrorMessage($client->getResponse()));

        $crawler = $client->followRedirect();

        $this->assertTrue($crawler->filter('a:contains("submit a support ticket")')->count() > 0, $this->getErrorMessage($client->getResponse()));

        unset($client, $crawler);
    }

    public function testFaqAction()
    {
        /* @var $client Symfony\Bundle\FrameworkBundle\Client */
        $client = self::createClient();
        $client->request('GET', $this->getUrl('sglc_chat_faq'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'GET response not successful: ' . $this->getErrorMessage($client->getResponse()));
    }

    public function testInviteAction()
    {
        $this->markTestIncomplete('Implement admin controller test first');
    }

    /**
     * @return void
     */
    public function testLoadAction()
    {
        /* @var $client Symfony\Bundle\FrameworkBundle\Client */
        $client = self::createClient();

        /* @var $crawler Symfony\Component\DomCrawler\Crawler */
        $crawler = $client->request('GET', $this->getUrl('sglc_chat_load', array('id' => '123whatever321')));

        $this->assertTrue($client->getResponse()->isRedirect(), 'Is not redirecting');

        $this->createSession($client);

        /* @var $crawler Symfony\Component\DomCrawler\Crawler */
        $crawler = $client->request('GET', $this->getUrl('sglc_chat_load', array('id' => $client->getRequest()->getSession()->get('chatsession'))));

        $this->assertTrue($crawler->filter('a:contains("submit a support ticket")')->count() > 0);
    }

    /**
     * @param \Symfony\Component\BrowserKit\Client $client
     * @return void
     */
    private function createSession(Client $client)
    {
        /* @var $crawler \Symfony\Component\DomCrawler\Crawler */
        $crawler = $client->request('GET', $this->getUrl('sglc_chat_homepage'));

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