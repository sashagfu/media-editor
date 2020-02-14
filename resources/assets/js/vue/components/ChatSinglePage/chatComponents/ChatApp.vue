<template>
  <div class="chat-messages">
    <div class="chat-messages__main columns is-gapless is-marginless">
      <div class="chat-messages__left-column column is-6">

        <!-- <a class="button small expanded start-chat-page"
                   href="/chat/create"
                >
                  {{ trans('chat.create') }}
                </a> -->
        <div
          v-for="(thread, key) in threads"
          :class="{
            'side-messages__active': thread.id === activeThread.id,
            'side-messages__unread': thread.isUnread === true
          }"
          :key="key"
          class="side-messages"
          @click="fetchThread(thread)"
        >
          <div class="side-messages__left">
            <user-ava :ava="thread.avatar"/>
            <div class="side-messages__text-box">
              <div class="side-messages__title">
                {{ thread.conversationer }}
              </div>
              <div class="side-messages__text">
                {{ thread.latestAuthor }} {{ thread.latestMessage }}
              </div>
              <!--<div class="side-messages__time">-->
              <!--{{message.messages[message.messages.length-1].date}}-->
              <!--{{message.messages[message.messages.length-1].time}}</div>-->
            </div>
          </div>
          <div class="side-messages__right">
            <div class="side-messages__pic"/>
          </div>
        </div>
        <infinite-loading
          ref="scrollThreads"
          @infinite="scrollThreads"
        >
          <span slot="no-more"/>
        </infinite-loading>
        <p
          v-if="$refs.scrollThreads
            && $refs.scrollThreads.isComplete
          && !threads.length"
        >
          There are no threads yet. You
          can create them from <a href="/profile">profile page</a>
        </p>
      </div>
      <div class="chat-messages__right-column column is-6">
        <div class="conversation__main">
          <infinite-loading
            ref="messagesLoading"
            :distance="10"
            direction="top"
            @infinite="scrollMessages"
          >
            <span slot="no-more"/>
            <span slot="no-results"/>
          </infinite-loading>
          <div
            v-for="(message, key) in activeThreadMessages"
            :key="key"
            class="conversation-item"
          >
            <user-ava :ava="message.user.avatar"/>
            <div class="conversation-item__main">
              <div class="conversation-item__head-line">
                <div class="conversation-item__title">
                  {{ message.user.display_name }}
                </div>
                <div class="conversation-item__time">
                  {{ message.created_at }}
                </div>
              </div>
              <div
                class="conversation-item__text"
                v-html="message.body"
              />
            </div>
          </div>
        </div>
        <div class="conversation__bottom">
          <div class="messenger">
            <form
              action=""
              class="chat-from-send"
              method="POST"
              @submit.prevent="sendMessage(activeThread)"
            >
              <div class="messenger__input">
                <div class="input-box__form">
                  <textarea
                    v-model="newMessage.message"
                    class="input-box__input input-box__input--textarea"
                    placeholder="Write your reply here..."
                    @keydown="messageHandler"
                  />
                </div>
              </div>
              <div class="messenger__buttons-line">
                <div class="messenger__buttons-left">
                  <button
                    class="messenger__button
                                                   messenger__button--add
                                                   messenger__button--photo"
                  />
                  <button
                    class="messenger__button
                                                   messenger__button--add
                                                   messenger__button--emoji"
                  />
                  <button
                    class="messenger__button
                                                   messenger__button--add
                                                   messenger__button--stickers"
                  />
                </div>
                <div class="messenger__buttons-right">
                  <button
                    :disabled="newMessage.message.length === 0"
                    type="submit"
                    class="messenger__button messenger__button--save"
                  >
                    {{ trans('chat.send') }}
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
/* @flow */
import InfiniteLoading from 'vue-infinite-loading';
import { mapActions } from 'vuex';
import _ from 'lodash';
import UserAva from '../../UserAva/UserAva';

export default {
  components: {
    UserAva,
    InfiniteLoading,
  },
  data() {
    return {
      threads: [],
      activeThread: {},
      activeThreadMessages: [],
      newMessage: { message: '' },
      threadNextPageUrl: '',
      messagesNextPageUrl: null,
      newMessageThreadId: false,
      emptyMessagesText: '',
      sendError: false,
    };
  },
  mounted() {
    this.fetchLastThread();
    this.setChatPage(true);
    // Listen for events for real time
    // window.Echo.private(`ChatBroadcaster.${window.Laravel.user_id}`)
    //     .listen('ChatMessageSent', (data) => {
    //         // Append message if user is viewing it's thread
    //         if (parseInt(data.message.thread_id, 10)
    // === parseInt(this.activeThread.id, 10)) {
    //             this.activeThreadMessages.push(data.message);
    //             this.$nextTick(() => {
    //                 this.scrollToBottom('.conversation__main');
    //             });
    //         }
    //         // Also insert message preview in threads container
    //         const thread = _.find(this.threads, (thrd) => {
    //             const newId = parseInt(data.message.thread_id, 10);
    //             return thrd.id === newId;
    //         });
    //         thread.latestAuthor = '';
    //         thread.latestMessage = data.latest_message;
    //         thread.isUnread = true;
    //         this.newMessageThreadId = parseInt(data.message.thread_id, 10);
    //     });
  },
  methods: {
    ...mapActions('chat', [
      'setChatPage',
      'setUnreadMessagesCount',
    ]),
    scrollThreads() {
      const link = this.threadNextPageUrl ? this.threadNextPageUrl : '/api/chat/threads';
      if (this.threadNextPageUrl === null) {
        this.$refs.scrollThreads.$emit('$InfiniteLoading:complete');
        return;
      }
      this.$http.get(link).then((response) => {
        this.threads = this.threads.concat(response.data.data);
        this.threadNextPageUrl = response.data.next_page_url;
        this.$refs.scrollThreads.$emit('$InfiniteLoading:loaded');
      });
    },
    // Make messages scrollable
    scrollMessages() {
      if (this.messagesNextPageUrl === null || !this.threads.length) {
        this.$refs.messagesLoading.$emit('$InfiniteLoading:complete');
        return;
      }
      const link = this.messagesNextPageUrl ? this.messagesNextPageUrl : `/api/chat/threads/${this.activeThread.id}/messages`;
      this.$http.get(link)
        .then((response) => {
          const messages = response.data.data.reverse();
          this.activeThreadMessages = messages.concat(this.activeThreadMessages);
          this.messagesNextPageUrl = response.data.next_page_url;
          this.$nextTick(() => {
            this.$refs.messagesLoading.$emit('$InfiniteLoading:loaded');
            if (response.data.current_page === 1) {
              this.scrollToBottom('.conversation__main');
            } else {
              this.$el.querySelector('.conversation__main').scrollTop = 100;
            }
          });
        });
    },
    // Fetch last active thread loaded
    fetchLastThread() {
      this.$http.get('/api/chat/lastThread').then((response) => {
        this.$set(this, 'activeThread', response.data);
        this.messagesNextPageUrl = '';
        this.$refs.messagesLoading.$emit('$InfiniteLoading:reset');
        this.$set(this.activeThread, 'isUnread', false);
      });
    },
    // Fetch thread after click on it
    fetchThread(thread) {
      if (thread.id === this.activeThreadId) {
        return false;
      }
      this.$set(this, 'activeThread', thread);
      this.activeThreadMessages = [];
      this.messagesNextPageUrl = '';
      this.$refs.messagesLoading.$emit('$InfiniteLoading:reset');
      this.$set(thread, 'isUnread', false);
      return true;
    },
    // Send message to thread
    sendMessage(activeThread) {
      this.sendError = false;
      this.$http.post(`/api/chat/threads/${activeThread.id}`, {
        _token: window.Laravel.csrfToken,
        message: this.newMessage.message,
      })
        .then((response) => {
          const { message } = response.data;
          this.activeThreadMessages.push(message);
          this.newMessage.message = '';
          this.$nextTick(() => {
            this.scrollToBottom('.conversation__main');
          });
          const Thread = _.find(this.threads, e => e.id === activeThread.id);
          this.$set(Thread, 'latestAuthor', this.trans('chat.you'));
          this.$set(Thread, 'latestMessage', message.body);
          this.$set(Thread, 'isUnread', false);
          this.setUnreadMessagesCount(); // $store.dispatch
        }, () => { // response
          this.sendError = true;
          this.scrollToBottom('.conversation__main');
        });
    },
    messageHandler(e) {
      if (e.keyCode === 13 && !e.shiftKey) {
        e.preventDefault();
        this.sendMessage(this.activeThread);
      }
    },
    // Scroll element to bottom
    scrollToBottom(selector) {
      const container = this.$el.querySelector(selector);
      container.scrollTop = container.scrollHeight;
    },

  },
};
</script>

<style
  lang="stylus"
  scoped
>
    @import '../../../../../sass/front/components/bulma-theme';

    .chat-messages {
        justify-items: flex-start;
        align-items: flex-start;
        height: 100%;
        &__main {
            justify-content: flex-start;
            align-items: flex-start;
            height: 100%;
        }
        &__left-column {
            border-right: 1px solid $border;
            overflow-y: auto;
            justify-content: normal;
            height: 100%;
            background: $white;
        }
        &__right-column {
            position: relative;
            padding-right: 75px !important;
            justify-content: normal;
            height: 100%;
            background: $white;
        }
    }

    .side-messages {
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        padding: 22px 26px;
        border-bottom: 1px solid $border;
        &__active {
            background-color: $grey-lighter;
        }
        &__unread {
            background-color: $grey-light;
        }
        &__left {
            display: flex;
        }
        &__right {
            display: flex;
            justify-content: flex-end;
        }
        &__text-box {
            display: flex;
            flex-direction: column;
            padding: 0 16px;
        }
        &__title {
            fnt($text, 14px, $weight-bold, left);
        }
        &__text {
            fnt($text-light, 12px, $weight-normal, left);
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            padding: 4px 0;
            width: 400px;
        }
        &__time {
            fnt($text-light, 11px, $weight-normal, left);
        }
        &__pic {
            height: 36px;
            width: 36px;
            background: url('../../../../../img/chat-мessages-icon--light.png')
                center center no-repeat;
        }

    }

    .conversation {
        &__main {
            padding: 13px 0;
            height: 100vh;
            overflow-y: scroll;
            border-bottom: 1px solid $border;
        }
        &__bottom {
            position: relative;
            width: 100%;
        }
    }

    .conversation-item {
        padding: 12px 26px;
        display: flex;
        &__main {
            display: flex;
            flex-direction: column;
            padding-left: 16px;
        }
        &__head-line {
            display: flex;
            justify-content: space-between;
            padding: 0 0 12px 0;
        }
        &__title {
            fnt($text, 14px, $weight-bold, left);
        }
        &__time {
            fnt($text-light, 10px, $weight-normal, right);
        }
        &__text {
            fnt($text-light, 13px, $weight-normal, left);
        }
    }

    .messenger {
        display: flex;
        flex-direction: column;
        &__input {
            display: flex;
            flex-direction: column;
            padding: 8px 26px;
            border-bottom: 1px solid $border;
        }
        &__buttons-line {
            height: 56px;
            padding: 0 26px;
            display: flex;
            justify-content: space-between;
        }
        &__buttons-left {
            display: flex;
            align-items: center;
            width: 50%;
        }
        &__buttons-right {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            width: 50%;
        }
        &__button {
            fnt($text-invert, 11px, $weight-semibold, center);
            border-radius: 3px;
            cursor: pointer;
            height: 32px;
            outline: none;
            transition: all .3s;
            &--restore {
                border: 1px solid $grey;
                background-color: $grey;
                &:hover {

                    background-color: transparent;
                    color: $grey;
                }
            }
            &--save {
                background-color: $primary;
                border: 1px solid $primary;
                padding: 0 26px;
                &:hover {
                    background-color: transparent;
                    color: $primary;
                }
            }
            &--add {
                background: center center no-repeat transparent;
                border: none;
                width: 36px;
            }
            &--stickers {
                background-image: url('../../../../../img/happy-sticker-icon--light.png');
                &:hover {
                    background-image: url('../../../../../img/happy-sticker-icon--dark.png');
                }
            }
            &--emoji {
                background-image: url('../../../../../img/happy-face-icon--light.png');
                &:hover {
                    background-image: url('../../../../../img/happy-face-icon--dark.png');
                }
            }
            &--photo {
                background-image: url('../../../../../img/camera-icon--light.png');
                &:hover {
                    background-image: url('../../../../../img/camera-icon--dark.png');
                }
            }
        }
    }

    .input-box {
        align-items: center;
        background-color: $background-light;
        border-radius: 3px;
        border: 1px solid $border;
        display: flex;
        justify-content: space-between;
        padding-left: 18px;
        &--is-success {
            border-color: $success;
        }
        &--is-danger {
            border-color: $danger;
        }
        &--column-first {
            margin-bottom: 10px;
        }
        &--column {
            margin: 10px 0;
        }
        &--column-last {
            margin-top: 10px;
        }
        &__form {
            display: flex;
            flex-direction: column;
            width: 100%;
        }
        &__label {
            fnt($text-light, 10px, $weight-normal, left);
            align-items: flex-start;
            display: flex;
            flex-direction: column;
            height: 20px;
            justify-content: flex-end;
        }
        &__input {
            fnt($text-light, 12px, $weight-normal, left);
            background-color: $background-light;
            border: none;
            height: 32px;
            outline: none;
            width: 100%;
            &--info {
                fnt($info, 12px, $weight-normal, left);
            }
            &--textarea {
                resize: none;
                padding: 8px 0;
                height: 106px;
            }
        }
        &__button {
            border-radius: 0 3px 3px 0;
            border: none;
            cursor: pointer;
            flex: 0 0 auto;
            height: 52px;
            outline: none;
            transition: all .3s;
            width: 48px;
            &--none {
                opacity: 0;
            }
            &--angle {
                align-items: center;
                background-color: $white;
                display: flex;
                justify-content: center;
            }
            &--account {
                align-items: center;
                background-color: $white;
                border-right: 1px solid $border;
                display: flex;
                justify-content: center;
                margin: 0 12px 0 -18px;
                width: 54px;
            }
            &--date {
                background: url('../../../../../img/month-calendar-icon--light.png')
                    center center no-repeat $white;
                &:hover {
                    background: url('../../../../../img/month-calendar-icon--dark.png')
                        $background-hover;
                }
            }
            &--add-ava {
                background: url('../../../../../img/computer-icon--light.png')
                center center no-repeat $white;
                &:hover {
                    background: url('../../../../../img/computer-icon--dark.png')
                        $background-hover;
                }
                &:active {
                    background-image: url('../../../../../img/computer-icon--blue.png');
                }
            }
            &--add-friends {
                background: url('../../../../../img/happy-face-icon--light.png')
                    center center no-repeat $white;
                &:hover {
                    background: url('../../../../../img/happy-face-icon--dark.png')
                        $background-hover;
                }
                &:active {
                    background-image: url('../../../../../img/happy-face-icon--blue.png');
                }
            }
        }
    }
</style>
