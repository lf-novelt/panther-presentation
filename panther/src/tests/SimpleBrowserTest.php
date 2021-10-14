<?php

declare(strict_types=1);

namespace Tests;

use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\PantherTestCase;

class SimpleBrowserTest extends PantherTestCase
{
    private $USERNAME = null;
    private $PASSWORD = null;
    static $client = null;

    public function __construct(?string $name = null, array $data = [], $dataName = '') {
        parent::__construct($name, $data, $dataName);

        $this->USERNAME = getenv('AD_USERNAME');
        $this->PASSWORD = getenv('AD_PASSWORD');
    }

    public function setUp(): void
    {
        $pantherClient = self::$pantherClient;

        if ($pantherClient === null) {
            $this->createAndLoginPanther();
        }

        parent::setUp();
    }

    private function createAndLoginPanther()
    {
        $pantherClient = static::createPantherClient(
        [
            'external_base_uri' => 'https://extranet-uat.novel-t.ch/xmart'
        ],
        [],
        [
            'capabilities' => [
                'acceptInsecureCerts' => true,
            ]
        ]
        );

        self::$pantherClient->request('GET', '/');
        
        self::$pantherClient->waitForVisibility("input[name='loginfmt']");
        self::$pantherClient->takeScreenshot('1-login.png');
        $crawler = self::$pantherClient->getCrawler();
        $crawler->filter("input[name='loginfmt']")->sendKeys($this->USERNAME);
        self::$pantherClient->takeScreenshot('2-loginUsernameFilled.png');
        $crawler->filter("input#idSIButton9")->click();

        self::$pantherClient->waitForVisibility("input[name='passwd']");
        self::$pantherClient->takeScreenshot('3-loginUsernameDone.png');

        $crawler = self::$pantherClient->getCrawler();
        $crawler->filter("input[name='passwd']")->sendKeys($this->PASSWORD );
        $crawler->filter("input#idSIButton9")->click();

        self::$pantherClient->waitForVisibility("input#idBtn_Back");
        self::$pantherClient->takeScreenshot('4-loginPassword.png');
        $crawler = self::$pantherClient->getCrawler();
        $crawler->filter("input#idBtn_Back")->click();

        self::$pantherClient->takeScreenshot('loginNo.png');
        self::$pantherClient->waitForVisibility("mart-figures-card h4");
        self::$pantherClient->takeScreenshot('5-logged.png');
    }

    public function testXmartHome(): void
    {

        self::$pantherClient->request('GET', '/');

        self::$pantherClient->waitForVisibility("mart-figures-card h4");
        self::$pantherClient->takeScreenshot('5-testXmartHome_logged.png');

        $crawler = self::$pantherClient->getCrawler();
        $xMart = $crawler->filter(".xmart-page-titles > div > p:nth-of-type(1)")->getText();
        $this->assertEquals('xMart is a self-service platform for data managers.', $xMart);
    }

    
    public function testXmartWisse(): void
    {

        self::$pantherClient->request('GET', '/WIISE');

        
        self::$pantherClient->waitForVisibility("mart-figures-card h4");
        self::$pantherClient->takeScreenshot('5-testXmartWiise_logged.png');

        $crawler = self::$pantherClient->getCrawler();
        $xMartTitle = $crawler->filter(".mart-title a")->getText();
        $this->assertEquals('Wiise Mart', $xMartTitle);
    }
}