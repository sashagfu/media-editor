<template>
  <div class="ThreadItemNarrow tn-item">
    <template v-if="countOfUsers === 1">
      <div
        v-if="unreadMessages"
        class="tn-item__unread-msg"
      >
        {{ unreadMessages }}
      </div>
      <Avatar
        v-if="user.avatar.includes('/profile/default/avatar')"
        :size="40"
        :username="user.displayName"
        custom-class="tn-item__avatar"
      />
      <div
        v-else
        :style="{'background-image': `url(${user.avatar})`}"
        class="tn-item__avatar"
      />
    </template>
    <template v-else>
      <!-- multi user chat item -->
      <div class="tn-item__m-avatar-box">
        <div
          v-if="unreadMessages"
          class="tn-item__unread-msg tn-item__unread-msg--multi"
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
              custom-class="tn-item__avatar tn-item__avatar--multi"
            />
            <div
              v-else
              :style="{'background-image': `url(${userItem.avatar})`}"
              class="tn-item__avatar tn-item__avatar--multi"
            />
          </div>
        </template>
        <div
          v-if="countOfUsers > 2"
          class="tn-item__avatar-item"
        >
          <div class="tn-item__avatar tn-item__avatar--multi tn-item__avatar--count">
            {{ decorateCountOfUser }}
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

import Avatar from 'Pages/ProfilePage/Avatar';

export default {
  name: 'ThreadItemNarrow',
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
      if (this.thread.users.length === 1) {
        return this.thread.users[0];
      }
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
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .tn-item {
    position: relative;
    height 70px

    &__avatar {
      fl-center();
      flex: 0 0 auto;
      background: center center/cover no-repeat $primary;
      height: 40px;
      width: 40px;
      border-radius: 50%;
      &--multi {
        height: 18px;
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
      height: 42px;
      width: 42px;
      margin: 0 -2px -2px 0;
    }
    &__unread-msg {
      fl-center();
      fnt($text-invert, 18px, $weight-normal, center);
      background-color: rgba($grey-darker, .5);
      position: absolute;
      height: 40px;
      width: 40px;
      border-radius: 50%;
      &--multi {
        background-color: rgba($grey-darker, .7);
        border-radius: 10px;
      }
    }
    &__avatar-item {
      height: 21px;
      width: 21px;
      padding: 0 4px 4px 0;
    }
  }

</style>
