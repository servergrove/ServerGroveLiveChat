<?php

namespace Application\ChatBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class ChatControllerTest extends WebTestCase
{

    /**
     * @var Symfony\Bundle\FrameworkBundle\Client
     */
    private $client;

    /**
     * @return Symfony\Bundle\FrameworkBundle\Client
     */
    protected function getClient()
    {
        return $this->client;
    }

    /**
     * @return Doctrine\ODM\MongoDB\DocumentManager
     */
    protected function getDocumentManager()
    {
        return $this->getClient()->getContainer()->get('doctrine.odm.mongodb.document_manager');
    }

    protected function setUp()
    {
        parent::setUp();
        $this->client = $this->createClient(array(), array('HTTP_HOST' => 'sglivechat-sg.v2.dev', 'HTTP_USER_AGENT' => 'MySuperBrowser/1.0'));
        $this->getClient()->insulate();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testIndex()
    {
        /* @var $crawler Symfony\Component\DomCrawler\Crawler */
        $crawler = $this->client->request('GET', '/livechat');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'GET response not successful');
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Question")')->count(), 'HTML not contains "Question"');

        $this->getClient()->submit($crawler->selectButton('Start Chat')->form(), array('name' => 'Ismael', 'email' => 'ismael@servergrove.com', 'question' => 'This is my comment'));
        $this->assertTrue($this->getClient()->getResponse()->isRedirect(), 'Is not redirecting');

        /* @var $crawler Symfony\Component\DomCrawler\Crawler */
        $crawler = $this->getClient()->followRedirect();

        $this->assertGreaterThan(0, $crawler->filter('html:contains("submit a support ticket")')->count(), 'HTML not contains "submit a spport ticket"');
    }

}