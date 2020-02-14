<?php

use App\Models\Circle;
use App\Models\Invite;

class CircleInvitesCest
{
    const CIRCLE_USER_WRAPPER_SELECTOR = 'div.circle-members__user-wrapper';
    const MEMBERS_INVITE_INPUT_SELECTOR  = '.invites-container__search';
    const AUTOCOMPLETE_INVITE_SELECTOR = 'ul.circles-invite__ul-autocomplete';
    const AC_RESULT_SELECTOR = '.ui-menu-item';
    const SEND_INVITES_BTN_SELECTOR = '.invites-container__send-invites';
    const NOTIFICATION_COUNT_SELECTOR = 'span.notifications-count';
    const NOTIFICATION_TOGGLER_SELECTOR = 'a.notifications-toggler';
    const INVITE_CANCEL_SELECTOR = 'a.invite-cancel';
    const INVITE_ACCEPT_SELECTOR = 'a.invite-accept';

    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function testIfAdminOfGroupCanSeeInviteFields(AcceptanceTester $I)
    {
        // Declare stuff
        $admin_id = 1;
        $another_user_id = 3;
        $title = 'Test for members invite fields';
        $description = 'Bacon ipsum dolor sit amet nulla ham qui sint exercitation eiusmod';
        $circle_slug = $I->createCircle($admin_id, 'password!', $title, $description);

        // Check if another user cannot see members invites input
        $another = $I->haveFriend('another');
        $another->does(function (AcceptanceTester $I) use ($circle_slug,$another_user_id) {
            $I->loginAs($another_user_id, 'password!');

            // Now go to created circle
            $I->amOnPage('/circles/'.$circle_slug.'/members');
            $I->dontseeElement(self::MEMBERS_INVITE_INPUT_SELECTOR);
        });

        $I->amOnPage('/circles/'.$circle_slug.'/members');
        $I->seeElement(self::MEMBERS_INVITE_INPUT_SELECTOR);
    }

    public function testIfAdminCanInvitedExistedUser(AcceptanceTester $I)
    {
        $title = 'Test for members invite existing user';
        $description = 'Bacon ipsum dolor sit amet nulla ham qui sint exercitation eiusmod';
        $admin_id = 1;
        $admin = User::find($admin_id);
        $another_user_id = 3;
        $another_user = User::find($another_user_id);
        $unread_notifications = $another_user->unreadNotifications->count();
        $notification_text = trans('notifications.circle_invite_notification', [
                'name'         => $admin->username,
                'circle_title' => $title,
            ]
        );
        $circle_slug = $I->createCircle($admin_id, 'password!', $title, $description);

        // Go to members invite page
        $I->amOnPage('/circles/'.$circle_slug.'/members');
        $I->seeElement(self::MEMBERS_INVITE_INPUT_SELECTOR);

        // Fill autocomplete and select user needed
        $I->fillField(self::MEMBERS_INVITE_INPUT_SELECTOR, $another_user->display_name);
        $I->wait(3);
        $I->click(self::AUTOCOMPLETE_INVITE_SELECTOR.' '.self::AC_RESULT_SELECTOR);
        $I->seeInField(self::MEMBERS_INVITE_INPUT_SELECTOR, $another_user->email.', ');
        $I->click(self::SEND_INVITES_BTN_SELECTOR);

        // Check if user cannot send invite anymore
        $I->amOnPage('/circles/'.$circle_slug.'/members');
        $I->seeElement(self::MEMBERS_INVITE_INPUT_SELECTOR);
        $I->fillField(self::MEMBERS_INVITE_INPUT_SELECTOR, $another_user->display_name);
        $I->waitForText(trans('search.no_res'), 5);

        // Check if another user receive notification and can cancel it
        $another = $I->haveFriend('another');
        $another->does(function (AcceptanceTester $I) use ($circle_slug,$another_user_id, $unread_notifications, $notification_text) {
            $I->loginAs($another_user_id, 'password!');
            // check if number of notifications will be increased
            $I->see((int) $unread_notifications + 1, self::NOTIFICATION_COUNT_SELECTOR);

            // open popup to see notifications
            $I->click(self::NOTIFICATION_TOGGLER_SELECTOR);
            $I->waitForText($notification_text, 5);

            // Reject Invite
            $I->click(self::INVITE_CANCEL_SELECTOR);
            $I->wait(2);
            $I->seeCurrentUrlEquals('/profile');
            $I->waitForElementVisible('.flash-message', 10);
            $I->waitForText(trans('circles.invite_canceled'), 10);
        });

        // Go and invite now one more time
        $I->fillField(self::MEMBERS_INVITE_INPUT_SELECTOR, $another_user->email);
        $I->wait(3);
        $I->click(self::AUTOCOMPLETE_INVITE_SELECTOR.' '.self::AC_RESULT_SELECTOR);
        $I->click(self::SEND_INVITES_BTN_SELECTOR);

        $another->does(function (AcceptanceTester $I) use (
            $circle_slug,
            $another_user_id,
            $unread_notifications,
            $notification_text,
            $another_user
        ) {
            // open popup to see notifications
            $I->click(self::NOTIFICATION_TOGGLER_SELECTOR);
            $I->waitForText($notification_text, 5);

            // Accept Invite
            $I->click(self::INVITE_ACCEPT_SELECTOR);
            $I->wait(2);
            $I->seeCurrentUrlEquals('/profile');
            $I->waitForElementVisible('.flash-message', 10);
            $I->waitForText(trans('circles.invite_accepted'), 10);

            // Check if user joined the group
            $I->amOnPage('/circles/'.$circle_slug);
            $I->scrollTo(self::CIRCLE_USER_WRAPPER_SELECTOR);
            $I->see($another_user->display_name);
        });
    }

    public function testIfUnregisteredUserCanAcceptInviteFromCircle(AcceptanceTester $I)
    {
        $title = 'Test for members invite unregistered user';
        $description = 'Bacon ipsum dolor sit amet nulla ham qui sint exercitation eiusmod';
        $admin_id = 1;
        $admin = User::find($admin_id);
        $email = 'something@test.com';
        $notification_text = trans('notifications.circle_invite_notification', [
                'name'         => $admin->username,
                'circle_title' => $title,
            ]
        );
        $circle_slug = $I->createCircle($admin_id, 'password!', $title, $description);
        $circle = Circle::whereSlug($circle_slug)->first();

        // Go to members invite page
        $I->amOnPage('/circles/'.$circle_slug.'/members');
        $I->seeElement(self::MEMBERS_INVITE_INPUT_SELECTOR);

        // Fill autocomplete and enter email needed
        $I->fillField(self::MEMBERS_INVITE_INPUT_SELECTOR, $email);
        $I->click(self::SEND_INVITES_BTN_SELECTOR);

        // Check email is received
        $I->wait(3);
        $I->receiveAnEmailToEmail($email);
        $I->seeInEmailHtmlBody(htmlentities($notification_text));

        // Generate link
        $invite = Invite::whereEmail($email)
            ->whereUsed(false)
            ->whereActive(true)
            ->first();

        $json = json_encode([
            'email' => $email,
            'invite_id' => $invite->id,
            'type' => 'circle',
            'inviter_id' => $circle->creator_id,
        ]);
        $encoded_invite = base64_encode($json);
        $link = route('unregistered.invite', ['encoded_invite' => $encoded_invite]);
        $link = str_replace('http://:', '', $link);

        // Visit link and check fro circle join
        $another = $I->haveFriend('another');
        $another->does(function (AcceptanceTester $I) use($link, $circle_slug) {
            // Go via link in email
            $I->amOnPage($link);

            $display_name = 'Super Testing';

            // Fill fields
            $I->fillField('input[name="display_name"]', $display_name);
            $I->fillField('input[name="talent"]', 'Testing everything i can');
            $I->fillField('input[name="password"]', 'Password123!');
            $I->click('button[type=submit]');

            // Check if user was created and can see welcome screen
            $I->wait(3);
            $I->click('a.welcome-redirect');
            $I->wait(3);
            $I->seeCurrentUrlEquals('/circles/'.$circle_slug);

            // Check if joined
            $I->scrollTo(self::CIRCLE_USER_WRAPPER_SELECTOR);
            $I->see($display_name);
        });
    }
}
