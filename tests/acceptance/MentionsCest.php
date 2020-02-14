<?php

/**
 * Class MentionsCest
 *
 * @SuppressWarnings(PHPMD)
 * @codingStandardsIgnoreStart
 */
class MentionsCest
{
    const MENU_CLASS = '.mentions-autocomplete';
    const POST_CONTENT_SELECTOR = 'div.create-post__content.contenteditable';
    const CREATE_POST_VIDEO_OPEN_BTN = 'a.create-post__video-btn';
    const VIDEOS_MODAL_SELECTOR = '#videosModal';
    const MEDIA_TAB_ITEM_SELECTOR = 'div.media-tab__item';
    const MEDIA_ITEM_BUTTON_SELECTOR = 'a.media-tab__select-btn';
    const POST_SHARE_BUTTON_SELECTOR = 'button.post-create__button';
    const PROFILE_POST_URL_SELECTOR  = 'a.post-container__single-url';
    const USER_MENTION_SELECTOR  = 'a.user-mention';
    const CLOSE_BUTTON_SELECTOR = 'button.close-button';

    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function testIfPostWithMentionWillBeCreated(AcceptanceTester $I)
    {
        // Login as admin and go to post creation post
        $I->adminLogin();
        $I->amOnPage('/post/create');

        // Fill fields
        $I->fillField(self::POST_CONTENT_SELECTOR, 'My dummy description @adm');

        $I->wait(3);
        // Check if automplete menu has been opened
        $I->seeElement('ul'.self::MENU_CLASS);
        // Click to select
        $I->click('ul'.self::MENU_CLASS.'>li:first-of-type');
        // Don't see menu
        $I->dontSeeElement('ul'.self::MENU_CLASS);
        // See text equals to normal one
        $text = $I->grabTextFrom(self::POST_CONTENT_SELECTOR);
        $I->see($text, self::POST_CONTENT_SELECTOR);

        // Select video and create post
        $I->click(self::CREATE_POST_VIDEO_OPEN_BTN);
        $I->waitForElementVisible(self::VIDEOS_MODAL_SELECTOR, 5);
        $I->moveMouseOver(self::MEDIA_TAB_ITEM_SELECTOR.':first-of-type');
        $I->waitForElementVisible(self::MEDIA_ITEM_BUTTON_SELECTOR.':first-of-type');
        $I->click(self::MEDIA_ITEM_BUTTON_SELECTOR.':first-of-type');
        $I->click(self::CLOSE_BUTTON_SELECTOR);
        $I->scrollTo(self::POST_SHARE_BUTTON_SELECTOR);
        $I->click(self::POST_SHARE_BUTTON_SELECTOR);

        $I->wait(8);

        $I->seeCurrentUrlEquals('/profile');
        $I->see(trans('posts.created'));
    }

    public function testIfPostDescriptionWillBeMadeProperly(AcceptanceTester $I)
    {
        // Login as admin go to profile page
        $I->adminLogin();
        $I->amOnPage('/profile');
        $admin = User::find(1);

        // Select the last post we created
        $I->scrollTo(self::PROFILE_POST_URL_SELECTOR);
        $I->click(self::PROFILE_POST_URL_SELECTOR);

        $I->wait(2);

        // Go to post
        $I->seeCurrentUrlMatches('/post/');

        // See mention
        $I->see($admin->display_name, self::USER_MENTION_SELECTOR);

        // Click on it and go to profile page
        $I->click(self::USER_MENTION_SELECTOR.':first-of-type');
        $I->seeCurrentUrlEquals('/profile');
    }
}
