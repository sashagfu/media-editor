<?php

use App\Models\Circle;

class CirclePostCest
{
    const FORM_ADD_POST_SELECTOR = 'form.feed_add__form';
    const FORM_ADD_IMAGES_SELECTOR = 'a.create-post__images-btn';
    const POST_SHARE_IMAGES_INPUT = 'input.create-post__images';
    const FORM_ADD_VIDEO_SELECTOR = 'a.create-post__video-btn';
    const POST_CONTENT_ERROR_TEXT = 'The post content field is required.';
    const VIDEO_ERROR_TEXT = 'The video id field is required when none of images are present.';
    const VIDEOS_MODAL_SELECTOR = '#videosModal';
    const MEDIA_TAB_ITEM_SELECTOR = 'div.media-tab__item';
    const MEDIA_ITEM_BUTTON_SELECTOR = 'a.media-tab__select-btn';
    const POST_SHARE_BUTTON_SELECTOR = 'button.post-create__button';
    const PROFILE_POST_URL_SELECTOR = 'a.post-container__single-url';
    const CLOSE_BUTTON_SELECTOR = 'button.close-button';
    const POST_CONTENT_SELECTOR = 'div.create-post__content.contenteditable';
    const POST_CONTAINER_AUTHOR_SELECTOR = 'a.post-container__author';
    const CIRCLE_HEADER_MEMBERSHIP_SELECTOR = 'a.circle-header__membership';
    const FEED_SELECTOR = 'div.feed';
    const CIRCLE_MENU_REQUESTS_SELECTOR = '.circle-menu__requests';
    const CIRCLE_REQUEST_CONTAINER_LINK_SELECTOR = 'a.request-container__user-link';
    const CIRCLE_REQUEST_APPROVE_SELECTOR = 'a.request-approve';
    const CIRCLE_REQUEST_CANCEL_SELECTOR = 'a.request-cancel';
    const CIRCLE_USER_WRAPPER_SELECTOR = 'div.circle-members__user-wrapper';
    const PROGRESS_BAR_SELECTOR = '.default-progress-bar';


    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function testIfUserCanAddPost(AcceptanceTester $I)
    {
        // Declare some stuff
        $title = 'Testing adding posts';
        $description = 'Lorem ipsum dot alore est';
        $admin = User::find(1);
        $another_user_id = 3;

        // Create open circle and see post adding form
        $circle_slug = $I->createCircle(1, 'password!', $title, $description);
        $I->amOnPage('/circles/'.$circle_slug);
        $I->scrollTo(self::FORM_ADD_POST_SELECTOR);
        $I->seeElementInDOM(self::FORM_ADD_POST_SELECTOR);
        $I->seeElementInDOM(self::FORM_ADD_IMAGES_SELECTOR);
        $I->seeElementInDOM(self::FORM_ADD_VIDEO_SELECTOR);
        $I->seeElementInDOM(self::POST_SHARE_BUTTON_SELECTOR);

        // Let's check the validator
        $I->click(self::POST_SHARE_BUTTON_SELECTOR);
        $I->see(self::POST_CONTENT_ERROR_TEXT);
        $I->see(self::VIDEO_ERROR_TEXT);

        // Select video and add content
        $content = 'I like adding content to my post!';
        $I->fillField(self::POST_CONTENT_SELECTOR, $content);
        $I->click(self::FORM_ADD_VIDEO_SELECTOR);
        $I->waitForElementVisible(self::VIDEOS_MODAL_SELECTOR, 5);
        $I->moveMouseOver(self::MEDIA_TAB_ITEM_SELECTOR.':first-of-type');
        $I->waitForElementVisible(self::MEDIA_ITEM_BUTTON_SELECTOR.':first-of-type');
        $I->click(self::MEDIA_ITEM_BUTTON_SELECTOR.':first-of-type');
        $I->click(self::CLOSE_BUTTON_SELECTOR);
        $I->scrollTo(self::POST_SHARE_BUTTON_SELECTOR);
        $I->click(self::POST_SHARE_BUTTON_SELECTOR);
        $I->waitForElement(self::PROGRESS_BAR_SELECTOR);

        $I->wait(5);

        $I->amOnPage('/circles/'.$circle_slug);
        $I->see($content);
        $I->see($admin->username, self::POST_CONTAINER_AUTHOR_SELECTOR.':first-of-type');
        
        // check if another can see it
        $another = $I->haveFriend('another');
        $another->does(function (AcceptanceTester $I) use ($another_user_id, $circle_slug, $content, $admin) {
            $I->loginAs($another_user_id, 'password!');
            $I->amOnPage('/circles/'.$circle_slug);
            $I->see($content);
            $I->see($admin->username, self::POST_CONTAINER_AUTHOR_SELECTOR.':first-of-type');
        });
    }

    public function testIfUserCanAttachImageToPost(AcceptanceTester $I)
    {
        $title = 'Testing adding posts';
        $description = 'Lorem ipsum dot alore est';
        $admin = User::find(1);
        $another_user_id = 3;

        // Create open circle and see post adding form
        $circle_slug = $I->createCircle(1, 'password!', $title, $description);
        $I->amOnPage('/circles/'.$circle_slug);
        $I->scrollTo(self::FORM_ADD_POST_SELECTOR);
        $content = 'I like adding content to my post!';
        $I->fillField(self::POST_CONTENT_SELECTOR, $content);
        $I->attachFile(self::POST_SHARE_IMAGES_INPUT, 'default-cover.png');
        $I->scrollTo(self::POST_SHARE_BUTTON_SELECTOR);
        $I->click(self::POST_SHARE_BUTTON_SELECTOR);
        $I->waitForElement(self::PROGRESS_BAR_SELECTOR);
        $I->dontSee(self::POST_CONTENT_ERROR_TEXT);
        $I->dontSee(self::VIDEO_ERROR_TEXT);

        $I->wait(5);

        $I->amOnPage('/circles/'.$circle_slug);
        $I->see($content);
        $I->see($admin->username, self::POST_CONTAINER_AUTHOR_SELECTOR.':first-of-type');

        // check if another can see it
        $another = $I->haveFriend('another');
        $another->does(function (AcceptanceTester $I) use ($another_user_id, $circle_slug, $content, $admin) {
            $I->loginAs($another_user_id, 'password!');
            $I->amOnPage('/circles/'.$circle_slug);
            $I->see($content);
            $I->see($admin->username, self::POST_CONTAINER_AUTHOR_SELECTOR.':first-of-type');
        });
    }

    public function testIfUserCanAddPostInClosedCircle(AcceptanceTester $I)
    {
        // Declare some stuff
        $title = 'Testing adding posts in closed circles';
        $description = 'Lorem ipsum dot alore est';
        $admin = User::find(1);
        $another_user_id = 3;
        $another_user = User::find($another_user_id);

        // Create open circle and see post adding form
        $circle_slug = $I->createCircle(1, 'password!', $title, $description);
        $I->updateCircleSettings($circle_slug, ['type' => Circle::TYPE_CLOSED]);
        $I->amOnPage('/circles/'.$circle_slug);

        // Select video and add content
        $content = 'I like adding content to my post!';
        $I->fillField(self::POST_CONTENT_SELECTOR, $content);
        $I->click(self::FORM_ADD_VIDEO_SELECTOR);
        $I->waitForElementVisible(self::VIDEOS_MODAL_SELECTOR, 5);
        $I->moveMouseOver(self::MEDIA_TAB_ITEM_SELECTOR.':first-of-type');
        $I->waitForElementVisible(self::MEDIA_ITEM_BUTTON_SELECTOR.':first-of-type');
        $I->click(self::MEDIA_ITEM_BUTTON_SELECTOR.':first-of-type');
        $I->click(self::CLOSE_BUTTON_SELECTOR);
        $I->scrollTo(self::POST_SHARE_BUTTON_SELECTOR);
        $I->click(self::POST_SHARE_BUTTON_SELECTOR);
        $I->waitForElement(self::PROGRESS_BAR_SELECTOR);

        $I->wait(5);

        $I->amOnPage('/circles/'.$circle_slug);
        $I->see($content);
        $I->see($admin->username, self::POST_CONTAINER_AUTHOR_SELECTOR.':first-of-type');

        // check if another can see it
        $another = $I->haveFriend('another');
        $another->does(function (AcceptanceTester $I) use (
            $another_user,
            $another_user_id,
            $circle_slug,
            $content,
            $admin
        ) {
            $I->loginAs($another_user_id, 'password!');
            $I->amOnPage('/circles/'.$circle_slug);
            $I->dontSee($content);
            $I->dontSee($admin->username, self::POST_CONTAINER_AUTHOR_SELECTOR.':first-of-type');
            $I->seeElement(self::CIRCLE_HEADER_MEMBERSHIP_SELECTOR);
            $I->click(self::CIRCLE_HEADER_MEMBERSHIP_SELECTOR);
            $I->amOnPage('/circles/'.$circle_slug);
            $I->dontSee($another_user->display_name);
            $I->dontSeeElement(self::FEED_SELECTOR);
        });

        $I->amOnPage('/circles/'.$circle_slug);
        $I->scrollTo(self::CIRCLE_MENU_REQUESTS_SELECTOR);
        $I->click(self::CIRCLE_MENU_REQUESTS_SELECTOR);
        $I->see($another_user->display_name, self::CIRCLE_REQUEST_CONTAINER_LINK_SELECTOR);
        $I->click(self::CIRCLE_REQUEST_APPROVE_SELECTOR);
        $I->amOnPage('/circles/'.$circle_slug);
        $I->scrollTo(self::CIRCLE_USER_WRAPPER_SELECTOR);
        $I->see($another_user->display_name);

        $another->does(function (AcceptanceTester $I) use ($another_user_id, $circle_slug, $another_user) {
            $I->amOnPage('/circles/'.$circle_slug);
            $I->see(trans('circles.leave'), self::CIRCLE_HEADER_MEMBERSHIP_SELECTOR);
            $I->scrollTo(self::CIRCLE_USER_WRAPPER_SELECTOR);
            $I->see($another_user->display_name);
            $I->seeElement(self::FEED_SELECTOR);
        });
    }
}
