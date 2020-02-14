<?php

use App\Models\Circle;

class CircleMembershipCest
{
    const CIRCLE_HEADER_MEMBERSHIP_SELECTOR = 'a.circle-header__membership';
    const CIRCLE_USER_WRAPPER_SELECTOR = 'div.circle-members__user-wrapper';
    const FEED_SELECTOR = 'div.feed';
    const CIRCLE_MENU_REQUESTS_SELECTOR = '.circle-menu__requests';
    const CIRCLE_REQUEST_CONTAINER_LINK_SELECTOR = 'a.request-container__user-link';
    const CIRCLE_REQUEST_SELF_CANCEL_SELECTOR = 'a.cancel-self-request';
    const CIRCLE_REQUEST_APPROVE_SELECTOR = 'a.request-approve';
    const CIRCLE_REQUEST_CANCEL_SELECTOR = 'a.request-cancel';
    const CIRCLE_POST_ADD_FORM = 'form.feed_add__form';
    const PRIVACY_LABEL_SETTINGS = 'a#privacy-label';
    const CIRCLE_SUBMIT_SETTINGS = 'input.circle-settings__submit';

    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function testIfSecretCircleIsSeenOnlyToMembers(AcceptanceTester $I)
    {
        // Declare stuff
        $title = 'Some dummy title';
        $description = 'some dummy description';
        $another_user_id = 3;
        $another_user = User::find($another_user_id);

        // Create Circle and ensure visibility for creator only
        $circle_slug = $I->createCircle(1, 'password!', $title, $description);
        $I->updateCircleSettings($circle_slug, ['type' => Circle::TYPE_SECRET]);
        $I->amOnPage('/circles');
        $I->see($title);
        $I->see($description);

        // Check if another cannot see it
        $another = $I->haveFriend('another');
        $another->does(function (AcceptanceTester $I) use ($another_user_id, $title, $description) {
            $I->loginAs($another_user_id, 'password!');
            $I->amOnPage('/circles');
            $I->dontSee($title);
            $I->dontSee($description);
        });

        // Update Back to Open type
        $I->updateCircleSettings($circle_slug, ['type' => Circle::TYPE_OPEN]);

        // Another user join
        $another->does(function (AcceptanceTester $I) use ($another_user_id, $circle_slug, $another_user) {
            $I->amOnPage('/circles/'.$circle_slug);
            $I->seeElement(self::CIRCLE_HEADER_MEMBERSHIP_SELECTOR);
            $I->click(self::CIRCLE_HEADER_MEMBERSHIP_SELECTOR);
            $I->amOnPage('/circles/'.$circle_slug);
            $I->scrollTo(self::CIRCLE_USER_WRAPPER_SELECTOR);
            $I->see($another_user->display_name);
        });

        // Update Back to Secret type
        $I->updateCircleSettings($circle_slug, ['type' => Circle::TYPE_SECRET]);

        $another->does(function (AcceptanceTester $I) use ($another_user_id, $title, $description) {
            $I->amOnPage('/circles');
            $I->See($title);
            $I->See($description);
        });
    }

    public function testIfUserCanJoinOpenCircle(AcceptanceTester $I)
    {
        // Declare stuff
        $title = 'Test for use join';
        $description = 'test for user join description';
        $another_user_id = 3;
        $another_user = User::find($another_user_id);
        $circle_slug = $I->createCircle(1, 'password!', $title, $description);

        $another = $I->haveFriend('another');
        $another->does(function (AcceptanceTester $I) use ($another_user_id, $circle_slug, $another_user) {
            $I->loginAs($another_user_id, 'password!');
            $I->amOnPage('/circles/'.$circle_slug);
            $I->seeElement(self::CIRCLE_HEADER_MEMBERSHIP_SELECTOR);
            $I->click(self::CIRCLE_HEADER_MEMBERSHIP_SELECTOR);
            $I->amOnPage('/circles/'.$circle_slug);
            $I->scrollTo(self::CIRCLE_USER_WRAPPER_SELECTOR);
            $I->see($another_user->display_name);
        });
    }

    public function testIfUserCanLeaveCircle(AcceptanceTester $I)
    {
        // Declare stuff
        $title = 'Test for user leave';
        $description = 'test for user leave description';
        $another_user_id = 3;
        $another_user = User::find($another_user_id);
        $circle_slug = $I->createCircle(1, 'password!', $title, $description);

        $another = $I->haveFriend('another');
        $another->does(function (AcceptanceTester $I) use ($another_user_id, $circle_slug, $another_user) {
            $I->loginAs($another_user_id, 'password!');
            $I->amOnPage('/circles/'.$circle_slug);
            $I->seeElement(self::CIRCLE_HEADER_MEMBERSHIP_SELECTOR);
            $I->click(self::CIRCLE_HEADER_MEMBERSHIP_SELECTOR);
            $I->amOnPage('/circles/'.$circle_slug);
            $I->scrollTo(self::CIRCLE_USER_WRAPPER_SELECTOR);
            $I->see($another_user->display_name);
            $I->scrollTo(self::CIRCLE_HEADER_MEMBERSHIP_SELECTOR);
            $I->click(self::CIRCLE_HEADER_MEMBERSHIP_SELECTOR);
            $I->amOnPage('/circles/'.$circle_slug);
            $I->scrollTo(self::CIRCLE_USER_WRAPPER_SELECTOR);
            $I->dontSee($another_user->display_name);
        });
    }

    public function testIfUserCanJoinClosedCircle(AcceptanceTester $I)
    {
        // Declare stuff
        $title = 'Test for user request';
        $description = 'test for user request description';
        $another_user_id = 3;
        $another_user = User::find($another_user_id);
        $circle_slug = $I->createCircle(1, 'password!', $title, $description);
        $I->updateCircleSettings($circle_slug, ['type' => Circle::TYPE_CLOSED]);

        $another = $I->haveFriend('another');
        $another->does(function (AcceptanceTester $I) use ($another_user_id, $circle_slug, $another_user) {
            $I->loginAs($another_user_id, 'password!');
            $I->amOnPage('/circles/'.$circle_slug);
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

    public function testIfUserCanCancleHisRequestImmediately(AcceptanceTester $I)
    {
        $title = 'Test for user request cancel';
        $description = 'test for user request cancel description';
        $another_user_id = 3;
        $another_user = User::find($another_user_id);
        $circle_slug = $I->createCircle(1, 'password!', $title, $description);
        $I->updateCircleSettings($circle_slug, ['type' => Circle::TYPE_CLOSED]);

        $another = $I->haveFriend('another');
        $another->does(function (AcceptanceTester $I) use ($another_user_id, $circle_slug, $another_user) {
            $I->loginAs($another_user_id, 'password!');
            $I->amOnPage('/circles/'.$circle_slug);
            $I->seeElement(self::CIRCLE_HEADER_MEMBERSHIP_SELECTOR);
            $I->click(self::CIRCLE_HEADER_MEMBERSHIP_SELECTOR);
            $I->wait(2);
            $I->dontSee($another_user->display_name);
            $I->dontSeeElement(self::FEED_SELECTOR);
            $I->seeElementInDOM(self::CIRCLE_REQUEST_SELF_CANCEL_SELECTOR);
            $I->click(self::CIRCLE_REQUEST_SELF_CANCEL_SELECTOR);
            $I->wait(5);
            $I->seeElement(self::CIRCLE_HEADER_MEMBERSHIP_SELECTOR.'.request');
        });
    }

    public function testIfUserCanCancelHisRequestAfterReload(AcceptanceTester $I)
    {
        $title = 'Test for user request cancel';
        $description = 'test for user request cancel description';
        $another_user_id = 3;
        $another_user = User::find($another_user_id);
        $circle_slug = $I->createCircle(1, 'password!', $title, $description);
        $I->updateCircleSettings($circle_slug, ['type' => Circle::TYPE_CLOSED]);

        $another = $I->haveFriend('another');
        $another->does(function (AcceptanceTester $I) use ($another_user_id, $circle_slug, $another_user) {
            $I->loginAs($another_user_id, 'password!');
            $I->amOnPage('/circles/'.$circle_slug);
            $I->seeElement(self::CIRCLE_HEADER_MEMBERSHIP_SELECTOR);
            $I->click(self::CIRCLE_HEADER_MEMBERSHIP_SELECTOR);
            $I->wait(2);
            $I->dontSee($another_user->display_name);
            $I->dontSeeElement(self::FEED_SELECTOR);
            $I->amOnPage('/circles/'.$circle_slug);
            $I->seeElementInDOM(self::CIRCLE_REQUEST_SELF_CANCEL_SELECTOR);
            $I->click(self::CIRCLE_REQUEST_SELF_CANCEL_SELECTOR);
            $I->wait(5);
            $I->seeElement(self::CIRCLE_HEADER_MEMBERSHIP_SELECTOR.'.request');
        });
    }

    public function testIfUserCanCancelRequest(AcceptanceTester $I)
    {
        // Declare stuff
        $title = 'Test for user request cancel';
        $description = 'test for user request cancel description';
        $another_user_id = 3;
        $another_user = User::find($another_user_id);
        $circle_slug = $I->createCircle(1, 'password!', $title, $description);
        $I->updateCircleSettings($circle_slug, ['type' => Circle::TYPE_CLOSED]);

        $another = $I->haveFriend('another');
        $another->does(function (AcceptanceTester $I) use ($another_user_id, $circle_slug, $another_user) {
            $I->loginAs($another_user_id, 'password!');
            $I->amOnPage('/circles/'.$circle_slug);
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
        $I->click(self::CIRCLE_REQUEST_CANCEL_SELECTOR);
        $I->amOnPage('/circles/'.$circle_slug);
        $I->scrollTo(self::CIRCLE_USER_WRAPPER_SELECTOR);
        $I->dontSee($another_user->display_name);

        $another->does(function (AcceptanceTester $I) use ($another_user_id, $circle_slug, $another_user) {
            $I->amOnPage('/circles/'.$circle_slug);
            $I->see(trans('circles.join'), self::CIRCLE_HEADER_MEMBERSHIP_SELECTOR);
            $I->scrollTo(self::CIRCLE_USER_WRAPPER_SELECTOR);
            $I->dontSee($another_user->display_name);
            $I->dontSeeElement(self::FEED_SELECTOR);
        });
    }

    public function testIfNotMemberCanNotAccessSecretCircle(AcceptanceTester $I)
    {
        $title = 'Test for user secret visibility';
        $description = 'test for user secret visibility description';
        $another_user_id = 3;
        $another_user = User::find($another_user_id);
        $circle_slug = $I->createCircle(1, 'password!', $title, $description);
        $I->updateCircleSettings($circle_slug, ['type' => Circle::TYPE_SECRET]);

        $another = $I->haveFriend('another');
        $another->does(function (AcceptanceTester $I) use ($another_user_id, $circle_slug, $another_user) {
            $I->loginAs($another_user_id, 'password!');
            $I->amOnPage('/circles/'.$circle_slug);
            $I->seeCurrentUrlEquals('/circles');
        });
    }

    public function testIfNotMemberCanSeeClosedTimeline(AcceptanceTester $I)
    {
        $title = 'Test for user closed timeline visibility';
        $description = 'test for user closed timeline visibility description';
        $another_user_id = 3;
        $another_user = User::find($another_user_id);
        $circle_slug = $I->createCircle(1, 'password!', $title, $description);
        $I->updateCircleSettings($circle_slug, ['type' => Circle::TYPE_CLOSED]);

        $another = $I->haveFriend('another');
        $another->does(function (AcceptanceTester $I) use ($another_user_id, $circle_slug, $another_user) {
            $I->loginAs($another_user_id, 'password!');
            $I->amOnPage('/circles/'.$circle_slug);
            $I->dontSeeElement(self::FEED_SELECTOR);
        });
    }

    public function testIfPostAddingPrivacySettingsWorks(AcceptanceTester $I)
    {
        $title = 'Test for user adding post privacy';
        $description = 'test for user adding post privacy description';
        $another_user_id = 3;
        $another_user = User::find($another_user_id);
        $circle_slug = $I->createCircle(1, 'password!', $title, $description);
        $I->seeCurrentUrlEquals('/circles/'.$circle_slug);
        $I->scrollTo(self::FEED_SELECTOR);
        $I->seeElement(self::CIRCLE_POST_ADD_FORM);


        $another = $I->haveFriend('another');
        $another->does(function (AcceptanceTester $I) use ($another_user_id, $circle_slug, $another_user) {
            $I->loginAs($another_user_id, 'password!');
            $I->amOnPage('/circles/'.$circle_slug);
            $I->seeElement(self::CIRCLE_HEADER_MEMBERSHIP_SELECTOR);
            $I->click(self::CIRCLE_HEADER_MEMBERSHIP_SELECTOR);
            $I->amOnPage('/circles/'.$circle_slug);
            $I->scrollTo(self::CIRCLE_USER_WRAPPER_SELECTOR);
            $I->see($another_user->display_name);
            $I->scrollTo(self::FEED_SELECTOR);
            $I->dontSeeElement(self::CIRCLE_POST_ADD_FORM);
        });

        // Update settings and check creator sees it
        $I->amOnPage('/circles/'.$circle_slug.'/settings');
        $I->click(self::PRIVACY_LABEL_SETTINGS);
        $I->click('input#circle-settings__post-members');
        $I->scrollTo(self::CIRCLE_SUBMIT_SETTINGS);
        $I->click(self::CIRCLE_SUBMIT_SETTINGS);
        $I->amOnPage('/circles/'.$circle_slug);
        $I->scrollTo(self::FEED_SELECTOR);
        $I->seeElement(self::CIRCLE_POST_ADD_FORM);

        // Check if another user see it
        $another->does(function (AcceptanceTester $I) use ($another_user_id, $circle_slug, $another_user) {
            $I->amOnPage('/circles/'.$circle_slug);
            $I->scrollTo(self::FEED_SELECTOR);
            $I->seeElement(self::CIRCLE_POST_ADD_FORM);
        });
    }

    public function testIfMembersBlockPrivacySettingsWorks(AcceptanceTester $I)
    {
        $title = 'Test for user members block privacy';
        $description = 'test for user adding members block description';
        $another_user_id = 3;
        $another_user = User::find($another_user_id);
        $circle_slug = $I->createCircle(1, 'password!', $title, $description);

        $another = $I->haveFriend('another');
        $another->does(function (AcceptanceTester $I) use ($another_user_id, $circle_slug, $another_user) {
            $I->loginAs($another_user_id, 'password!');
            $I->amOnPage('/circles/'.$circle_slug);
            $I->seeElement(self::CIRCLE_USER_WRAPPER_SELECTOR);
        });

        // Update settings and check creator sees it
        $I->amOnPage('/circles/'.$circle_slug.'/settings');
        $I->click(self::PRIVACY_LABEL_SETTINGS);
        $I->click('input#circle-settings__members-block-all-members');
        $I->scrollTo(self::CIRCLE_SUBMIT_SETTINGS);
        $I->click(self::CIRCLE_SUBMIT_SETTINGS);

        // Check if another user do not see it
        $another->does(function (AcceptanceTester $I) use ($another_user_id, $circle_slug, $another_user) {
            $I->amOnPage('/circles/'.$circle_slug);
            $I->amOnPage('/circles/'.$circle_slug);
            $I->dontSeeElement(self::CIRCLE_USER_WRAPPER_SELECTOR);
        });
    }

}
