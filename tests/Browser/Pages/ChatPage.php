<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;
use Tests\DuskTestCase;

class ChatPage extends BasePage
{
    const ADMIN_ID = 1;
    const ANOTHER_CHAT_TESTER_ID = 3;
    const SECOND_CHAT_TESTER_ID = 4;
    const CHAT_CREATION_WAIT_TIME = 5;
    const MAX_MESSAGE_CREATION_TIME = 5;
    const MAX_CHAT_WAIT_TIME = 5;
    const MULTIPLE_CHAT_USERS_COUNT = 4;

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/chat';
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@start_chat'                       => '.start-chat',
            '@start_chat_btn'                   => '.profile-action__btn--message',
            '@start_chat_modal'                 => '.chat-create-popup',
            '@start_chat_modal_message'         => '.chat-create__main #input-box__message',
            '@start_chat_modal_recipients'      => '.chat-create__main .input-box__recipients',
            '@start_chat_modal_add_friends_btn' => '.chat-create__main .input-box__button--add-friends',
            '@search_friends_box'               => '.search-friends-box',
            '@search_friends_input'             => '.search-friends-box .multiselect__input',
            '@search_friend_item'               => '.multiselect__content .multiselect__element',
            '@start_chat_modal_btn'             => '.chat-create__main .btn.btn--primary',
            '@chat_thread_container'            => '.chat-messages__left-column .side-messages',
            '@chat_message_preview'             => '.side-messages__text',
            '@messenger_input'                  => '.messenger__input textarea.input-box__input',
            '@messages_container'               => '.chat-messages__right-column',
            '@messages_container_send_btn'      => '.messenger .messenger__button--save',
            '@widget_messages_container'        => '.chat-menu-box__item',
            '@widget_message_single_container'  => '.chat-menu-box__item .chat-content-box',
            '@single_message_container'         => '.conversation__main .conversation-item',
            '@single_message'                   => '.conversation-item__text',
            '@widget_single_message'            => '.chat-content-box__title-text',
            '@send_message_form'                => '.chat-form-send',
            '@chat_unread_count'                => '.chat > .chat__item > .tag',
            '@chat_dropdown_btn'                => '.chat__item.chat-box',
            '@threads_menu'                     => 'div#chat-top-menu-container',
            '@message_input'                    => 'textarea[name=message]',
            '@chat_widget'                      => '#chat-widget',
            '@chat_menu_box_item'               => '.chat-menu-box__item',
            '@chat_menu_thread_item'            => '.chat-menu-box__item .chat-content-box',
            '@chat_content_box'                 => '.chat-menu-box__item > .chat-content-box',
        ];
    }

    /**
     * Assert that the browser is on the page.
     *
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url());
    }

    public function createChat(Browser $browser, $message, $display_name)
    {
        $browser->visit(route('front.my_profile'))
            ->waitFor('@start_chat_btn')
            ->click('@start_chat_btn')
            ->waitFor('@start_chat_modal')
            ->assertVisible('@start_chat_modal_message')
            ->assertVisible('@start_chat_modal_add_friends_btn')
            ->type('@start_chat_modal_message', $message)
            ->click('@start_chat_modal_add_friends_btn')
            ->waitFor('@search_friends_box')
            ->assertVisible('@search_friends_input')
            ->type('@search_friends_input', $display_name)
            ->waitFor('@search_friend_item')
            ->click('@search_friend_item')
            ->assertSeeIn(
                '@start_chat_modal_recipients',
                $display_name
            )
            ->click('@start_chat_modal_btn')
            ->pause(ChatPage::CHAT_CREATION_WAIT_TIME * 1000)
            ->assertPathIs(DuskTestCase::makeAssertPath(route('chat.index')))
            ->pause(ChatPage::CHAT_CREATION_WAIT_TIME * 1000)
            ->waitForText($message)
            ->assertSeeIn(
                '@chat_thread_container:first-of-type @chat_message_preview',
                trans('chat.you') . ' ' . $message
            );
    }

    public function createMultipleChat(Browser $browser, $message, $term)
    {
        $browser->visit(route('front.my_profile'))
            ->waitFor('@start_chat_btn')
            ->click('@start_chat_btn')
            ->waitFor('@start_chat_modal')
            ->assertVisible('@start_chat_modal_message')
            ->assertVisible('@start_chat_modal_add_friends_btn')
            ->type('@start_chat_modal_message', $message)
            ->click('@start_chat_modal_add_friends_btn')
            ->waitFor('@search_friends_box')
            ->assertVisible('@search_friends_input')
            ->type('@search_friends_input', $term)
            ->waitFor('@search_friend_item')
            ->click('@search_friend_item:first-of-type')
            ->click('@search_friends_input')
            ->click('@search_friend_item:nth-of-type(2)')
            ->click('@start_chat_modal_btn')
            ->pause(ChatPage::CHAT_CREATION_WAIT_TIME * 1000)
            ->assertPathIs(DuskTestCase::makeAssertPath(route('chat.index')))
            ->pause(ChatPage::CHAT_CREATION_WAIT_TIME * 1000)
            ->assertSeeIn(
                '@chat_thread_container:first-of-type @chat_message_preview',
                trans('chat.you') . ' ' . $message
            );
    }
}
