<?php

namespace App\Tests\Controller;

use Symfony\Component\Panther\PantherTestCase;

class ConferencesControllerTest extends PantherTestCase
{
    /** @test */
    public function index(): void
    {
        $client = static::createPantherClient(['external_base_uri' => $_SERVER['SYMFONY_PROJECT_DEFAULT_ROUTE_URL']]);
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h2', 'Give your feedback!');
    }

    /** @test */
    public function it_test_comment_submission(): void
    {
        $client = static::createClient();
        $client->request('GET', '/conferences/amsterdam-2019');
        $client->submitForm('Submit', [
            'comment_form[author]' => 'Fabien',
            'comment_form[text]' => 'Some feedback from an automated functional test',
            'comment_form[email]' => 'me@automat.ed',
            'comment_form[photo]' => dirname(__DIR__, 2).'/public/images/test.jpg',
        ]);
        self::assertResponseRedirects();
        $client->followRedirect();
        self::assertSelectorExists('div:contains("There are 2 comments")');
    }

    public function testConferencePage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        self::assertCount(2, $crawler->filter('h4'));

        $client->clickLink('View');

        self::assertPageTitleContains('Amsterdam');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h2', 'Amsterdam 2019');
        self::assertSelectorExists('div:contains("There are 1 comments")');
    }
}
