<template>
  <div class="ThreadItem tr-item">
    <template v-if="countOfUsers === 1">
      <div
        v-if="unreadMessages"
        class="tr-item__unread-msg"
      >
        {{ unreadMessages }}
      </div>
      <Avatar
        v-if="user.avatar.includes('/profile/default/avatar')"
        :size="40"
        :username="user.displayName"
        custom-class="tr-item__avatar"
      />
      <div
        v-else
        :style="{'background-image': `url(${user.avatar})`}"
        class="tr-item__avatar"
      />
      <div class="tr-item__title-box">
        <div class="tr-item__title">
          {{ user.displayName }}
        </div>
        <div
          v-if="unreadMessages"
          class="tr-item__sub-title"
        >
          you have {{
            thread.unreadMessagesCount
          }} unread {{
            thread.unreadMessagesCount === 1 ? 'message' : 'messages'
          }}
        </div>
        <div
          v-else
          class="tr-item__sub-title"
        >
          @{{ user.username }}
        </div>
      </div>
    </template>
    <template v-else>
      <!-- multi user chat avatar -->
      <div class="tr-item__m-avatar-box">
        <div
          v-if="unreadMessages"
          class="tr-item__unread-msg tr-item__unread-msg--multi"
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
              custom-class="tr-item__avatar tr-item__avatar--multi"
            />
            <div
              v-else
              :style="{'background-image': `url(${userItem.avatar})`}"
              class="tr-item__avatar tr-item__avatar--multi"
            />
          </div>
        </template>
        <div class="tn-item__avatar-item">
          <div class="tr-item__avatar tr-item__avatar--multi tr-item__avatar--count">
            {{ decorateCountOfUser }}
          </div>
        </div>
      </div>
      <div class="tr-item__title-box">
        <div
          v-if="thread.name"
          class="tr-item__title"
        >
          {{ thread.name }}
        </div>
        <div
          v-else
          class="tr-item__title"
        >
          {{ user.displayName }}
        </div>
        <div
          v-if="unreadMessages"
          class="tr-item__sub-title"
        >
          you have {{
            thread.unreadMessagesCount
          }} unread {{
            thread.unreadMessagesCount === 1 ? 'message' : 'messages'
          }}
        </div>
        <div
          v-else-if="thread.name"
          class="tr-item__sub-title"
        >
          chat with {{ thread.users.length }} participants
        </div>
        <div
          v-else
          class="tr-item__sub-title"
        >
          and {{ countOfUsers }} participants
        </div>
      </div>
    </template>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

import Avatar from 'Pages/ProfilePage/Avatar';

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
      const count = this.countOfUsers - 3;
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
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .tr-item {
    fl-left();
    padding: 5px 0;
    position: relative;
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
      height: 40px;
      margin: 0 -2px -2px 0;
      width: 40px;
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
