<?php

declare(strict_types=1);

namespace Tests;

use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\PantherTestCase;

class SimpleBrowserTest extends PantherTestCase
{
    const USERNAME = '';
    const PASSWORD = '';

    public function testNovelT(): void
    {
        $client = Client::createChromeClient();

        $client->request('GET', 'https://novel-t.ch/');
        $client->waitFor('li:nth-of-type(3) > .nav-link');
        $client->takeScreenshot('page-homepage.png');
        $client->clickLink('Our Focus');

        // Wait for an element to be present in the DOM (even if hidden)
        $crawler = $client->waitForElementToContain('.header-title','Our Focus');

        $headerTitle = $crawler->filter('.header-title')->text();
        $this->assertEquals("Our Focus", $headerTitle);
        $client->takeScreenshot('page-our-focus.png');

        $crawler = $client->waitFor('app-focus > .our-focus-landing');

        $crawler->filter('div:nth-of-type(3) > div:nth-of-type(2) > .solution-part-2 > .solution-2')->click();
        $client->takeScreenshot('page-integration.png');

        $p = $crawler->filter('.focus-para > p')->text();
        $this->assertEquals('Power knowledge-driven decisions with integrated, centralized and harmonized data', $p);
    }

    public function testXmart(): void
    {
        $client = Client::createChromeClient();

        $client->request('GET', 'https://extranet-uat.novel-t.ch/xmart/');
        $client->waitForVisibility("input[name='loginfmt']");
        $client->takeScreenshot('1-login.png');
        $crawler = $client->getCrawler();
        $crawler->filter("input[name='loginfmt']")->sendKeys(self::USERNAME);
        $client->takeScreenshot('2-loginUsernameFilled.png');
        $crawler->filter("input#idSIButton9")->click();

        $client->waitForVisibility("input[name='passwd']");
        $client->takeScreenshot('3-loginUsernameDone.png');

        $crawler = $client->getCrawler();
        $crawler->filter("input[name='passwd']")->sendKeys(self::PASSWORD );
        $crawler->filter("input#idSIButton9")->click();

        $client->waitForVisibility("input#idBtn_Back");
        $client->takeScreenshot('4-loginPassword.png');
        $crawler = $client->getCrawler();
        $crawler->filter("input#idBtn_Back")->click();

        $client->takeScreenshot('loginNo.png');
        $client->waitForVisibility("mart-figures-card h4");
        $client->takeScreenshot('5-logged.png');

        $crawler = $client->getCrawler();
        $xMart = $crawler->filter(".p-col-12.p-md-6.xmart-page-titles > div > p:nth-of-type(1)")->getText();
        $this->assertEquals('xMart is a self-service platform for data managers.', $xMart);
    }
}