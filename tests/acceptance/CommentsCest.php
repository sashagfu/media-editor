<?php


class CommentsCest
{
    const COMMENT_TEXT = 'I am robot, but i can write comments!';
    const NESTED_COMMENT_TEXT = 'I am robot, but i can write nested comments!';
    const OPEN_COMMENTS_CONTAINER = 'a.post-container_comments';
    const COMMENTS_CONTAINER = 'div.comments-container.callout';
    const SECTION_CONTENT = 'section.content';
    const REPLY_BTN_SELECTOR = 'a.comment-reply__btn';
    const REPLY_FORM = 'form.comments-reply__form';


    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function testIfICanLeaveComments(AcceptanceTester $I)
    {
        // Admin login and choose post
        $I->adminLogin();
        $post_id = 5;
        $post = Post::find($post_id);


        $I->amOnPage('/post/'.$post_id);
        $I->click(self::OPEN_COMMENTS_CONTAINER);

        $I->waitForElementVisible(self::COMMENTS_CONTAINER, 5);

        if ($post->comments->count()) {
            $I->see($post->comments->first()->text, self::SECTION_CONTENT);
            $I->scrollTo(self::REPLY_BTN_SELECTOR.':first-of-type');
            $I->click(self::REPLY_BTN_SELECTOR.':first-of-type');
            $I->waitForElementVisible(self::REPLY_FORM, 5);

            $I->submitForm(self::REPLY_FORM, ['comment_text' => self::NESTED_COMMENT_TEXT]);

            $I->wait(3);

            // refresh page to see if everything is good
            $I->amOnPage('/post/'.$post_id);
            $I->click(self::OPEN_COMMENTS_CONTAINER);
            $I->scrollTo(self::REPLY_BTN_SELECTOR.':first-of-type');
            $I->see(self::NESTED_COMMENT_TEXT, self::SECTION_CONTENT.'>p');
        } else {
            $I->submitForm(self::REPLY_FORM, ['comment_text' => self::COMMENT_TEXT]);
            $I->wait(3);
            $I->scrollTo(self::REPLY_BTN_SELECTOR.':first-of-type');
            $I->see(self::COMMENT_TEXT, self::SECTION_CONTENT.'section.content>p');

            // refresh page to see if everything is good
            $I->amOnPage('/post/'.$post_id);
            $I->click(self::OPEN_COMMENTS_CONTAINER);
            $I->scrollTo(self::REPLY_BTN_SELECTOR.':first-of-type');
            $I->see(self::COMMENT_TEXT, 'section.content>p');
        }
    }
}
