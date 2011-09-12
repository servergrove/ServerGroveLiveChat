<?php

namespace ServerGrove\LiveChatBundle\Tests\Controller;

use ServerGrove\LiveChatBundle\Document;
use ServerGrove\LiveChatBundle\Document\CannedMessage;
use ServerGrove\LiveChatBundle\Document\Operator;
use ServerGrove\LiveChatBundle\Document\Operator\Department;
use Symfony\Component\BrowserKit\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Description of AdminControllerTest
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class AdminControllerTest extends ControllerTest
{

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    private $client;

    public function testIndexAction()
    {
        /* @var $crawler \Symfony\Component\DomCrawler\Crawler */
        $crawler = $this->client->request('GET', '/admin/sglivechat');

        $this->assertGetSuccessful($this->client);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please enter your e-mail and password to access the admin panel")')->count(), 'HTML not contains Login description');

        # Test Login
        $form = $crawler->selectButton('Login')->form();
        $this->fillLoginFormFields($form);
        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirection(), 'Is not redirecting');

        $this->logout();

        $this->login();

        $this->client->followRedirects(false);

        $crawler = $this->client->request('GET', '/admin/sglivechat');
        $this->assertTrue($this->client->getResponse()->isRedirect(), $this->getErrorMessage($this->client->getResponse()));

        $this->logout();

        unset($crawler);
    }

    public function testCannedMessagesAction()
    {
        $this->login();

        /* @var $crawler \Symfony\Component\DomCrawler\Crawler */
        $crawler = $this->client->request('GET', '/admin/sglivechat/canned-messages');

        $this->assertGetSuccessful($this->client);
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Canned Messages list")')->count());

        $this->logout();

        unset($crawler);
    }

    public function testEditCannedMessageAction()
    {
        $this->login();

        $cannedMessage = $this->getTestCannedMessage();
        $dm = $this->getDocumentManager();
        $dm->flush();

        /* @var $crawler \Symfony\Component\DomCrawler\Crawler */
        $crawler = $this->client->request('GET', '/admin/sglivechat/canned-message/' . $cannedMessage->getId());

        $this->assertGetSuccessful($this->client);
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Edit canned message")')->count());

        $form = $crawler->selectButton('Submit')->form();
        $newTitle = $form['canned_message[title]'] = 'New Title of canned message';
        $crawler = $this->client->submit($form);

        $this->assertPostRedirect($this->client);

        $dm->refresh($cannedMessage);
        $this->assertEquals($newTitle, $cannedMessage->getTitle());

        $dm->remove($cannedMessage);
        $dm->flush();

        $this->logout();

        unset($crawler);
    }

    public function testNewCannedMessage()
    {
        $this->login();

        /* @var $crawler \Symfony\Component\DomCrawler\Crawler */
        $crawler = $this->client->request('GET', '/admin/sglivechat/canned-message');

        $this->assertGetSuccessful($this->client);
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Add new canned message")')->count());

        $form = $crawler->selectButton('Submit')->form();
        $title = $form['canned_message[title]'] = 'Title of canned message';
        $form['canned_message[content]'] = 'Content of canned message';
        $crawler = $this->client->submit($form);

        $this->assertPostRedirect($this->client);

        /* @var $dm Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $this->getDocumentManager();
        $this->assertGreaterThan(0, $dm->getRepository('ServerGroveLiveChatBundle:CannedMessage')->findBy(array('title' => $title))->count(), 'Unexistent canned message after form submit');

        $dm->createQueryBuilder('ServerGroveLiveChatBundle:CannedMessage')
                ->remove()
                ->field('title')
                ->equals($title)
                ->getQuery()
                ->execute();

        $this->assertEquals(0, $dm->getRepository('ServerGroveLiveChatBundle:CannedMessage')->findBy(array('title' => $title))->count(), 'Canned message still exists after removal');

        $dm->flush();

        $this->logout();

        unset($crawler);
    }

    public function testOperatorsAction()
    {
        $this->login();

        /* @var $crawler \Symfony\Component\DomCrawler\Crawler */
        $crawler = $this->client->request('GET', '/admin/sglivechat/operators');

        $this->assertGetSuccessful($this->client);
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Operators list")')->count());

        $this->logout();

        unset($crawler);
    }

    public function testNewOperator()
    {
        $this->login();

        /* @var $crawler \Symfony\Component\DomCrawler\Crawler */
        $crawler = $this->client->request('GET', '/admin/sglivechat/operator');

        $this->assertGetSuccessful($this->client);
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Add new operator")')->count());

        $form = $crawler->selectButton('Submit')->form();

        $form['operator[name]'] = 'John Doe';
        $form['operator[email][first]'] = $form['operator[email][second]'] = 'john@doe.com';
        $form['operator[passwd][first]'] = $form['operator[passwd][second]'] = 'johnpass';

        $crawler = $this->client->submit($form);

        $this->assertPostRedirect($this->client);

        /* @var $dm Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $this->getDocumentManager();
        $this->assertGreaterThan(0, $dm->getRepository('ServerGroveLiveChatBundle:Operator')->findBy(array('email' => 'john@doe.com'))->count(), 'Unexistent operator after form submit');

        $dm->createQueryBuilder('ServerGroveLiveChatBundle:Operator')
                ->remove()
                ->field('email')
                ->equals('john@doe.com')
                ->getQuery()
                ->execute();
        $dm->flush();

        $this->assertEquals(0, $dm->getRepository('ServerGroveLiveChatBundle:Operator')->findBy(array('email' => 'john@doe.com'))->count(), 'operator still exists after removal');

        $this->logout();

        unset($dm, $crawler);
    }

    public function testEditOperator()
    {
        /* @var $dm Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $this->getDocumentManager();

        $operator = new Operator();
        $operator->setName('John Doe');
        $operator->setEmail('john@doe.com');
        $operator->setPasswd('johnpass');

        $dm->persist($operator);
        $dm->flush();

        $this->login();

        /* @var $crawler \Symfony\Component\DomCrawler\Crawler */
        $crawler = $this->client->request('GET', '/admin/sglivechat/operator/' . $operator->getId());
        $this->assertGetSuccessful($this->client);
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Edit operator ")')->count());

        $form = $crawler->selectButton('Submit')->form();

        $form['operator[name]'] = 'John J. Doe';

        $crawler = $this->client->submit($form);

        $this->assertPostRedirect($this->client);

        $dm->refresh($operator);
        $this->assertEquals('John J. Doe', $operator->getName());

        $dm->remove($operator);

        $this->logout();

        unset($dm, $operator, $crawler);
    }

    /**
     * @return void
     */
    public function testOperatorDepartmentsAction()
    {
        $this->login();

        /* @var $crawler \Symfony\Component\DomCrawler\Crawler */
        $crawler = $this->client->request('GET', '/admin/sglivechat/operator/departments');

        $this->assertGetSuccessful($this->client);
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Departments list")')->count());

        $this->logout();

        unset($crawler);
    }

    public function testNewOperatorDepartment()
    {
        $this->login();

        /* @var $crawler \Symfony\Component\DomCrawler\Crawler */
        $crawler = $this->client->request('GET', '/admin/sglivechat/operator/department');

        $this->assertGetSuccessful($this->client);
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Add new department")')->count());

        $form = $crawler->selectButton('Submit')->form();

        $form['operator_department[name]'] = 'Test Department';

        $crawler = $this->client->submit($form);

        $this->assertPostRedirect($this->client);

        /* @var $dm Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $this->getDocumentManager();
        $this->assertGreaterThan(0, $dm->getRepository('ServerGroveLiveChatBundle:Operator\\Department')->findBy(array('name' => 'Test Department'))->count(), 'Unexistent department after form submit');

        $dm->createQueryBuilder('ServerGroveLiveChatBundle:Operator:Department')
                ->remove()
                ->field('name')
                ->equals('Test Department')
                ->getQuery()
                ->execute();
        $dm->flush();

        $this->assertEquals(0, $dm->getRepository('ServerGroveLiveChatBundle:Operator:Department')->findBy(array('name' => 'Test Department'))->count(), 'Department still exists after removal');

        $this->logout();

        unset($dm, $operator, $crawler);
    }

    public function testEditOperatorDepartment()
    {
        $this->login();

        /* @var $dm Doctrine\ODM\MongoDB\DocumentManager */
        $dm = $this->getDocumentManager();

        $department = new Department();
        $department->setName('Test department');

        $dm->persist($department);
        $dm->flush();

        /* @var $crawler \Symfony\Component\DomCrawler\Crawler */
        $crawler = $this->client->request('GET', '/admin/sglivechat/operator/department/' . $department->getId());
        $this->assertGetSuccessful($this->client);
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Edit department ")')->count());

        $form = $crawler->selectButton('Submit')->form();

        $form['operator_department[name]'] = 'My test department';

        $crawler = $this->client->submit($form);

        $this->assertPostRedirect($this->client);

        $dm->refresh($department);
        $this->assertEquals('My test department', $department->getName());

        $dm->remove($department);

        $this->logout();

        unset($dm, $operator, $crawler);
    }

    public function testSessionsAction()
    {
        $this->login();

        /* @var $crawler \Symfony\Component\DomCrawler\Crawler */
        $crawler = $this->client->request('GET', '/admin/sglivechat/console/sessions');

        $this->assertGetSuccessful($this->client);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Current Sessions:")')->count());

        $this->logout();

        unset($crawler);
    }

    public function testVisitorsAction()
    {
        $this->login();

        /* @var $crawler \Symfony\Component\DomCrawler\Crawler */
        $crawler = $this->client->request('GET', '/admin/sglivechat/visitors');

        $this->assertGetSuccessful($this->client);
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Visitors list")')->count());

        $this->logout();

        unset($crawler);
    }

    public function testVisitsAction()
    {
        $this->login();

        /* @var $crawler \Symfony\Component\DomCrawler\Crawler */
        $crawler = $this->client->request('GET', '/admin/sglivechat/visits');

        $this->assertGetSuccessful($this->client);
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Visits list")')->count());

        $this->logout();

        unset($crawler);
    }

    public function testCannedMessageAction()
    {
        $this->login();

        /* @var $crawler \Symfony\Component\DomCrawler\Crawler */
        $crawler = $this->client->request('GET', '/admin/sglivechat/canned-message');

        $this->assertGetSuccessful();
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Add new canned message")')->count());

        $this->logout();
    }


    public function testChatSessionAction()
    {
        $chatSession = $this->getTestSession();
        $this->login();
        $this->client->request('GET', '/admin/sglivechat/console/chat-session/' . $chatSession->getId());
        $this->assertGetSuccessful();
        $this->logout();
    }

    public function testChatSessionsAction()
    {
        $this->login();
        $this->client->request('GET', '/admin/sglivechat/console/chat-sessions');
        $this->assertGetSuccessful();
        $this->logout();
    }

    public function testCloseChatAction()
    {
        $dm = $this->getDocumentManager();
        $chatSession = $this->getTestSession();
        $this->login();
        $this->client->request('GET', '/admin/sglivechat/console/close/' . $chatSession->getId());
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $dm->refresh($chatSession);
        $this->assertEquals(Document\Session::STATUS_CLOSED, $chatSession->getStatusId());
        $this->logout();
    }

    public function testCurrentVisitsAction()
    {
        $this->login();
        $this->client->request('GET', '/admin/sglivechat/console/current-visits.json');
        $this->assertGetSuccessful();
        $this->logout();
    }

    public function testLoginAction()
    {
        $this->login();
        $this->client->request('GET', '/admin/sglivechat/login');
        $this->assertGetSuccessful();
        $this->logout();
    }

    public function testRequestedChatsAction()
    {
        $this->login();
        $this->client->request('GET', '/admin/sglivechat/console/requested-chats');
        $this->assertGetSuccessful();
        $this->logout();
    }

    public function testRequestsAction()
    {
        $this->login();
        $this->client->request('GET', '/admin/sglivechat/console/sessions');
        $this->assertGetSuccessful();
        $this->logout();
    }

    public function testSessionsServiceAction()
    {
        $this->login();
        $this->client->request('GET', '/admin/sglivechat/console/sessions-service.json');
        $this->assertGetSuccessful();
        $this->logout();
    }

    public function testVisitorAction()
    {
        $this->login();
        $visitor = $this->getTestVisitor();
        $this->getDocumentManager()->flush();
        $this->client->request('GET', '/admin/sglivechat/visitor/' . $visitor->getId());
        $this->assertGetSuccessful();
        $this->logout();
    }

    protected function setUp()
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    private function assertGetSuccessful()
    {
        $this->assertTrue($this->client->getResponse()->isOk(), 'GET response not successful. Code: ' . $this->client->getResponse()->getStatusCode() . '_' . $this->getErrorMessage($this->client->getResponse()));
    }

    private function assertPostRedirect()
    {
        $this->assertEquals('POST', $this->client->getRequest()->getMethod(), 'Request type is not POST');
        $this->assertTrue($this->client->getResponse()->isRedirect(), 'POST request is redirecting');
    }

    private function fillLoginFormFields($form)
    {
        $operator = $this->getTestOperator();
        $form['login[email]'] = $operator->getEmail();
        $form['login[passwd]'] = 'ismapass';
    }

    private function login()
    {
        $crawler = $this->client->request('GET', '/admin/sglivechat');

        if ($this->client->getResponse()->isSuccessful()) {
            $form = $crawler->selectButton('Login')->form();
            $this->fillLoginFormFields($form);

            return $this->client->submit($form);
        }

        return false;
    }

    private function logout()
    {
        return $this->client->request('GET', '/admin/sglivechat/logout');
    }

}