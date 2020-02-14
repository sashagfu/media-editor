<?php


class NotificationsCest
{
    const NOTIFICATIONS_PER_LOAD = 10;
    const UNSTARRED_POST_BTN_SELECTOR = 'a.post-star__btn.unstarred.float-left';
    const UNLIKED_POST_BTN_SELECTOR = 'a.post-like__btn.unliked.float-left';
    const NOTIFICATION_COUNT_SELECTOR = 'span.notifications-count';
    const NOTIFICATION_TOGGLER_SELECTOR = 'a.notifications-toggler';
    const NOTIFICATION_ITEM_SELECTOR = 'a.notification-container__link';
    const NOTIFICATION_ITEM_LINK_SELECTOR = '#notifications-container > div > div.row.notifications-scroll > a';
    const FOLLOW_USER_BTN_SELECTOR = 'a.button.user-following__btn';
    const NEW_NOTIFICATION_SELECTOR = 'div.notification-container__new-notification';
    const OPEN_COMMENTS_CONTAINER = 'a.post-container_comments';
    const COMMENT_CONTAINER_FORM = 'form.comments-container__form';
    const COMMENT_REPLY_BTN_SELECTOR = 'a.comment-reply__btn:first-of-type';
    const REPLY_COMMENT_FORM = 'form.comments-reply__form';
    const POST_CONTENT_SELECTOR = 'div.create-post__content.contenteditable';
    const CREATE_POST_VIDEO_OPEN_BTN = 'a.create-post__video-btn';
    const VIDEOS_MODAL_SELECTOR = '#videosModal';
    const MEDIA_TAB_ITEM_SELECTOR = 'div.media-tab__item';
    const MEDIA_ITEM_BUTTON_SELECTOR = 'a.media-tab__select-btn';
    const POST_SHARE_BUTTON_SELECTOR = 'button.post-create__button';
    const PROFILE_POST_URL_SELECTOR  = 'a.post-container__single-url';
    const CLOSE_BUTTON_SELECTOR = 'button.close-button';

    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function testIfUserCanSendNotificationToOtherUser(AcceptanceTester $I)
    {
        // Declaring all stuff
        $admin_user = User::find(1);
        $another_user_id = 3;
        $another_user = User::find($another_user_id);
        $unread_notifications = $admin_user->unreadNotifications->count();

        // Lets login as admin and create post
        $I->createPost('Test notification sending', 15);

        // Lets login as another User
        $another = $I->haveFriend('another');
        $another->does(function(AcceptanceTester $I) use ($another_user_id, $admin_user) {
            $I->loginAs($another_user_id, 'password!');

            // Now find simple or performance post of Admin
            $post = $admin_user->posts->first();
            $post_id = $post->id;

            // Go to the post page and like or star it
            $I->amOnPage('/post/'.$post_id);

            if ($post->isPerformance()) {
                $I->click(self::UNSTARRED_POST_BTN_SELECTOR);
            } else {
                $I->click(self::UNLIKED_POST_BTN_SELECTOR);
            }
        });

        // wait some time to get notification to user
        $I->wait(5);

        // check if number of notifications will be increased
        $I->see((int) $unread_notifications + 1, self::NOTIFICATION_COUNT_SELECTOR);

        // open popup to see notifications
        $I->click(self::NOTIFICATION_TOGGLER_SELECTOR);
        $post = $admin_user->posts->first();
        if ($post->isPerformance()) {
            $I->waitForText(trans('notifications.post_notification_star', [
                    'name' => $another_user->username,
                ]
            ), 5);
        } else {
            $I->waitForText(trans('notifications.post_notification_like', [
                    'name' => $another_user->username,
                ]
            ), 5);
        }
    }

    public function testIfNotificationWillBeAppendedToPopupImmediately(AcceptanceTester $I)
    {
        // Declaring all stuff
        $admin_user = User::find(1);
        $another_user_id = 3;
        $another_user = User::find($another_user_id);
        $unread_notifications = $admin_user->unreadNotifications->count();

        // Lets login as admin and create post
        $I->createPost('Test notification sending again', 15);

        // Open popup to see if magic is done
        $I->click(self::NOTIFICATION_TOGGLER_SELECTOR);

        // Lets login as another User
        $another = $I->haveFriend('another');
        $another->does(function(AcceptanceTester $I) use ($another_user_id, $admin_user) {
            $I->loginAs($another_user_id, 'password!');

            // Now find simple or performance post of Admin
            $post = $admin_user->posts->last();
            $post_id = $post->id;

            // Go to the post page and like or star it
            $I->amOnPage('/post/'.$post_id);

            if ($post->isPerformance()) {
                $I->click(self::UNSTARRED_POST_BTN_SELECTOR);
            } else {
                $I->click(self::UNLIKED_POST_BTN_SELECTOR);
            }
        });

        // wait some time to get notification to user
        $I->wait(5);

        // check if number of notifications will be increased
        $I->see((int) $unread_notifications + 1, self::NOTIFICATION_COUNT_SELECTOR);

        //check if notification will be appended
        $post = $admin_user->posts->first();
        if ($post->isPerformance()) {
            $I->waitForText(trans('notifications.post_notification_star', [
                    'name' => $another_user->username,
                ]
            ), 5);
        } else {
            $I->waitForText(trans('notifications.post_notification_like', [
                    'name' => $another_user->username,
                ]
            ), 5);
        }
    }

    public function testIfPostAuthorWillNotRecieveNotificationOnSelfLikeOrStarPost(AcceptanceTester $I)
    {
        // Declaring all stuff
        $admin_user = User::find(1);
        $unread_notifications = $admin_user->unreadNotifications->count();

        // Lets login as admin
        $I->adminLogin();

        // Find Post
        $post = $admin_user->posts->first();
        $post_id = $post->id;

        // Go to the post page and like or star it
        $I->amOnPage('/post/'.$post_id);

        // Open popup to see if magic is done
        $I->click(self::NOTIFICATION_TOGGLER_SELECTOR);

        if ($post->isPerformance()) {
            $I->click(self::UNSTARRED_POST_BTN_SELECTOR);
        } else {
            $I->click(self::UNLIKED_POST_BTN_SELECTOR);
        }

        // wait some time to get notification to user
        $I->wait(5);

        // check if number of notifications will not be increased
        $I->dontSee((int) $unread_notifications + 1, self::NOTIFICATION_COUNT_SELECTOR);

        //check if notification will not be appended
        $post = $admin_user->posts->first();
        if ($post->isPerformance()) {
            $I->wait(3);
            $I->dontSee(trans('notifications.post_notification_star', [
                    'name' => $admin_user->username,
                ]
            ));
        } else {
            $I->wait(3);
            $I->dontSee(trans('notifications.post_notification_like', [
                    'name' => $admin_user->username,
                ]
            ));
        }
    }

    public function testIfUserCanUseInfiniteScrollToLoadMoreNotifications(AcceptanceTester $I)
    {
        // Declaring all stuff
        $admin_user = User::find(1);
        $unread_notifications = $admin_user->unreadNotifications->count();
        $scroll_times = floor($unread_notifications/10);

        // Lets login as admin
        $I->adminLogin();

        // Open popup to load first notifications
        $I->click(self::NOTIFICATION_TOGGLER_SELECTOR);

        if ($unread_notifications < self::NOTIFICATIONS_PER_LOAD) {
            $I->canSeeNumberOfElementsInDOM(self::NOTIFICATION_ITEM_SELECTOR, $unread_notifications);
        } else {
            $I->canSeeNumberOfElementsInDOM(self::NOTIFICATION_ITEM_SELECTOR, self::NOTIFICATIONS_PER_LOAD);
            for ($i = 1; $i <= $scroll_times; $i++) {
                $I->executeJS("$('.notifications-container').animate({scrollTop: $('.notification-container__link:last').offset().top })");
                $I->wait(5);
                $i == $scroll_times ?
                    $I->canSeeNumberOfElementsInDOM(self::NOTIFICATION_ITEM_SELECTOR, $unread_notifications) :
                    $I->canSeeNumberOfElementsInDOM(self::NOTIFICATION_ITEM_SELECTOR,
                        ($i * self::NOTIFICATIONS_PER_LOAD) + self::NOTIFICATIONS_PER_LOAD);
            }
        }
    }

    public function testIfNotificationClickWillRedirectMeToPostAndNotificationMarkedAsRead(AcceptanceTester $I)
    {
        // Declaring all stuff
        $admin_user = User::find(1);
        $unread_notifications_before = $admin_user->unreadNotifications->count();

        // Lets login as admin
        $I->adminLogin();

        // Open popup to load first notifications
        $I->click(self::NOTIFICATION_TOGGLER_SELECTOR);

        // Grab link and click on it
        $notification_link = $I->grabAttributeFrom(self::NOTIFICATION_ITEM_LINK_SELECTOR.':nth-child(1)', 'href');
        $I->click(self::NOTIFICATION_ITEM_LINK_SELECTOR.':nth-child(1)');

        // Waiting for redirect
        $I->wait(2);

        //make link in codecept format
        $edited_link = str_replace('http://127.0.0.1:8000', '', $notification_link);

        // Checking if link is right
        $I->seeCurrentUrlEquals($edited_link);

        // Checking total number of read notifications
        $I->See((int) $unread_notifications_before - 1, self::NOTIFICATION_COUNT_SELECTOR);

        //Checking if css class was removed
        $I->click(self::NOTIFICATION_TOGGLER_SELECTOR);
        $I->dontSeeElement(self::NOTIFICATION_ITEM_LINK_SELECTOR . ':nth-child(1) >' . self::NEW_NOTIFICATION_SELECTOR);
    }

    public function testIfUserWillRecieveNotificationAfterSomeoneFollowsHim(AcceptanceTester $I)
    {
        // Declaring all stuff
        $admin_user = User::find(1);
        $another_user_id = 3;
        $another_user = User::find($another_user_id);
        $unread_notifications = $admin_user->unreadNotifications->count();
        $notification_text = trans('notifications.following_notification', [
                'name' => $another_user->username
            ]
        );

        // Lets login as admin and
        $I->adminLogin();

        // Lets login as another User
        $another = $I->haveFriend('another');
        $another->does(function(AcceptanceTester $I) use ($another_user_id, $admin_user) {
            $I->loginAs($another_user_id, 'password!');

            // Now got to Admin profile page
            $I->amOnPage('/profile/'.$admin_user->username);

            // Now click on follow button
            $I->click(self::FOLLOW_USER_BTN_SELECTOR.'.info');

            $I->wait(5);

            // Make sure user followed admin
            $I->seeElement(self::FOLLOW_USER_BTN_SELECTOR.'.alert');
        });

        // wait some time to get notification to user
        $I->wait(5);

        // check if number of notifications will be increased
        $I->see((int) $unread_notifications + 1, self::NOTIFICATION_COUNT_SELECTOR);

        // open popup to see notifications
        $I->click(self::NOTIFICATION_TOGGLER_SELECTOR);
        $I->waitForText($notification_text, 5);

        $I->receiveAnEmailToEmail($admin_user->email);
        $I->seeInEmailHtmlBody(htmlentities($notification_text));
    }

    public function testIfFollowingNotificationClickWillRedirectMeToProfileAndNotificationMarkedAsRead(AcceptanceTester $I)
    {
        // Declaring all stuff
        $admin_user = User::find(1);
        $unread_notifications_before = $admin_user->unreadNotifications->count();

        // Lets login as admin
        $I->adminLogin();

        // Open popup to load first notifications
        $I->click(self::NOTIFICATION_TOGGLER_SELECTOR);

        // Grab link and click on it
        $notification_link = $I->grabAttributeFrom(self::NOTIFICATION_ITEM_LINK_SELECTOR.':nth-child(1)', 'href');
        $I->click(self::NOTIFICATION_ITEM_LINK_SELECTOR.':nth-child(1)');

        // Waiting for redirect
        $I->wait(2);

        //make link in codecept format
        $edited_link = str_replace('http://127.0.0.1:8000', '', $notification_link);

        // Checking if link is right
        $I->seeCurrentUrlEquals($edited_link);

        // Checking total number of read notifications
        $I->See((int) $unread_notifications_before - 1, self::NOTIFICATION_COUNT_SELECTOR);

        //Checking if css class was removed
        $I->click(self::NOTIFICATION_TOGGLER_SELECTOR);
        $I->dontSeeElement(self::NOTIFICATION_ITEM_LINK_SELECTOR . ':nth-child(1) >' . self::NEW_NOTIFICATION_SELECTOR);
    }

    public function testIfUserCanReceiveNotificationAfterHisPostCommented(AcceptanceTester $I)
    {
        // Declaring all stuff
        $admin_user = User::find(1);
        $another_user_id = 3;
        $another_user = User::find($another_user_id);
        $unread_notifications = $admin_user->unreadNotifications->count();
        $post = $admin_user->posts->first();
        $notification_text = trans('notifications.post_notification_comment', [
            'name' => $another_user->username,
        ]);

        // Lets login as admin and
        $I->adminLogin();

        // Lets login as another User
        $another = $I->haveFriend('another');
        $another->does(function(AcceptanceTester $I) use ($another_user_id, $admin_user) {
            $I->loginAs($another_user_id, 'password!');

            // Now find post of Admin
            $post = $admin_user->posts->first();
            $post_id = $post->id;

            // Go to the post page
            $I->amOnPage('/post/'.$post_id);

            // Open comments and add one
            $I->click(self::OPEN_COMMENTS_CONTAINER);
            $I->submitForm(self::COMMENT_CONTAINER_FORM, ['comment_text' => 'I am robot, but i can write tests']);
        });

        // wait some time to get notification to user
        $I->wait(5);

        // check if number of notifications will be increased
        $I->see((int) $unread_notifications + 1, self::NOTIFICATION_COUNT_SELECTOR);

        // open popup to see notifications
        $I->click(self::NOTIFICATION_TOGGLER_SELECTOR);
        $I->waitForText($notification_text, 5);

        $I->receiveAnEmailToEmail($admin_user->email);
        $I->seeInEmailHtmlBody(htmlentities($notification_text));
    }

    public function testIfUserCanReceiveNotificationAfterHisCommentWasReplied(AcceptanceTester $I)
    {
        // Declaring all stuff
        $admin_user = User::find(1);
        $another_user_id = 3;
        $another_user = User::find($another_user_id);
        $unread_notifications = $admin_user->unreadNotifications->count();
        $post_id = 1;
        $post = Post::find($post_id);
        $notification_text = trans('notifications.comment_reply_notification', [
            'name' => $another_user->username,
        ]);

        // Lets login as admin and create comment
        $I->adminLogin();

        $I->amOnPage('/post/'.$post_id);

        $I->click(self::OPEN_COMMENTS_CONTAINER);

        $I->scrollTo(self::COMMENT_CONTAINER_FORM);
        $I->submitForm(self::COMMENT_CONTAINER_FORM, ['comment_text' => 'super-duper comment text']);
        $I->wait(3);
        $I->scrollTo(self::COMMENT_REPLY_BTN_SELECTOR);
        $I->see('super-duper comment text');

        // Lets login as another User
        $another = $I->haveFriend('another');
        $another->does(function(AcceptanceTester $I) use ($another_user_id, $admin_user, $post_id, $post) {
            $I->loginAs($another_user_id, 'password!');

            // Go to the post page
            $I->amOnPage('/post/'.$post_id);

            // Open comments and add one
            $I->click(self::OPEN_COMMENTS_CONTAINER);
            $I->scrollTo(self::COMMENT_REPLY_BTN_SELECTOR.':last-of-type');
            $I->click(self::COMMENT_REPLY_BTN_SELECTOR.':last-of-type');
            $I->waitForElementVisible(self::REPLY_COMMENT_FORM, 5);

            $I->submitForm(self::REPLY_COMMENT_FORM, ['comment_text' => 'super nested text']);
        });

        // wait some time to get notification to user
        $I->wait(5);

        // check if number of notifications will be increased
        $I->see((int) $unread_notifications + 1, self::NOTIFICATION_COUNT_SELECTOR);

        // open popup to see notifications
        $I->click(self::NOTIFICATION_TOGGLER_SELECTOR);
        $I->waitForText($notification_text, 5);
    }

    public function testIfFollowerWillReceiveNotificationOnUserCreatePost(AcceptanceTester $I)
    {
        // Declaring all stuff
        $admin_user = User::find(1);
        $another_user_id = 3;
        $another_user = User::find($another_user_id);
        $unread_notifications = $admin_user->unreadNotifications->count();
        $post_content = 'Test notification content post creation';
        $notification_text = trans('notifications.follower_post_create', [
            'name' => $another_user->username,
        ]);

        // Lets login as admin and follow if needed some user
        $I->adminLogin();

        // Ensure following
        $I->amOnPage('/profile');
        $I->scrollTo('h4.following_title');
        $I->see($another_user->display_name);

        // Lets login as another User
        $another = $I->haveFriend('another');
        $another->does(function(AcceptanceTester $I) use ($another_user_id, $admin_user, $post_content) {
            $I->loginAs($another_user_id);

            // Go to post creation page
            $I->amOnPage('/post/create');

            $I->fillField(self::POST_CONTENT_SELECTOR, $post_content);

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
        });

        // wait some time to get notification to user
        $I->wait(5);

        // check if number of notifications will be increased
        $I->see((int) $unread_notifications + 1, self::NOTIFICATION_COUNT_SELECTOR);

        // open popup to see notifications
        $I->click(self::NOTIFICATION_TOGGLER_SELECTOR);
        $I->waitForText($notification_text, 5);

        $I->receiveAnEmailToEmail($admin_user->email);
        $I->seeInEmailHtmlBody(htmlentities($notification_text));
    }
}
