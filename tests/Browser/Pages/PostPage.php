<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;
use Tests\DuskTestCase;
use PHPUnit_Framework_Assert as PHPUnit;

class PostPage extends BasePage
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/';
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@feed_main'              => '#feed',
            '@post_container'         => '.timeline__box .post',
            '@post_container_actions' => '.post-footer-with-medallions',
            '@feed_form'              => '#feed-form',
            '@post_menu_headline'     => '.head-line__settings',
            '@content_div'            => '.create-post__content',
            '@post_description'       => '.post-main .main__text-item',
            '@images_input'           => '.create-post__images',
            '@videos_btn_selection'   => '.create-post__video-btn',
            '@videos_modal'           => '#videosModal',
            '@videos_modal_item'      => '.media-tab__item',
            '@video_select_btn'       => 'a.media-tab__select-btn',
            '@create_post_btn'        => '.post-create__button',
            '@progress_bar'           => '.default-progress-bar',
            '@post_link'              => '.post-container__single-url',
            '@like_btn'               => '.blog-post-likes__icon',
            '@star_btn'               => '.blog-post-stars__icon',
            '@star_count'             => '.blog-post-stars__number',
            '@like_count'             => '.blog-post-likes__number',
            '@reaction_loading'       => '.post-footer-with-medallions > .level-left >  i.fa-spinner',
            '@flag_post_btn'          => '.post a.head-line__flag-btn',
            '@flags_modal'            => '.flag-post-popup',
            '@flags_modal_comment'    => '.flag-post__comment',
            '@create_flag_btn'        => '.flag-post__create-button',
            '@comments_btn'           => '.blog-post-comments__icon',
            '@comments_count'         => '.blog-post-comments__number',
            '@new_comment_form'       => '.comment-a-post__input',
            '@new_subcomment_form'    => '.comment-a-post:last-of-type .comment-a-post__input:last-of-type',
            '@subcomments_open_btn'   => '.comment-likes__reply',
            '@comment_container'      => '.comment',
            '@add_comment_btn'        => '.comment-a-post__button',
            '@subcomment_container'   => '.comments-replay .comment',
            '@post_share_btn'         => '.level-item.blog-post-shares',
            '@email_share_btn'        => 'a.post-container__email-share',
            '@email_share_modal'      => '.modal.email-share-modal',
            '@emails_input'           => '.email-share-modal__emails-input',
            '@email_share_btn_send'   => '.email-share-modal__share',
            '@feed_share_btn'         => '.post-container__share',
            '@users_feed_share'       => '.user-feed-share',
            '@chat_share'             => '.chat-share',
            '@sharing_modal'          => '.chat-share-modal',
            '@chat_to_share_users'    => '.multiselect__content',
            '@chat_to_share_item'     => '.multiselect__element',
            '@chat_to_share_btn'      => '.chat-share__btn',
            '@facebook_share'         => '.facebook-share',
            '@twitter_share'          => '.twitter-share',
        ];
    }

    public function goToSinglePostPage(Browser $browser, $slug)
    {
        $browser->visit(route('post.single', ['slug' => $slug]))
            ->pause(5000)
            ->assertVisible('@post_container')
            ->assertVisible('@post_container_actions')
            ->assertPathis('/post/' . $slug);
    }
}
