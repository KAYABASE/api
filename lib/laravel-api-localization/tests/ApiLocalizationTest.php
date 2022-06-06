<?php

namespace Fabrikod\ApiLocalization\Tests;

use Fabrikod\ApiLocalization\Exceptions\NotAvailableLocaleException;

class ApiLocalizationTest extends TestCase
{
    public function testSetLocale()
    {
        $this->disableMiddleware();

        $this->getJson('/api')->assertSee("Hello world");

        $this->refreshApplication('tr');

        $this->assertEquals('tr', $this->apiLocalization->setLocale('tr'));
        $this->assertEquals('tr', $this->apiLocalization->currentLocale());
        $this->assertEquals('tr_TR.UTF-8', setlocale(LC_TIME, 0));

        $this->getJson('/api')->assertSee("Merhaba Dünya");

        $this->refreshApplication('en');

        $this->assertEquals('en', $this->apiLocalization->setLocale('en'));
        $this->assertEquals('en', $this->apiLocalization->currentLocale());
        $this->assertEquals('en_GB.UTF-8', setlocale(LC_TIME, 0));

        $this->getJson('/api')->assertSee("Hello world");

        $this->expectException(NotAvailableLocaleException::class);

        $this->apiLocalization->setLocale('de');
    }

    public function testHeaderLocalization()
    {
        $this->withoutExceptionHandling();

        $this->getJson('/api')->assertSee("Hello world");

        $this->getJson('/api', ['Accept-Language' => 'tr'])->assertSee("Merhaba Dünya");

        $this->getJson('/api', ['Accept-Language' => 'en'])->assertSee("Hello world");

        $this->refreshApplication('en');

        $this->getJson('/api', ['Accept-Language' => 'tr_TR'])->assertSee("Merhaba Dünya");
        $this->getJson('/api', ['Accept-Language' => 'tr-tr'])->assertSee("Merhaba Dünya");
        $this->getJson('/api', ['Accept-Language' => 'tr-TR'])->assertSee("Merhaba Dünya");

        $this->assertEquals('tr', app()->getLocale());

        $this->getJson('/api', ['Accept-Language' => 'en_GB'])->assertSee("Hello world");
        $this->getJson('/api', ['Accept-Language' => 'en-en'])->assertSee("Hello world");
        $this->getJson('/api', ['Accept-Language' => 'en-GB'])->assertSee("Hello world");

        $this->assertEquals('en', app()->getLocale());

        // With customize header name testing.
        $this->refreshApplication('en');

        $this->withoutExceptionHandling();

        config(['api-localization.header_name' => 'X-Language']);

        $this->getJson('/api', ['X-Language' => 'tr'])->assertSee("Merhaba Dünya");
        $this->getJson('/api', ['X-Language' => 'tr_TR'])->assertSee("Merhaba Dünya");

        $this->assertEquals('tr', app()->getLocale());

        $this->getJson('/api', ['X-Language' => 'en'])->assertSee("Hello world");
        $this->getJson('/api', ['X-Language' => 'en_GB'])->assertSee("Hello world");

        $this->assertEquals('en', app()->getLocale());
    }

    /** @test */
    public function testExceptionToAvailableLocaleInMiddleware()
    {
        $this->withoutExceptionHandling();

        config(['api-localization.enable_middleware_exception' => false]);

        // Default locale is en.
        $this->getJson('/api', ['Accept-Language' => 'de'])->assertSuccessful()->assertSee("Hello world");

        config(['api-localization.enable_middleware_exception' => true]);

        $this->expectException(NotAvailableLocaleException::class);

        $this->getJson('/api', ['Accept-Language' => 'de']);
    }

    public function testIgnoredUrls()
    {
        config(['api-localization.ignoredUrls' => ['/api/skipped']]);

        $this->getJson('/api/skipped')->assertSuccessful()->assertSee("Hello world");

        $this->getJson('/api/skipped', ['Accept-Language' => 'tr'])->assertSuccessful()->assertSee("Hello world");
    }

    public function testLocaleMaps()
    {
        config(['api-localization.localeMaps' => [
            'en-EN' => 'en',
            'tr-TR' => 'tr',
            'de-DE' => 'tr',
        ]]);

        $this->getJson('/api', ['Accept-Language' => 'de-DE']);

        $this->assertEquals('tr', app()->currentLocale());

    }
}
