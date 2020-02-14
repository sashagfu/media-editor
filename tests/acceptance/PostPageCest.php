<?php

/**
 * Class PerformancesPageCest
 *
 * @SuppressWarnings(PHPMD)
 * @codingStandardsIgnoreStart
 */
class PostPageCest
{
    const POST_CREATE_LINK = '/post/create';
    const POST_SHARE_BUTTON_SELECTOR = 'button.post-create__button';
    const POST_CONTENT_ERROR_TEXT = 'The post content field is required.';
    const POST_VIDEO_ERROR_TEXT = 'The video id field is required when none of images are present.';
    const POST_CONTENT_SELECTOR = 'div.create-post__content.contenteditable';
    const CREATE_POST_VIDEO_OPEN_BTN = 'a.create-post__video-btn';
    const VIDEOS_MODAL_SELECTOR = '#videosModal';
    const MEDIA_TAB_ITEM_SELECTOR = 'div.media-tab__item';
    const MEDIA_ITEM_BUTTON_SELECTOR = 'a.media-tab__select-btn';
    const PROFILE_POST_URL_SELECTOR  = 'a.post-container__single-url';
    const USER_MENTION_SELECTOR  = 'a.user-mention';
    const CLOSE_BUTTON_SELECTOR = 'button.close-button';
    const POST_HASHTAG_SELECTOR = 'a.post-hashtag';
    const POST_CONTAINER_SELECTOR = 'div.blog-post.post-container';
    const POST_SINGLE_LINK_SELECTOR = 'a.post-container__single-url';
    const POST_FLAG_SELECTOR = 'a.post-container__flag';
    const POST_FLAG_FORM = 'form.flags-container__form';
    const POST_FLAG_REASON = 'So abuse content';
    const PROGRESS_BAR_SELECTOR = '.default-progress-bar';

    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function testUnRegisteredUserCannotAccessPostCreationPage(AcceptanceTester $I)
    {
        $I->amOnPage(self::POST_CREATE_LINK);
        $I->seeElement('form');
        $I->submitForm('form', ['email' =>'dummy_email@dummy.com', 'password' => 'password!']);
        $I->seeCurrentUrlEquals('/login');
    }

    public function testRegisteredUserCanAccessPostCreationPage(AcceptanceTester $I)
    {
        $I->adminLogin();
        $I->amOnPage(self::POST_CREATE_LINK);
        $I->seeCurrentUrlEquals(self::POST_CREATE_LINK);
    }

    public function testPostCreationValidation(AcceptanceTester $I)
    {
        $I->adminLogin();
        $I->amOnPage(self::POST_CREATE_LINK);
        $I->seeCurrentUrlEquals(self::POST_CREATE_LINK);

        $I->click(self::POST_SHARE_BUTTON_SELECTOR);
        $I->waitForText(self::POST_CONTENT_ERROR_TEXT, 2);
    }

    public function testIfPostWillBeCreatedWithoutVideo(AcceptanceTester $I)
    {
        $I->adminLogin();
        $I->amOnPage(self::POST_CREATE_LINK);
        $I->seeCurrentUrlEquals(self::POST_CREATE_LINK);

        $I->fillField(self::POST_CONTENT_SELECTOR, 'My dummy description');

        $I->click(self::POST_SHARE_BUTTON_SELECTOR);
        $I->waitForText(self::POST_VIDEO_ERROR_TEXT, 2);
    }

    public function testIfPostWillBeCreatedProperly(AcceptanceTester $I)
    {
        $I->adminLogin();
        $I->amOnPage(self::POST_CREATE_LINK);
        $I->seeCurrentUrlEquals(self::POST_CREATE_LINK);

        $I->fillField(self::POST_CONTENT_SELECTOR, 'My dummy description');

        // Select video and create post
        $I->click(self::CREATE_POST_VIDEO_OPEN_BTN);
        $I->waitForElementVisible(self::VIDEOS_MODAL_SELECTOR, 5);
        $I->moveMouseOver(self::MEDIA_TAB_ITEM_SELECTOR.':first-of-type');
        $I->waitForElementVisible(self::MEDIA_ITEM_BUTTON_SELECTOR.':first-of-type');
        $I->click(self::MEDIA_ITEM_BUTTON_SELECTOR.':first-of-type');
        $I->click(self::CLOSE_BUTTON_SELECTOR);
        $I->scrollTo(self::POST_SHARE_BUTTON_SELECTOR);
        $I->click(self::POST_SHARE_BUTTON_SELECTOR);
        $I->waitForElement(self::PROGRESS_BAR_SELECTOR);

        $I->wait(8);

        $I->seeCurrentUrlEquals('/profile');
        $I->see(trans('posts.created'));
    }

    public function testIfHashtagsWillBeParsed(AcceptanceTester $I)
    {
        $hashtag = '#hashtag';
        $I->adminLogin();
        $I->amOnPage(self::POST_CREATE_LINK);
        $I->seeCurrentUrlEquals(self::POST_CREATE_LINK);

        $I->fillField(self::POST_CONTENT_SELECTOR, 'My dummy description with '.$hashtag);

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

        $I->amOnPage('/tag/hashtag');
        $I->see('My dummy description with');
        $I->seeElement(self::POST_HASHTAG_SELECTOR);
    }

    public function checkAfterClickingHashtagIWillSeePostsWithThisHashtag(AcceptanceTester $I)
    {
        $admin = User::find(1);
        $I->adminLogin();
        $I->amOnPage('/performances');
        $I->seeCurrentUrlEquals('/performances');

        $tag = str_replace('#', '', $I->grabTextFrom(self::POST_HASHTAG_SELECTOR.':first-of-type'));
        $I->click(self::POST_HASHTAG_SELECTOR.':first-of-type');
        $I->canSeeInCurrentUrl('/tag/'.$tag);

        $countTagPosts = Post::where('content', 'like', '%'.'#'.$tag.'%')
            ->select('posts.*')
            ->skipFlagged($admin)
            ->latest()
            ->count();

        $I->canSeeNumberOfElements(self::POST_CONTAINER_SELECTOR, $countTagPosts);
    }

    public function testIfUserCanSeeSinglePostPage(AcceptanceTester $I)
    {
        $I->adminLogin();
        $I->amOnPage('/performances');
        $I->seeCurrentUrlEquals('/performances');

        $post_id = $I->grabAttributeFrom(self::POST_CONTAINER_SELECTOR.':first-of-type', 'data-id');

        $I->amOnPage('/post/'.$post_id);
        $I->seeCurrentUrlEquals('/post/'.$post_id);
        $content = Post::find($post_id)->makeContent();
        $I->see($content);
    }

    public function testIfUserCanNotSeeFlaggedPost(AcceptanceTester $I)
    {
        $I->adminLogin();
        $I->amOnPage('/performances');
        $I->seeCurrentUrlEquals('/performances');

        $post_id = $I->grabAttributeFrom(self::POST_CONTAINER_SELECTOR.':first-of-type', 'data-id');

        $I->click(self::POST_FLAG_SELECTOR.':first-of-type');
        $I->waitForText(trans('flags.reason_select'), 2);
        $I->waitForText(trans('flags.reason_comment'), 2);
        $I->submitForm(self::POST_FLAG_FORM, ['reason_comment' => self::POST_FLAG_REASON]);

        $content = Post::find($post_id)->makeContent();

        $I->wait(2);
        $I->dontSee($content);

        $I->amOnPage('/post'.$post_id);
        $I->dontSee($content);
    }
}
