<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;
use PHPUnit_Framework_Assert as PHPUnit;


class FollowingPage extends BasePage
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/following';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url());
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@feed'           => '#feed',
            '@post_container' => '.timeline__column > .timeline__box > .box',
        ];
    }

    public function assertCountItems(Browser $browser, $expected, $element)
    {
        PHPUnit::assertEquals($expected, count($browser->elements($element)));
    }

    public function assertEquals(Browser $browser, $expected, $haystack)
    {
        PHPUnit::assertEquals($expected, $haystack);
    }
}
