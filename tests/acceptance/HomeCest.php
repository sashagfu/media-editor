<?php

/**
 * Class HomeCest
 *
 * @SuppressWarnings(PHPMD)
 * @codingStandardsIgnoreStart
 */
class HomeCest
{
    const VIDEO_SELECTOR = 'div.video-container > iframe';
    CONST HEADER_MENU_FOLLOWING = 'Following';
    CONST HEADER_MENU_PERFORMANCES = 'Performances';
    CONST HEADER_MENU_LOGIN = 'Login';
    CONST HEADER_MENU_REGISTER = 'Register';
    CONST SITE_NAME = 'Yuustar';

    public function _before(AcceptanceTester $I)
    {
//        TODO: Remove if needed (don't see the necessity)
//        $I->refreshMigration();
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function testIfUnregisteredUserCanSeeLinksAndVideo(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->seeInTitle(self::SITE_NAME);
        $I->seeLink(self::HEADER_MENU_FOLLOWING);
        $I->seeLink(self::HEADER_MENU_PERFORMANCES);
        $I->seeLink(self::HEADER_MENU_LOGIN);
        $I->seeLink(self::HEADER_MENU_REGISTER);
        $I->canSeeElement(self::VIDEO_SELECTOR);
    }
}
