<template>
  <transition name="component-translate">
    <div
      :class="[
        'ChatWidget',
        'cw',
        {'cw--narrow': isNarrow},
      ]"
    >
      <div class="cw__header">
        <div class="cw__left">
          <div class="cwh__user-box">
            <template v-if="countOfUsers < 2">
              <Avatar
                v-if="user.avatar.includes('/profile/default/avatar')"
                :size="36"
                :username="user.displayName || user.display_name"
                custom-class="cwh__avatar"
                @click="goToProfile"
              />
              <div
                v-else
                :style="{'background-image': `url(${user.avatar})`}"
                class="cwh__avatar"
                @click="goToProfile"
              />
              <div
                class="cwh__title-box"
                @click="goToProfile"
              >
                <div class="cwh__title">
                  {{ user.displayName ? user.displayName : user.display_name }}
                </div>
                <div class="cwh__sub-title">
                  @{{ user.username }}
                </div>
              </div>
            </template>
            <template v-else>
              <MultiUserAvatar
                :active-user="activeUser"
                :users="thread.users"
                :thread-id="thread.id"
              />
              <div
                v-if="showInput"
                class="cwh__input-box"
              >
                <input
                  v-model="inputName"
                  type="text"
                  placeholder="Input name of thread"
                  class="cwh__input"
                  @keyup.esc="closeNameInput"
                  @keyup.enter="updateThread"
                >
                <div
                  class="cwh__input-btn"
                  @click="closeNameInput"
                >
                  <font-awesome-icon
                    v-if="loadingName"
                    :icon="['fas', 'spinner']"
                    spin
                    class="fa-icon fa-icon--inp"
                  />
                  <font-awesome-icon
                    v-else
                    :icon="['fas', 'times']"
                    class="fa-icon fa-icon--inp"
                  />
                </div>
              </div>
              <div
                v-else-if="thread.name"
                class="cwh__title-box"
                @dblclick="openNameInput"
              >
                <div class="cwh__title">
                  {{ thread.name }}
                </div>
                <div class="cwh__sub-title">
                  chat with {{ thread.users.length }} participants
                </div>
              </div>
              <div
                v-else
                class="cwh__title-box"
                @dblclick="openNameInput"
              >
                <div class="cwh__title">
                  {{ user.displayName }}
                </div>
                <div class="cwh__sub-title">
                  and {{ countOfUsers }} participants
                </div>
              </div>
            </template>
          </div>
        </div>
        <div class="cw__right">
          <div class="cwh__btn">
            <AddParticipant
              :active-user="activeUser"
              :thread="thread"
            />
          </div>
          <div
            class="cwh__btn"
            @click="closeChat"
          >
            <font-awesome-icon
              :icon="['fas', 'times']"
              class="fa-icon fa-icon--search"
            />
          </div>
        </div>
      </div>
      <div
        ref="chatWidgetContent"
        class="cw__main"
      >
        <div
          v-if="$apollo.queries.fetchMessages.loading"
          class="cw__loading"
        >
          <font-awesome-icon
            :icon="['fas', 'spinner']"
            spin
            class="fa-icon fa-icon--big"
          />
        </div>
        <div
          v-else-if="!fetchMessages.length"
          class="cw__empty"
        >
          {{ trans('chat.have_not_messages') }}
        </div>
        <template
          v-for="(message, index) in fetchMessages"
          v-else
        >
          <ChatWidgetMessage
            :key="`ms-${message.id}`"
            :message="message"
            :active-user="activeUser"
            :messages="fetchMessages"
            :loading-id="loadingId"
            :idx="index"
          />
        </template>
      </div>
      <div class="cw__footer">
        <form
          class="cwf__form"
          @submit.prevent="handleSendMessage()"
        >
          <div class="cwf__input el-textarea">
            <textarea
              v-model="message"
              rows="1"
              placeholder="Please enter to message..."
              class="el-textarea__inner"
              @focus="onFocus"
              @blur="ifBlur"
              @keydown.enter.exact.prevent
              @keyup.enter.exact="handleSendMessage"
              @keydown.enter.shift.exact="newline"
            />
          </div>
          <button
            :class="[
              'cwf__button',
              {'cwf__button--is-active': messageUploading},
              {'cwf__button--is-active': isActive}
            ]"
          >
            <font-awesome-icon
              :icon="['fas', 'paper-plane']"
              class="fa-icon fa-icon--invert"
            />
          </button>
          <template v-if="errors.commentText">
            <div
              v-for="(error, key) in errors.commentText"
              :key="key"
              class="cwf__error cwf__error--bottom"
              @click="skipError('comment_text', key)"
            >
              {{ error }}
            </div>
          </template>
        </form>
      </div>
    </div>
  </transition>

</template>

<script>
import moment from 'moment';
import { mapGetters } from 'vuex';
import { has } from 'lodash';

import CLOSE_THREAD from 'Gql/messages/mutations/closeThread.graphql';
import FETCH_MESSAGES from 'Gql/messages/queries/fetchMessages.graphql';
import FETCH_THREADS from 'Gql/messages/queries/fetchThreads.graphql';
import SUBSCRIPTION_MESSAGE_CREATED from 'Gql/messages/subscriptions/messageCreated.graphql';
import SUBSCRIPTION_MESSAGE_DELETED from 'Gql/messages/subscriptions/messageDeleted.graphql';
import SUBSCRIPTION_MESSAGE_UPDATED from 'Gql/messages/subscriptions/messageUpdated.graphql';

import CREATE_MESSAGE from 'Gql/messages/mutations/createMessage.graphql';
import DELETE_MESSAGE from 'Gql/messages/mutations/deleteMessage.graphql';
import READ_MESSAGES from 'Gql/messages/mutations/markThreadRead.graphql';
import UPDATE_MESSAGE from 'Gql/messages/mutations/updateMessage.graphql';
import UPDATE_THREAD from 'Gql/messages/mutations/updateThread.graphql';

import Avatar from 'Pages/ProfilePage/Avatar';

import ActionBox from './ActionBox';
import AddParticipant from './AddParticipant';
import ChatWidgetMessage from './ChatWidgetMessage';
import MultiUserAvatar from './MultiUserAvatar';

export default {
  name: 'ChatWidget',
  components: {
    ActionBox,
    AddParticipant,
    Avatar,
    ChatWidgetMessage,
    MultiUserAvatar,
  },
  props: {
    isNarrow: {
      type: Boolean,
      default: true,
    },
    thread: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      loadingId: '',
      messageUploading: false,
      showInput: false,
      inputName: '',
      loadingName: false,

      message: '',
      editableMessage: null,
      errors: {
        commentText: [],
      },
      isActive: false,
      fetchMessages: [],
      reading: null,
    };
  },
  computed: {
    ...mapGetters('general', ['activeUser']),
    users() {
      if (this.thread.users.length === 1) {
        return this.thread.users;
      }
      return this.thread.users.filter(u => u.uuid !== this.activeUser.uuid);
    },
    user() {
      return this.users[0];
    },
    countOfUsers() {
      const users = this.thread.users.filter(u => u.uuid !== this.activeUser.uuid);
      return users.length;
    },
  },
  watch: {
    fetchMessages() {
      if (this.reading) {
        clearTimeout(this.reading);
        this.reading = null;
      }
      setTimeout(this.scrollToBottom, 1000);
      this.readMessages();
    },
  },
  created() {
    this.$bus.$on('edit-message', (message) => {
      this.message = message.body;
      this.editableMessage = message;
    });
    this.$bus.$on('delete-message', (message) => {
      this.deleteMessage(message);
    });
    this.inputName = this.thread.name;
  },
  mounted() {
    this.reading = setTimeout(this.readMessages, 5000);
  },
  beforeDestroy() {
    clearTimeout(this.reading);
    this.reading = null;
    // this.$apollo.subscriptions.fetchMessages.skip = true;
  },
  methods: {
    goToProfile() {
      if (this.$route.path !== '/media-editor') {
        this.$router.push({
          name: 'ProfilePage',
          hash: '#about',
          params: {
            username: this.user.username,
          },
        });
      } else {
        window.location = `/profile/${this.user.username}`;
      }
    },
    openNameInput() {
      this.showInput = true;
    },
    closeNameInput() {
      this.inputName = this.thread.name;
      this.showInput = false;
    },
    cloneMessage(obj) {
      return JSON.parse(JSON.stringify(obj, (k, v) => {
        switch (k) {
          case '__typename':
          case 'createdAt':
          case 'updatedAt':
          case 'user':
            return undefined;
          default:
            return v;
        }
      }));
    },
    scrollToBottom() {
      // scroll to bottom
      this.$refs.chatWidgetContent.scrollTop = this.$refs.chatWidgetContent.scrollHeight;
    },
    newline() {
      this.message = `${this.message}\n`;
    },
    async updateThread() {
      if (!this.inputName) {
        return;
      }
      try {
        this.loadingName = true;
        const { data: { updateThread: uThread } } = await this.$apollo.mutate({
          mutation: UPDATE_THREAD,
          variables: {
            thread: {
              id: this.thread.id,
              name: this.inputName,
            },
          },
          update: (store, { data: { updateThread } }) => {
            const data = store.readQuery({ query: FETCH_THREADS });
            const idx = data.fetchThreads.findIndex(ft => ft.id === updateThread.id);
            if (idx !== -1) {
              data.fetchThreads[idx] = updateThread;
            }
            store.writeQuery({
              query: FETCH_THREADS,
              data,
            });
          },
        });
        this.inputName = uThread.name;
        this.showInput = false;
      } catch (err) {
        console.error(err);
      } finally {
        this.loadingName = false;
      }
    },
    async readMessages() {
      try {
        await this.$apollo.mutate({
          mutation: READ_MESSAGES,
          variables: {
            threadId: this.thread.id,
          },
        });
      } catch (err) {
        console.error(err);
      }
    },
    async deleteMessage(message) {
      try {
        this.loadingId = message.id;
        await this.$apollo.mutate({
          mutation: DELETE_MESSAGE,
          variables: {
            id: message.id,
          },
          // quick delete message
          update: (store, { data: { deleteMessage } }) => {
            const fetchMessages = {
              query: FETCH_MESSAGES,
              variables: {
                threadId: this.thread.id,
              },
            };
            const data = store.readQuery(fetchMessages);
            data.fetchMessages = data.fetchMessages.filter(m => m.id !== deleteMessage.id);
            store.writeQuery({
              ...fetchMessages,
              data,
            });
          },
        });
      } catch (err) {
        console.error(err);
      } finally {
        this.loadingId = '';
      }
    },
    handleSendMessage() {
      if (this.editableMessage) {
        this.editMessage();
      } else {
        this.addMessage();
      }
    },
    async editMessage() {
      const message = Object.assign(
        this.cloneMessage(this.editableMessage),
        { body: this.message },
      );

      try {
        this.messageUploading = true;
        await this.$apollo.mutate({
          mutation: UPDATE_MESSAGE,
          variables: {
            message,
          },
          // quick update messages
          update: (store, { data: { updateMessage } }) => {
            const fetchMessages = {
              query: FETCH_MESSAGES,
              variables: {
                threadId: this.thread.id,
              },
            };
            const data = store.readQuery(fetchMessages);
            const idx = data.fetchMessages.findIndex(m => m.id === updateMessage.id);
            if (idx === -1) {
              data.fetchMessages = Object.assign(data.fetchMessages[idx], updateMessage);
              store.writeQuery({
                ...fetchMessages,
                data,
              });
            }
          },
        });
        this.message = '';
        this.editableMessage = null;
      } catch (err) {
        console.error(err);
      } finally {
        this.messageUploading = false;
      }
    },
    async addMessage() {
      if (!this.message) return;
      const { message } = this;

      // clear input
      this.message = '';

      try {
        this.messageUploading = true;
        await this.$apollo.mutate({
          mutation: CREATE_MESSAGE,
          variables: {
            message: {
              threadId: this.thread.id,
              body: message,
            },
          },
          // quick add messages
          update: (store, { data: { createMessage } }) => {
            const fetchMessages = {
              query: FETCH_MESSAGES,
              variables: {
                threadId: this.thread.id,
              },
            };
            const data = store.readQuery(fetchMessages);
            const idx = data.fetchMessages.findIndex(m => m.id === createMessage.id);
            if (idx === -1) {
              data.fetchMessages = data.fetchMessages.concat(createMessage);
              store.writeQuery({
                ...fetchMessages,
                data,
              });
            }
          },
          // Optimistic UI
          optimisticResponse: {
            __typename: 'Mutation',
            createMessage: {
              __typename: 'Message',
              body: message,
              id: '-1',
              threadId: this.thread.id,
              user: this.activeUser,
              userId: this.activeUser.id,
              project: null,
              createdAt: moment(),
              updatedAt: moment(),
              shareData: {
                __typename: 'ShareData',
                shareUuid: null,
                shareId: null,
                shareType: null,
              },
            },
          },
        });
        this.scrollToBottom();
      } catch (err) {
        console.error(err);
      } finally {
        this.messageUploading = false;
      }
    },
    onFocus() {
      this.isActive = true;
    },
    ifBlur() {
      this.isActive = false;
    },
    skipError(key, index) {
      this.errors[key].splice(index, 1);
    },
    closeChat() {
      this.$apollo.mutate({
        mutation: CLOSE_THREAD,
        variables: {
          id: this.thread.id,
        },
      });
      this.$emit('close-chat-window');
    },
  },
  apollo: {
    fetchMessages: {
      query: FETCH_MESSAGES,
      variables() {
        return {
          threadId: this.thread.id,
        };
      },
      subscribeToMore: [
        {
          document: SUBSCRIPTION_MESSAGE_CREATED,
          variables() {
            return {
              threadId: this.thread.id,
            };
          },
          updateQuery: (previousResult, { subscriptionData }) => {
            if (!has(subscriptionData, 'data')) {
              return previousResult;
            }
            const { fetchMessages } = previousResult;
            const { messageCreated } = subscriptionData.data;

            if (fetchMessages.find(flw => flw.id === messageCreated.id)) {
              return previousResult;
            }
            return {
              fetchMessages: [
                ...fetchMessages,
                messageCreated,
              ],
            };
          },
        },
        {
          document: SUBSCRIPTION_MESSAGE_DELETED,
          variables() {
            return {
              threadId: this.thread.id,
            };
          },
          updateQuery: (previousResult, { subscriptionData }) => {
            if (!has(subscriptionData, 'data')) {
              return previousResult;
            }
            const { fetchMessages } = previousResult;
            const { messageDeleted } = subscriptionData.data;

            const filteredMessage = fetchMessages.filter(flw => flw.id !== messageDeleted.id);
            return { fetchMessages: [...filteredMessage] };
          },
        },
        {
          document: SUBSCRIPTION_MESSAGE_UPDATED,
          variables() {
            return {
              threadId: this.thread.id,
            };
          },
          updateQuery: (previousResult, { subscriptionData }) => {
            if (!has(subscriptionData, 'data')) {
              return previousResult;
            }
            const { fetchMessages } = previousResult;
            const { messageUpdated } = subscriptionData.data;

            const idx = fetchMessages.findIndex(m => m.id === messageUpdated.id);
            if (idx === -1) {
              fetchMessages[idx] = messageUpdated;
            }
            return { fetchMessages: [...fetchMessages] };
          },
        },
      ],
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .fa-icon {
    fnt($grey-light, 14px, $weight-normal, center)
    transition all .3s

    &--invert {
      color $white
    }

    &--big {
      font-size 1.5rem
    }

    &--inp {
      color $grey-lighter
      font-size 12px
    }
  }

  /* chat widget */
  .cw {
    background-color $white
    border-radius $radius
    border 1px solid $border
    bottom 8px
    display flex
    flex-direction column
    position absolute
    right 216px
    transition all .8s
    z-index 10
    width: 467px

    &--narrow {
      right 80px
    }

    &__header {
      fl-between()
      height 56px
      width 100%
      border-bottom 1px solid $border
      padding 0 8px
    }

    &__main {
      height 350px
      overflow-y auto
      padding: 0 8px
    }

    &__loading {
      fl-center()
      height 80px
      width 100%
    }

    &__empty {
      fnt($text-lighter, 18px, $weight-light, center)
      fl-center()
      text-transform capitalize
      height 80px
      width 100%
    }

    &__footer {
      fl-left()
      margin-bottom 4px
      min-height 40px
      padding 0 8px
      width 100%
    }

    &__left {
      fl-left()
    }

    &__right {
      fl-right()
    }

    & >>> .el-input__inner:hover {
      border-color: $border;
    }
  }

  /* chat widget header */
  .cwh {
    &__user-box {
      fl-left()
    }

    &__avatar {
      cursor pointer
      fl-center()
      circle(36)
      background center center / cover no-repeat $primary

      &--multi {
        size(18, 18)
        margin 0 2px 2px 0
      }

      &--count {
        fnt($text-invert, 8px, $weight-normal, center)
      }
    }

    &__m-avatar-box {
      size(40, 40)
      display flex
      flex-wrap wrap
      margin 0 -2px -2px 0
    }

    &__input-box {
      fl-left()
      padding-left 8px
    }

    &__input {
      size(28, 130)
      fnt($text-light, 12px, $weight-light, left)
      border 1px solid $border
      border-radius $radius 0 0 $radius
      border-right none
      padding 0 8px
      outline none

      &:active,
      &:focus {
        border-color $primary
      }
    }

    &__input-btn {
      size(28, 28)
      fl-center()
      border 1px solid $border
      border-radius 0 $radius $radius 0
      border-left none
      outline none
    }

    &__input:active + &__input-btn,
    &__input:focus + &__input-btn {
      border-color $primary
    }

    &__title-box {
      display flex
      flex-direction column
      padding-left 8px
      cursor pointer
    }

    &__title {
      fnt($text, 14px, $weight-semibold, left)
      line-height 1
    }

    &__sub-title {
      fnt($text-light, 10px, $weight-light, left)
    }

    &__btn {
      fl-center()
      size(36, 28)
      cursor pointer
      outline none

      &:hover .fa-icon {
        opacity .5
      }

      &:last-child {
        margin-right -4px
      }
    }

    &__btn + &__btn {
      margin-left 4px
    }
  }

  /* chat widget footer */
  .cwf {
    &__form {
      align-items flex-end
      background-color $white
      border-radius $radius
      border 1px solid $border
      display flex
      position relative
      width 100%

      & >>> .el-textarea__inner {
        fnt($text-light, 12px, $weight-normal, left)
        border none

        &::-webkit-input-placeholder {
          color $grey-lighter
          font-weight 300
        }

        &::-moz-placeholder {
          color $grey-lighter;
          font-weight 300
        }

        &:-moz-placeholder {
          color: $grey-lighter;
          font-weight: 300;
        }

        &:-ms-input-placeholder {
          color: $grey-lighter;
          font-weight: 300;
        }
        resize: none;
      }
    }

    &__input {
      margin-left 1px
    }

    &__button {
      fl-center();
      size(26, 40)
      background-color $grey-lighter
      border-radius $radius-small
      border none
      cursor pointer
      flex 0 0 auto
      margin 1px
      outline none
      transition all .3s

      &:active {
        background-color $blue-hover
      }

      &--is-active {
        background-color $info
      }
    }

    &__error {
      fnt($warning-invert, .75rem, $weight-normal, left)
      background-color $warning
      border 1px solid $warning
      border-radius $radius
      box-shadow 0 2px 2px 0 rgba($black-bis, .16), 0 0 0 1px rgba($black-bis, .08)
      cursor pointer
      left 20px
      margin-bottom -6px
      padding 4px 12px
      position absolute
      transition box-shadow .3s
      width 80%
      z-index 2

      &--bottom {
        bottom -18px
        left 20px
      }

      &:hover {
        box-shadow 0 3px 8px 0 rgba($black-bis, 0.2), 0 0 0 1px rgba($black-bis, 0.08)
      }
    }
  }

  .component-translate-enter-active {
    transition transform .3s ease-out
  }

  .component-translate-leave-active {
    transition transform .1s ease-in
  }

  .component-translate-enter,
  .component-translate-leave-to {
    transform translateY(100%)
  }

</style>
