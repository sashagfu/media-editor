<template>
  <div
    class="ChatMessageContentBox chat-cb"
    @click="openThread()"
  >
    <!--:class="{'unread': thread.isUnread === true}"-->
    <div class="chat-cb__item--left">
      <div class="chat-cb__ava-box">
        <Avatar
          v-if="user.avatar.includes('/profile/default/avatar')"
          :size="36"
          :username="user.displayName"
          custom-class="chat-cb__avatar"
        />
        <div
          v-else
          :style="{'background-image': `url(${user.avatar})`}"
          class="chat-cb__avatar"
        />
      </div>
      <div class="chat-cb__title">
        <div class="chat-cb__title-user">
          {{ user.displayName }}
        </div>
        <div class="chat-cb__title-text">
          <!--{{ thread.latestMessage }}-->
        </div>
      </div>
    </div>
    <div class="chat-cb__item--right" />
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

import Avatar from 'Pages/ProfilePage/Avatar';

export default {
  name: 'ChatMessageContentBox',
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
    countOfUsers() {
      const users = this.thread.users.filter(u => u.uuid !== this.activeUser.uuid);
      return users.length;
    },
  },
  methods: {
    openThread() {
      this.$bus.$emit('chat-open', this.thread.id);
    },
  },
};

</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .unread {
    background-color $white-ter
  }

  .chat-cb {
    fl-between();
    min-height 84px
    padding 22px
    width 100%
    &:not(:last-child) {
      border-bottom 1px solid $border
    }
    &:hover {
      background-color $white-bis
    }
    &__item {
      &--left {
        fl-left()
      }
      &--right {
        fl-right()
      }
    }
    &__ava-box {
      fl-left()
    }
    &__avatar {
      fl-center()
      circle(36)
      background center center/cover no-repeat
    }
    &__title {
      fl-left()
      padding 0 12px
    }

    &__title-user {
      fnt($text, 12px, $weight-bold, left)
    }
    &__title-text {
      fnt($text-light, 12px, $weight-normal, left)
      display inline
    }
    &__title-data {
      fnt($text-light, 11px, $weight-normal, left)
    }

    &-icon {
      size(22, 22)
      background center center no-repeat
      &--commented {
        background-image url("../../../../../img/comented-icon.png")
      }
      &--delete {
        size(8, 8)
        background-image url("../../../../../img/delete-icon.png")
        margin-right 12px
        margin-top 10px
      }
      &--more-options {
        size(8, 10)
        background-image url("../../../../../img/more-icon.png")
        margin-right 12px
        margin-top 10px
      }
      &__inline {
        align-items flex-start
        display flex
        justify-content flex-end
      }
      &__centered {
        height 100%
        padding-right 26px
        padding-top 12px
        width 100%
      }
    }
  }

  a.chat-content-user {
    fnt($text-light !important, 12px, $weight-semibold, left)
    &:hover {
      color $text !important
    }
  }
</style>
