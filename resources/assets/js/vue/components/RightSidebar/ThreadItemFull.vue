<template>
  <div class="ThreadItemFull tf-item">
    <template v-if="countOfUsers === 1">
      <div
        v-if="unreadMessages"
        class="tf-item__unread-msg"
        @click="chatOpen"
      >
        {{ unreadMessages }}
      </div>
      <Avatar
        v-if="user.avatar.includes('/profile/default/avatar')"
        :size="40"
        :username="user.displayName"
        custom-class="tf-item__avatar"
        @click="chatOpen"
      />
      <div
        v-else
        :style="{'background-image': `url(${user.avatar})`}"
        class="tf-item__avatar"
        @click="chatOpen"
      />
      <div
        class="tf-item__title-box"
        @click="chatOpen"
      >
        <div class="tf-item__title">
          {{ user.displayName }}
        </div>
        <div
          v-if="unreadMessages"
          class="tf-item__sub-title"
        >
          you have {{
            thread.unreadMessagesCount
          }} unread {{
            thread.unreadMessagesCount === 1 ? 'message' : 'messages'
          }}
        </div>
        <div
          v-else
          class="tf-item__sub-title"
        >
          @{{ user.username }}
        </div>
      </div>
      <div class="tf-item__actions-box">
        <font-awesome-icon
          :icon="['fas', 'times']"
          class="fa-icon fa-icon"
          @click="hideThread"
        />
      </div>
    </template>
    <template v-else>
      <!-- multi user chat avatar -->
      <div
        class="tf-item__m-avatar-box"
        @click="chatOpen"
      >
        <div
          v-if="unreadMessages"
          class="tf-item__unread-msg tf-item__unread-msg--multi"
        >
          {{ unreadMessages }}
        </div>
        <template v-for="userItem in multiUsers">
          <div
            :key="`usr-${userItem.id}`"
            class="tn-item__avatar-item"
          >
            <Avatar
              v-if="userItem.avatar.includes('/profile/default/avatar')"
              :size="20"
              :username="userItem.displayName"
              custom-class="tf-item__avatar tf-item__avatar--multi"
            />
            <div
              v-else
              :style="{'background-image': `url(${userItem.avatar})`}"
              class="tf-item__avatar tf-item__avatar--multi"
            />
          </div>
        </template>
        <div
          v-if="countOfUsers > 2"
          class="tn-item__avatar-item"
        >
          <div class="tf-item__avatar tf-item__avatar--multi tf-item__avatar--count">
            {{ decorateCountOfUser }}
          </div>
        </div>
      </div>
      <div
        class="tf-item__title-box"
        @click="chatOpen"
      >
        <div
          v-if="thread.name"
          class="tf-item__title"
        >
          {{ thread.name }}
        </div>
        <div
          v-else
          class="tf-item__title"
        >
          {{ user.displayName }}
        </div>
        <div
          v-if="unreadMessages"
          class="tf-item__sub-title"
        >
          you have {{
            thread.unreadMessagesCount
          }} unread {{
            thread.unreadMessagesCount === 1 ? 'message' : 'messages'
          }}
        </div>
        <div
          v-else-if="thread.name"
          class="tf-item__sub-title"
        >
          chat with {{ thread.users.length }} participants
        </div>
        <div
          v-else
          class="tf-item__sub-title"
        >
          and {{ countOfUsers }} participants
        </div>
      </div>
      <div class="tf-item__actions-box">
        <font-awesome-icon
          :icon="['fas', 'times']"
          class="fa-icon fa-icon"
          @click="hideThread"
        />
      </div>
    </template>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

import Avatar from 'Pages/ProfilePage/Avatar';

import HIDE_THREAD from '../../../../graphQL/messages/mutations/hideThread.graphql';

export default {
  name: 'ThreadItemFull',
  components: {
    Avatar,
  },
  props: {
    thread: {
      type: Object,
      default: () => ({}),
    },
  },
  computed: {
    ...mapGetters('general', ['activeUser']),
    user() {
      const users = this.thread.users.filter(u => u.uuid !== this.activeUser.uuid);
      return users[0];
    },
    multiUsers() {
      if (this.thread.users.length > 3) {
        return this.thread.users.slice(0, 3);
      }
      return this.thread.users;
    },
    countOfUsers() {
      if (this.thread.users.length === 1) {
        return this.thread.users.length;
      }
      const users = this.thread.users.filter(u => u.uuid !== this.activeUser.uuid);
      return users.length;
    },
    decorateCountOfUser() {
      const count = this.countOfUsers - 2;
      if (count < 1) {
        return '';
      }
      if (count >= 1 && count < 10) {
        return `+${count}`;
      }
      return '+9';
    },
    unreadMessages() {
      if (!this.thread.unreadMessagesCount) {
        return '';
      }
      if (this.thread.unreadMessagesCount < 10) {
        return `+${this.thread.unreadMessagesCount}`;
      }
      return '+9';
    },
  },
  methods: {
    hideThread() {
      this.$apollo.mutate({
        mutation: HIDE_THREAD,
        variables: {
          id: this.thread.id,
        },
      });
    },
    chatOpen() {
      this.$emit('chat-open', this.thread);
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .tf-item {
    fl-right();
    position: relative;
    height 70px
    &__avatar {
      fl-center();
      background: center center/cover no-repeat $primary;
      border-radius: 50%;
      flex: 0 0 auto;
      height: 40px;
      width: 40px;
      &--multi {
        height: 18px;
        margin: 0 2px 2px 0;
        width: 18px;
      }
      &--count {
        fnt($text-invert, 8px, $weight-normal, center);
      }
    }
    &__m-avatar-box {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      height: 40px;
      margin: 0 -2px -2px 0;
      width: 40px;
      min-width: 40px;
    }
    &__unread-msg {
      fl-center();
      fnt($text-invert, 18px, $weight-normal, center);
      background-color: rgba($grey-darker, .5);
      border-radius: 50%;
      height: 40px;
      left: 0;
      position: absolute;
      width: 40px;
      &--multi {
        background-color: rgba($grey-darker, .7);
        border-radius: 10px;
      }
    }
    &__avatar-item {
      height: 21px;
      padding: 0 4px 4px 0;
      width: 21px;
    }
    &__title-box {
      display: flex;
      flex-grow: 1;
      flex-direction: column;
      padding-left: 8px;
    }
    &__title {
      fnt($text, 14px, $weight-semibold, left);
      line-height: 1;
    }
    &__sub-title {
      fnt($text-light, 10px, $weight-light, left);
    }
  }

</style>
