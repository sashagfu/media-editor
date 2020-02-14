<?php

use App\Models\User;

/**
 * Class PerformancesPageCest
 *
 * @SuppressWarnings(PHPMD)
 * @codingStandardsIgnoreStart
 */
class ProfilePageCest
{
    const PROFILE_POST_CONTAINER_SELECTOR = 'div.profile-post';
    const PROFILE_AUTHOR_LINK_SELECTOR = 'a.post-container__author';
    const TOTAL_LIKES_SELECTOR = 'p.profile__total-likes';
    const TOTAL_STARS_SELECTOR = 'p.profile__total-stars';
    const TOTAL_FOLLOWERS_SELECTOR = 'p.profile__total-followers';
    const PROFILE_POST_LINK_SELECTOR = 'a.post-container__single-url';
    const UNSTARRED_POST_BTN_SELECTOR = 'a.post-star__btn.unstarred.float-left';
    const UNLIKED_POST_BTN_SELECTOR = 'a.post-like__btn.unliked.float-left';
    const GEOCOMPLETE_INPUT_SELECTOR = 'input#geocomplete-container__input';
    const MAP_SELECTOR = 'div.geocomplete-map';
    const PAC_CONTAINER_SELECTOR = 'div.pac-container';
    const GEOCOMPLETE_BUTTON_UPDATE_SELECTOR = 'button.geocomplete-container__save.success.button';
    const GOOGLE_LINK_INPUT = 'input[name=google_plus]';
    const GOOGLE_LINK_SELECTOR = 'a.social-link.google_plus';
    const FACEBOOK_LINK_INPUT = 'input[name=facebook]';
    const FACEBOOK_LINK_SELECTOR = 'a.social-link.facebook';
    const INSTAGRAM_LINK_INPUT = 'input[name=instagram]';
    const INSTAGRAM_LINK_SELECTOR = 'a.social-link.instagram';
    const LINKEDIN_LINK_INPUT = 'input[name=linkedin]';
    const LINKEDIN_LINK_SELECTOR = 'a.social-link.linkedin';
    const SNAPCHAT_LINK_INPUT = 'input[name=snapchat]';
    const SNAPCHAT_LINK_SELECTOR = 'a.social-link.snapchat';
    const PROFILE_SAVE_BUTTON = 'button.save-profile';

    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function testRegisteredUserCanSeeHisPostsIfAny(AcceptanceTester $I)
    {
        $admin = User::whereEmail('admin@yuustar.com')->firstOrFail();
        $I->adminLogin();
        $I->amOnPage('/profile');
        $I->seeCurrentUrlEquals('/profile');
        $I->see(trans('posts.page_title'));

        if ($admin->posts) {
            foreach($admin->posts as $post){
                $I->see($post->created_ad->diffForHumans());
            }
            $I->seeNumberOfElements(self::PROFILE_POST_CONTAINER_SELECTOR, $admin->posts->count());
        } else {
            $I->see(trans('profiles.empty_posts'));
        }
    }

    public function testRegisteredUserCanSeeAnotherUserPostsIfAny(AcceptanceTester $I)
    {
        $I->adminLogin();
        $I->amOnPage('/performances');
        $I->seeCurrentUrlEquals('/performances');
        $username = $I->grabTextFrom(self::PROFILE_AUTHOR_LINK_SELECTOR.':first-of-type');
        $I->click(self::PROFILE_AUTHOR_LINK_SELECTOR.':first-of-type');
        $I->seeCurrentUrlEquals('/profile/'.$username);

        $user = User::whereUsername($username)->firstOrFail();

        $I->see($user->display_name);

        if ($user->posts) {
            foreach($user->posts as $post){
                $I->see($post->created_at->diffForHumans());
            }
            $I->seeNumberOfElements(self::PROFILE_POST_CONTAINER_SELECTOR, $user->posts->count());
        } else {
            $I->see(trans('profiles.empty_posts'));
        }
    }

    public function testIfTotalLikesStarsFollowersAreSeenForMe(AcceptanceTester $I)
    {
        // Admin Login
        $I->adminLogin();

        $I->amOnPage('/profile');
        $admin_user = User::find(1);

        // See for needed values
        if ($admin_user->total_likes > 0) {
            $I->see(trans('profiles.total_likes').': '.$admin_user->total_likes, self::TOTAL_LIKES_SELECTOR);
        }
        if ($admin_user->total_stars > 0) {
            $I->see(trans('profiles.total_stars').': '.$admin_user->total_stars, self::TOTAL_STARS_SELECTOR);
        }
        if ($admin_user->followers->count() > 0) {
            $I->see(trans('profiles.total_followers').': '.$admin_user->followers->count(), self::TOTAL_FOLLOWERS_SELECTOR);
        }

    }

    public function testIfTotalLikesStarsFollowersAreSeenForAnotherUser(AcceptanceTester $I)
    {
        // Admin Login
        $I->adminLogin();

        $another_user = User::find(4);
        $I->amOnPage('/profile/'.$another_user->username);


        // See for needed values
        if ($another_user->total_likes > 0) {
            $I->see(trans('profiles.total_likes').': '.$another_user->total_likes, self::TOTAL_LIKES_SELECTOR);
        }
        if ($another_user->total_stars > 0) {
            $I->see(trans('profiles.total_stars').': '.$another_user->total_stars, self::TOTAL_STARS_SELECTOR);
        }
        if ($another_user->followers->count() > 0) {
            $I->see(trans('profiles.total_followers').': '.$another_user->followers->count(), self::TOTAL_FOLLOWERS_SELECTOR);
        }
    }

    public function testIfNumberOfTotalValuesWillBeIncreased(AcceptanceTester $I)
    {
        // Declaring staff
        $I->createPost('Test total likes');
        $admin_user = User::find(1);
        $total_likes = $admin_user->total_likes;
        $total_stars = $admin_user->total_stars;
        $another_user_id = 3;
        $post = $admin_user->posts()->latest()->first();

        $another = $I->haveFriend('another');
        $another->does(function(AcceptanceTester $I) use ($another_user_id, $admin_user, $post) {
            $I->loginAs($another_user_id, 'password!');

            // Now got to Admin profile page
            $I->amOnPage('/profile/'.$admin_user->username);

            // Now click on post button
            $I->scrollTo(self::PROFILE_POST_LINK_SELECTOR.':last-of-type');
            $I->click(self::PROFILE_POST_LINK_SELECTOR.':last-of-type');

            $I->wait(2);

            $I->seeCurrentUrlMatches('/post/');
            if ($post->isPerformance()) {
                $I->click(self::UNSTARRED_POST_BTN_SELECTOR);
            } else {
                $I->click(self::UNLIKED_POST_BTN_SELECTOR);
            }
        });

        $I->amOnPage('/profile');

        if ($post->isPerformance()) {
            $I->see(trans('profiles.total_stars').': '.($total_stars + 1), self::TOTAL_STARS_SELECTOR);
        } else {
            $I->see(trans('profiles.total_likes').': '.($total_likes + 1), self::TOTAL_LIKES_SELECTOR);
        }

        if ($admin_user->followers->count() > 0) {
            $I->see(trans('profiles.total_followers').': '.$admin_user->followers->count(), self::TOTAL_FOLLOWERS_SELECTOR);
        }
    }

    public function testIfUserCanUpdateHisLocationAndItWillBeVisibleToOthers(AcceptanceTester $I)
    {
        // Declaring staff
        $admin_user = User::find(1);
        $another_user_id = 3;

        // Go to profile Page and see if everything needed is loaded
        $I->adminLogin();
        $I->amOnpage('/profile');
        $I->scrollTo(self::SNAPCHAT_LINK_INPUT);
        $I->seeElement(self::GEOCOMPLETE_INPUT_SELECTOR);
        $I->seeElement(self::MAP_SELECTOR);

        $I->fillField(self::GEOCOMPLETE_INPUT_SELECTOR, 'New York');
        $I->seeElement(self::PAC_CONTAINER_SELECTOR);
        $I->click(self::PAC_CONTAINER_SELECTOR.'>div.pac-item:first-child');

        $I->click(self::GEOCOMPLETE_BUTTON_UPDATE_SELECTOR);
        $I->wait(1);
        $I->see(trans('profiles.location_save'));

        $another = $I->haveFriend('another');
        $another->does(function(AcceptanceTester $I) use ($another_user_id, $admin_user) {
            $I->loginAs($another_user_id, 'password!');

            // Now got to Admin profile page
            $I->amOnPage('/profile/'.$admin_user->username);

            // Now click on post button
            $I->scrollTo(self::MAP_SELECTOR);
            $I->seeElement(self::MAP_SELECTOR.'>iframe');
        });
    }

    public function testIfUserWillNotSeeLocationOfAnotherUser(AcceptanceTester $I)
    {
        $admin_user = User::find(1);
        $admin_user->deleteSetting('location');
        $another_user_id = 7;

        $another = $I->haveFriend('another');
        $another->does(function(AcceptanceTester $I) use ($another_user_id, $admin_user) {
            $I->loginAs($another_user_id);

            // Now got to Admin profile page
            $I->amOnPage('/profile/'.$admin_user->username);

            // Now click on post button
            $I->dontSeeElementInDOM(self::MAP_SELECTOR);
            $I->dontSeeElementInDOM(self::MAP_SELECTOR.'>iframe');
        });
    }

    public function testIfUserCanUpdateHisSocialLinks(AcceptanceTester $I)
    {
        // Declaring staff
        $admin_user = User::find(1);
        $another_user_id = 3;

        // Go to profile Page and see if everything needed is loaded
        $I->adminLogin();
        $I->amOnpage('/profile');
        $I->scrollTo(self::GOOGLE_LINK_INPUT);

        $I->fillField(self::GOOGLE_LINK_INPUT, 'https://plus.google.com/');
        $I->fillField(self::FACEBOOK_LINK_INPUT, 'https://facebook.com/');
        $I->fillField(self::INSTAGRAM_LINK_INPUT, 'https://instagram.com/');
        $I->fillField(self::LINKEDIN_LINK_INPUT, 'https://linkedin.com/');
        $I->fillField(self::SNAPCHAT_LINK_INPUT, 'https://snapchat.com/');
        $I->scrollTo(self::PROFILE_SAVE_BUTTON);
        $I->click(self::PROFILE_SAVE_BUTTON);

        $I->wait(5);

        $I->seeCurrentUrlEquals('/profile');
        $I->seeElement(self::GOOGLE_LINK_SELECTOR);
        $I->seeElement(self::FACEBOOK_LINK_SELECTOR);
        $I->seeElement(self::INSTAGRAM_LINK_SELECTOR);
        $I->seeElement(self::LINKEDIN_LINK_INPUT);
        $I->seeElement(self::SNAPCHAT_LINK_INPUT);

        $another = $I->haveFriend('another');
        $another->does(function(AcceptanceTester $I) use ($another_user_id, $admin_user) {
            $I->loginAs($another_user_id, 'password!');

            // Now got to Admin profile page
            $I->amOnPage('/profile/'.$admin_user->username);

            // Now click on post button
            $I->seeElement(self::GOOGLE_LINK_SELECTOR);
            $I->seeElement(self::FACEBOOK_LINK_SELECTOR);
            $I->seeElement(self::INSTAGRAM_LINK_SELECTOR);
            $I->seeElement(self::LINKEDIN_LINK_SELECTOR);
            $I->seeElement(self::SNAPCHAT_LINK_SELECTOR);
        });
    }
}
