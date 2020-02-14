<template>
  <el-dialog
    :visible.sync="visible"
    :lock-scroll="false"
    title="Share Project"
    width="30%"
    class="ChatShareBox chat-share-box"
    @close="closeChatShareDialog"
  >
    <div class="chat-share-box__form">
      <el-input
        v-model="commentMessage"
        :autosize="{ minRows: 1 }"
        resize="none"
        placeholder="Write your comment here..."
        class="chat-share-box__input"
      />
    </div>
    <el-tabs
      v-model="activeTab"
      :stretch="true"
      class="chat-share-box__tabs"
    >
      <el-tab-pane
        label="Thread"
        name="thread"
      >
        <div class="chat-share-box__threads">
          <div
            v-for="thread in fetchThreads"
            :key="thread.id"
            class="chat-share-box__thread-box"
          >
            <ThreadItem :thread="thread" />
            <ChatShareButton
              :thread="thread"
              :pending-shares="pendingShares"
              @share-to-thread="shareToThread"
            />
          </div>
        </div>
      </el-tab-pane>
      <el-tab-pane
        label="User"
        name="user"
      >
        <div class="chat-share-box__users">
          <div
            v-for="user in friends"
            :key="user.id"
            class="chat-share-box__user-box"
          >
            <UserItem
              :user="user"
            />
            <ChatShareButton
              :user="user"
              :pending-shares="pendingShares"
              @share-to-user="shareToUser"
            />
          </div>
        </div>
      </el-tab-pane>
    </el-tabs>
  </el-dialog>
</template>

<script>
import { mergeUnique } from 'Helpers/utils';
import { mapGetters } from 'vuex';

import FETCH_THREADS from 'Gql/messages/queries/fetchThreads.graphql';
import FETCH_MESSAGES from 'Gql/messages/queries/fetchMessages.graphql';
import FETCH_FOLLOWERS from 'Gql/users/queries/fetchFollowers.graphql';
import FETCH_FOLLOWING from 'Gql/users/queries/fetchFollowing.graphql';

import CREATE_MESSAGE from 'Gql/messages/mutations/createMessage.graphql';
import CREATE_THREAD from 'Gql/messages/mutations/createThread.graphql';

import ThreadItem from './ThreadItem';
import ChatShareButton from './ChatShareButton';
import UserItem from './UserItem';
import * as constants from '../../../helpers/constants';

export default {
  name: 'ChatShareBox',
  components: {
    ThreadItem,
    ChatShareButton,
    UserItem,
  },
  props: {
    project: {
      type: Object,
      default: () => {},
    },
  },
  data() {
    return {
      visible: true,
      fetchThreads: [],
      commentMessage: '',
      pendingShares: [],
      activeTab: 'thread',
      fetchFollowers: [],
      fetchFollowing: [],
    };
  },
  computed: {
    ...mapGetters('general', ['activeUser']),
    friends() {
      const followers = this.cloneFollowers(this.fetchFollowers);
      const following = this.cloneFollowers(this.fetchFollowing);
      return mergeUnique(followers, following);
    },
  },
  methods: {
    closeChatShareDialog() {
      this.$emit('close-chat-share-dialog');
    },
    async shareToThread(thread) {
      const message = {
        threadId: thread.id,
        body: this.commentMessage,
        shareData: {
          shareType: constants.PROJECT_TYPE,
          shareId: this.project.id,
          shareUuid: this.project.uuid,
        },
      };
      this.pendingShares.push({
        threadId: thread.id,
      });
      await this.sendMessage(message);
      this.pendingShares = this.pendingShares.filter(sh => sh.threadId !== thread.id);
    },
    async shareToUser(user) {
      this.pendingShares.push({
        userId: user.id,
      });
      const { data: { createThread } } = await this.$apollo.mutate({
        mutation: CREATE_THREAD,
        variables: {
          targetId: user.id,
        },
        update: (store, { data: { createThread: cThread } }) => {
          const data = store.readQuery({ query: FETCH_THREADS });
          const idx = data.fetchThreads.findIndex(ft => ft.id === cThread.id);
          if (idx !== -1) {
            data.fetchThreads[idx] = cThread;
          } else {
            data.fetchThreads.push(cThread);
          }
          store.writeQuery({ query: FETCH_THREADS, data });
        },
      });
      const message = {
        threadId: createThread.id,
        body: this.commentMessage,
        shareData: {
          shareType: constants.PROJECT_TYPE,
          shareId: this.project.id,
        },
      };
      await this.sendMessage(message);
      this.pendingShares = this.pendingShares.filter(sh => sh.userId !== user.id);
    },
    async sendMessage(message) {
      await this.$apollo.mutate({
        mutation: CREATE_MESSAGE,
        variables: {
          message,
        },
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
            store.writeQuery({ ...fetchMessages, data });
          }
        },
      });
    },
    cloneFollowers(obj) {
      return JSON.parse(JSON.stringify(obj, (k, v) => {
        switch (k) {
          case '__typename':
          case 'createdAt':
          case 'updatedAt':
            return undefined;
          default:
            return v;
        }
      }));
    },
  },
  apollo: {
    fetchThreads: {
      query: FETCH_THREADS,
    },
    fetchFollowers: {
      query: FETCH_FOLLOWERS,
      variables() {
        return {
          userId: this.activeUser.id,
        };
      },
    },
    fetchFollowing: {
      query: FETCH_FOLLOWING,
      variables() {
        return {
          userId: this.activeUser.id,
        };
      },
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .chat-share-box
    & >>> .el-dialog__body {
      padding: 32px 26px;
    }

    &__thread-box
      fl-between()

    &__form
      align-items flex-end
      background-color $white
      border-radius $radius
      margin-bottom 20px
      display flex
      position relative
      width 100%

      & >>> .el-input__inner {
        fnt($text-light, 12px, $weight-normal, left)
        &:hover {
          border 1px solid #53b847;
        }
        &::-webkit-input-placeholder {
          color $grey-lighter
          font-weight 300
        }
        &::-moz-placeholder {
          color $grey-lighter;
          font-weight 300
        }
        &:-moz-placeholder {
          color       : $grey-lighter;
          font-weight : 300;
        }
        &:-ms-input-placeholder {
          color       : $grey-lighter;
          font-weight : 300;
        }
      }

    &__tabs
      & >>> .el-tabs__nav
        width 100%
      & >>> .el-tabs__item
        width 50%
    &__user-box
      fl-between()

</style>
