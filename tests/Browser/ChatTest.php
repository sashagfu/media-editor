<?php
namespace Tests\Browser;

use Tests\DuskTestCase;
use App\Models\User;
use Tests\Browser\Pages\ChatPage;

class ChatTest extends DuskTestCase
{
    public function testIfUserCanAccessСhatPage()
    {
        $this->browse(function ($admin) {
            $admin->loginAs(User::find(ChatPage::ADMIN_ID))
                ->visit(new ChatPage())
                ->pause(ChatPage::MAX_CHAT_WAIT_TIME * 1000)
                ->assertVisible('@start_btn');
        });
    }

    public function testIfUserCanOpenDropdownAnywhereExceptChatPage()
    {
        $this->browse(function ($admin) {
            $admin->loginAs(User::find(ChatPage::ADMIN_ID))
                ->visit(new ChatPage())
                ->visit(route('performances.index'))
                ->click('@chat_dropdown_btn');
                $admin->waitFor('@chat_menu_box_item');
            if (User::find(ChatPage::ADMIN_ID)->threads) {
                $admin->waitFor('@chat_menu_thread_item');
            }
                $admin->visit(route('chat.index'))
                ->click('@chat_dropdown_btn')
                ->assertMissing('@chat_menu_box_item');
            if (User::find(ChatPage::ADMIN_ID)->threads) {
                $admin->assertMissing('@chat_menu_thread_item');
            }
        });
    }

    public function testIfUserCanCreateNewThread()
    {
        $this->browse(function ($admin) {
            $message = 'New message for new chat';
            $search_term = 'Chat tester';
            $admin->loginAs(User::find(ChatPage::ADMIN_ID))
                ->visit(new ChatPage())
                ->visit(route('front.my_profile'))
                ->waitFor('@start_chat_btn')
                ->click('@start_chat_btn')
                ->waitFor('@start_chat_modal')
                ->assertVisible('@start_chat_modal_message')
                ->assertVisible('@start_chat_modal_add_friends_btn')
                ->type('@start_chat_modal_message', $message)
                ->click('@start_chat_modal_add_friends_btn')
                ->waitFor('@search_friends_box')
                ->assertVisible('@search_friends_input')
                ->type('@search_friends_input', $search_term)
                ->waitFor('@search_friend_item')
                ->click('@search_friend_item')
                ->assertSeeIn(
                    '@start_chat_modal_recipients',
                    $search_term
                )
                ->click('@start_chat_modal_btn')
                ->pause(ChatPage::CHAT_CREATION_WAIT_TIME * 1000)
                ->assertPathIs(DuskTestCase::makeAssertPath(route('chat.index')))
                ->pause(ChatPage::CHAT_CREATION_WAIT_TIME * 1000)
                ->assertSeeIn(
                    '@chat_thread_container:first-of-type @chat_message_preview',
                    trans('chat.you') . ' ' . $message
                );
        });
    }

    public function testChatCreationValidation()
    {
        $this->browse(function ($admin) {
            $admin->loginAs(User::find(ChatPage::ADMIN_ID))
                ->visit(new ChatPage())
                ->visit(route('front.my_profile'))
                ->waitFor('@start_chat_btn')
                ->click('@start_chat_btn')
                ->waitFor('@start_chat_modal')
                ->assertVisible('@start_chat_modal_message')
                ->assertVisible('@start_chat_modal_add_friends_btn')
                ->click('@start_chat_modal_btn')
                ->assertPathIs(DuskTestCase::makeAssertPath(route('front.my_profile')))
                ->type('@start_chat_modal_message', '')
                ->click('@start_chat_modal_btn')
                ->waitForText(trans('validation.required', ['attribute' => 'message']))
                ->waitForText(trans('validation.required', ['attribute' => 'recipients']));
        });
    }

    public function testIfUserCanCreateMultiUserChat()
    {
        $this->browse(function ($admin) {
            $message = 'New message for new multiple chat';
            $search_term = 'tester';
            $names = 'Chat tester, Dummy tester';
            $admin->loginAs(User::find(ChatPage::ADMIN_ID))
                ->visit(new ChatPage())
                ->visit(route('front.my_profile'))
                ->waitFor('@start_chat_btn')
                ->click('@start_chat_btn')
                ->waitFor('@start_chat_modal')
                ->assertVisible('@start_chat_modal_message')
                ->assertVisible('@start_chat_modal_add_friends_btn')
                ->type('@start_chat_modal_message', $message)
                ->click('@start_chat_modal_add_friends_btn')
                ->waitFor('@search_friends_box')
                ->assertVisible('@search_friends_input')
                ->type('@search_friends_input', $search_term)
                ->waitFor('@search_friend_item')
                ->click('@search_friend_item:first-of-type')
                ->click('@search_friends_input')
                ->click('@search_friend_item:nth-of-type(2)')
                ->assertSeeIn(
                    '@start_chat_modal_recipients',
                    $names
                )
                ->click('@start_chat_modal_btn')
                ->pause(ChatPage::CHAT_CREATION_WAIT_TIME * 1000)
                ->assertPathIs(DuskTestCase::makeAssertPath(route('chat.index')))
                ->pause(ChatPage::CHAT_CREATION_WAIT_TIME * 1000)
                ->assertSeeIn(
                    '@chat_thread_container:first-of-type @chat_message_preview',
                    trans('chat.you') . ' ' . $message
                );
        });
    }

    public function testIfUserCanSendMessageInChat()
    {
        $this->browse(function ($admin) {
            $message = 'test message';
            $new_message = 'new message for chat';
            $display_name = User::find(3)->display_name;

            $admin->loginAs(User::find(ChatPage::ADMIN_ID))
                ->visit(new ChatPage())
                ->createChat($message, $display_name)
                ->pause(ChatPage::MAX_CHAT_WAIT_TIME * 1000)
                ->assertSeeIn(
                    '@chat_thread_container:first-of-type @chat_message_preview',
                    trans('chat.you') . ' ' . $message
                )
                ->type('@messenger_input', $new_message);
                $admin->driver->executeScript('window.scrollTo(0,document.body.scrollHeight)');
                $admin->click('@messages_container_send_btn')
                ->assertDontSee(trans('common.error_reload'))
                ->pause(ChatPage::MAX_MESSAGE_CREATION_TIME * 1000)
                ->assertSeeIn(
                    '@chat_thread_container:first-of-type @chat_message_preview',
                    $new_message
                );
        });
    }

    public function testIfChatMessagesAreRealTimeForTwoUsers()
    {
        $this->browse(function ($admin, $tester) {
            $message = 'First initial message';
            $display_name = User::find(ChatPage::ANOTHER_CHAT_TESTER_ID)->display_name;

            $admin->loginAs(User::find(ChatPage::ADMIN_ID))
                ->visit(new ChatPage)
                ->createChat($message, $display_name);
            $tester->loginAs(User::find(ChatPage::ANOTHER_CHAT_TESTER_ID))
                ->visit(new ChatPage)
                ->pause(ChatPage::MAX_CHAT_WAIT_TIME * 1000)
                ->assertSeeIn(
                    '@messages_container @single_message_container:last-of-type @single_message',
                    $message
                );
            $new_message_1 = 'New message to test';
            $admin->visit(route('chat.index'))
                ->pause(ChatPage::MAX_CHAT_WAIT_TIME * 1000)
                ->type('@messenger_input', $new_message_1);
                $admin->driver->executeScript('window.scrollTo(0,document.body.scrollHeight)');
                $admin->click('@messages_container_send_btn')
                ->waitForText($new_message_1, ChatPage::MAX_MESSAGE_CREATION_TIME);
            $new_message_2 = 'Now tester creates message';
            $tester->waitForText($new_message_1)
                ->type('@messenger_input', $new_message_2);
            $tester->driver->executeScript('window.scrollTo(0,document.body.scrollHeight)');
            $tester->click('@messages_container_send_btn')
                ->waitForText($new_message_2, ChatPage::MAX_MESSAGE_CREATION_TIME);
            $admin->waitForText($new_message_2);
        });
    }

    public function testIfChatMessagesAreRealTimeMultipleUsers()
    {
        $this->browse(function ($admin, $first_tester, $second_tester) {
            $message = 'First initial multi user message';
            $search_term = 'tester';
            $third_user_id = 4;

            $admin->loginAs(User::find(ChatPage::ADMIN_ID))
                ->visit(new ChatPage)
                ->createMultipleChat($message, $search_term);

            $first_tester->loginAs(User::find(ChatPage::ANOTHER_CHAT_TESTER_ID))
                ->visit(new ChatPage)
                ->pause(ChatPage::MAX_CHAT_WAIT_TIME * 1000)
                ->assertSeeIn(
                    '@messages_container @single_message_container:last-of-type @single_message',
                    $message
                );
            $second_tester->loginAs(User::find($third_user_id))
                ->visit(new ChatPage)
                ->pause(ChatPage::MAX_CHAT_WAIT_TIME * 1000)
                ->assertSeeIn(
                    '@messages_container @single_message_container:last-of-type @single_message',
                    $message
                );
            $new_message_1 = 'New message to multi user chat test';
            $admin->visit(route('chat.index'))
                ->pause(3000)
                ->type('@messenger_input', $new_message_1);
            $admin->driver->executeScript('window.scrollTo(0,document.body.scrollHeight)');
            $admin->click('@messages_container_send_btn')
                ->waitForText($new_message_1, ChatPage::MAX_MESSAGE_CREATION_TIME);
            $first_tester->waitForText($new_message_1);
            $second_tester->waitForText($new_message_1);
            $new_message_2 = 'Now user with id 3 writes message';
            $first_tester->type('@messenger_input', $new_message_2);
            $first_tester->driver->executeScript('window.scrollTo(0,document.body.scrollHeight)');
            $first_tester->click('@messages_container_send_btn')
                ->waitForText($new_message_2, ChatPage::MAX_MESSAGE_CREATION_TIME);
            $admin->waitForText($new_message_2);
            $second_tester->waitForText($new_message_2);

            $new_message_3 = 'Now user with id 4 writes message';
            $second_tester->type('@messenger_input', $new_message_3);
            $second_tester->driver->executeScript('window.scrollTo(0,document.body.scrollHeight)');
            $second_tester->click('@messages_container_send_btn')
                ->waitForText($new_message_3, ChatPage::MAX_MESSAGE_CREATION_TIME);
            $admin->waitForText($new_message_3);
            $first_tester->waitForText($new_message_3);

            $admin->waitForText($new_message_2);
        });
    }

    public function testIfUserCanSwitchChats()
    {
        $this->browse(function ($admin) {
            $message_1 = 'test message';
            $message_2 = 'test message 2';
            $display_name_1 = User::find(ChatPage::ANOTHER_CHAT_TESTER_ID)->display_name;
            $display_name_2 = User::find(ChatPage::SECOND_CHAT_TESTER_ID)->display_name;

            $admin->loginAs(User::find(ChatPage::ADMIN_ID))
                ->visit(new ChatPage)
                ->createChat($message_1, $display_name_1)
                ->assertSeeIn(
                    '@messages_container @single_message_container:last-of-type @single_message',
                    $message_1
                )
                ->createChat($message_2, $display_name_2)
                ->assertSeeIn(
                    '@messages_container @single_message_container:last-of-type @single_message',
                    $message_2
                )
                ->click('@chat_thread_container:nth-of-type(2)')
                ->pause(ChatPage::MAX_CHAT_WAIT_TIME * 1000)
                ->assertDontSeeIn('@messages_container', $message_2)
                ->assertSeeIn('@messages_container', $message_1);
        });
    }

    public function testIfUserCanSeeUnreadMessagesCountUpdating()
    {
        $this->browse(function ($admin, $tester) {
            $message = 'test message initial';
            $new_message = 'new message for chat update count';
            $new_message_2 = 'new message for chat update count 2';
            $new_message_3 = 'new message for chat update count 3';
            $display_name = User::find(ChatPage::ADMIN_ID)->display_name;

            $tester->loginAs(User::find(ChatPage::ANOTHER_CHAT_TESTER_ID))
                ->visit(new ChatPage)
                ->createChat($message, $display_name)
                ->pause(ChatPage::MAX_CHAT_WAIT_TIME * 1000)
                ->assertSeeIn(
                    '@messages_container @single_message_container:last-of-type @single_message',
                    $message
                );
            $admin->loginAs(User::find(ChatPage::ADMIN_ID))
                ->visit(new ChatPage())
                ->pause(ChatPage::MAX_CHAT_WAIT_TIME * 1000)
                ->assertSeeIn(
                    '@messages_container @single_message_container:last-of-type @single_message',
                    $message
                )
                ->pause(5000);
            $admin_before_send_unread_count = User::find(ChatPage::ADMIN_ID)->unreadMessagesCount;
            if ($admin_before_send_unread_count != 0) {
                $admin->assertSeeIn(
                    '@chat_unread_count',
                    $admin_before_send_unread_count
                );
            }
            $tester->type('@messenger_input', $new_message);
            $tester->driver->executeScript('window.scrollTo(0,document.body.scrollHeight)');
            $tester->click('@messages_container_send_btn')
                ->assertDontSee(trans('common.error_reload'))
                ->assertSeeIn('@messages_container', $message);

            $admin->pause(3000)
                ->assertSeeIn(
                    '@chat_unread_count',
                    $admin_before_send_unread_count + 1
                );

            $tester->type('@messenger_input', $new_message_2);
            $tester->driver->executeScript('window.scrollTo(0,document.body.scrollHeight)');
            $tester->click('@messages_container_send_btn')
                ->pause(ChatPage::MAX_MESSAGE_CREATION_TIME * 1000 + 2000)
                ->assertSeeIn(
                    '@messages_container @single_message_container:last-of-type @single_message',
                    $new_message_2
                );

            $admin->pause(3000)
                ->assertSeeIn(
                    '@chat_unread_count',
                    $admin_before_send_unread_count + 2
                );
            $admin->type('@messenger_input', $new_message_3);
            $admin->driver->executeScript('window.scrollTo(0,document.body.scrollHeight)');
            $admin->click('@messages_container_send_btn')
                ->pause(ChatPage::MAX_MESSAGE_CREATION_TIME * 1000)
                ->assertSeeIn(
                    '@messages_container @single_message_container:last-of-type @single_message',
                    $new_message_3
                )
            ->pause(2000);
            if ($admin_before_send_unread_count != 0) {
                $admin->driver->executeScript('window.scrollTo(0,0)');
                $admin->assertSeeIn(
                    '@chat_unread_count',
                    $admin_before_send_unread_count - 1
                );
            }
        });
    }

    public function testIfUserCanOpenThreadFromDropdownMenu()
    {
        $this->browse(function ($admin) {
            $message = 'test chat from dropdown';
            $display_name = User::find(ChatPage::ANOTHER_CHAT_TESTER_ID)->display_name;
            $admin->loginAs(User::find(ChatPage::ADMIN_ID))
                ->visit(new ChatPage)
                ->createChat($message, $display_name)
                ->visit(route('performances.index'))
                ->click('@chat_dropdown_btn')
                ->pause(ChatPage::CHAT_CREATION_WAIT_TIME * 1000)
                ->assertVisible('@chat_menu_box_item')
                ->assertVisible('@chat_menu_thread_item')
                ->click('@chat_menu_thread_item:first-of-type')
                ->assertVisible('@chat_widget')
                ->pause(ChatPage::MAX_CHAT_WAIT_TIME * 1000)
                ->click('@messenger_input')
                ->assertSee($message);
        });
    }

    public function testIfUserCanChatRealtimeFromWidget()
    {
        $this->browse(function ($admin, $tester) {
            $message = 'test realtime chat from dropdown';
            $message_2 = 'test notification';
            $display_name = User::find(ChatPage::ANOTHER_CHAT_TESTER_ID)->display_name;

            $admin->loginAs(User::find(ChatPage::ADMIN_ID))
                ->visit(new ChatPage)
                ->createChat($message, $display_name)
                ->visit(route('performances.index'))
                ->click('@chat_dropdown_btn')
                ->pause(ChatPage::MAX_CHAT_WAIT_TIME * 1000)
                ->assertVisible('@chat_menu_box_item')
                ->assertVisible('@chat_menu_thread_item')
                ->click('@chat_menu_thread_item:first-of-type')
                ->pause(ChatPage::MAX_CHAT_WAIT_TIME * 1000)
                ->assertVisible('@chat_widget')
                ->pause(ChatPage::MAX_CHAT_WAIT_TIME * 1000)
                ->click('@messenger_input');
            $admin->driver->executeScript('window.scrollTo(0,0)');
            $admin->assertSee($message);
            $tester->loginAs(User::find(ChatPage::ANOTHER_CHAT_TESTER_ID))
                ->visit(new ChatPage)
                ->visit(route('performances.index'))
                ->click('@chat_dropdown_btn')
                ->pause(ChatPage::MAX_CHAT_WAIT_TIME * 1000)
                ->assertVisible('@chat_menu_box_item')
                ->assertVisible('@chat_menu_thread_item')
                ->click('@chat_menu_thread_item:first-of-type')
                ->pause(ChatPage::MAX_CHAT_WAIT_TIME * 1000)
                ->assertVisible('@chat_widget')
                ->pause(ChatPage::MAX_CHAT_WAIT_TIME * 1000)
                ->click('@messenger_input');
            $tester->driver->executeScript('window.scrollTo(0,0)');
            $tester->assertSee($message);
            $admin->type('@messenger_input', $message_2);
            $admin->driver->executeScript('window.scrollTo(0,document.body.scrollHeight)');
            $admin->click('@messages_container_send_btn')
                ->assertDontSee(trans('common.error_reload'))
                ->pause(ChatPage::MAX_MESSAGE_CREATION_TIME * 1000)
                ->assertSee($message_2);
            $tester->pause(2000)
                ->assertSee($message_2);
        });
    }

    public function testIfUserCannotCreateTwoIdenticalTreads()
    {
        $this->browse(function ($admin) {
            $message = 'New message for new chat';
            $message_2 = 'New message for new chat 2';
            $display_name = User::find(ChatPage::ANOTHER_CHAT_TESTER_ID)->display_name;

            $admin->loginAs(User::find(ChatPage::ADMIN_ID))
                ->visit(new ChatPage)
                ->createChat($message, $display_name)
                ->pause(ChatPage::MAX_CHAT_WAIT_TIME * 1000)
                ->assertSeeIn(
                    '@messages_container @single_message_container:last-of-type @single_message',
                    $message
                )
                ->visit(new ChatPage)
                ->createChat($message_2, $display_name)
                ->pause(ChatPage::MAX_CHAT_WAIT_TIME * 1000)
                ->assertSeeIn(
                    '@messages_container @single_message_container:last-of-type @single_message',
                    $message_2
                );
        });
    }
}
