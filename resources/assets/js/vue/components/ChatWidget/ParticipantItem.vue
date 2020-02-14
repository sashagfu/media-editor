<template>
  <div
    class="ParticipantItem pi"
  >
    <div
      class="left"
      @click="goToProfile"
    >
      <Avatar
        v-if="user.avatar.includes('/profile/default/avatar')"
        :size="32"
        :username="user.displayName"
        custom-class="pi__avatar"
      />
      <div
        v-else
        :style="{'background-image': `url(${user.avatar})`}"
        class="pi__avatar"
      />
      <div class="pi__title-box">
        <div class="pi__title">
          {{ user.displayName }}
        </div>
        <div class="pi__sub-title">
          @{{ user.username }}
        </div>
      </div>
    </div>
    <div class="fl-right">
      <div
        class="pi__btn"
        @click="toggleUser"
      >
        <font-awesome-icon
          v-if="selected"
          :icon="['far', 'check-square']"
          class="fa-icon fa-icon--dark"
        />
        <font-awesome-icon
          v-else
          :icon="['far', 'square']"
          class="fa-icon fa-icon--dark"
        />
      </div>
    </div>
  </div>
</template>

<script>
import Avatar from 'Pages/ProfilePage/Avatar';

export default {
  name: 'ParticipantItem',
  components: {
    Avatar,
  },
  props: {
    user: {
      type: Object,
      default: () => ({}),
    },
    selectedFriends: {
      type: Array,
      default: () => [],
    },
  },
  computed: {
    selected() {
      return this.selectedFriends.some(u => u === this.user.id);
    },
  },
  methods: {
    goToProfile() {
      if (this.$route.path !== 'media-editor') {
        this.$router.push({
          name: 'ProfilePage',
          params: {
            username: this.user.username,
          },
        });
      } else {
        window.location = `/profile/${this.user.username}`;
      }
    },
    toggleUser() {
      this.$emit('toggle-user', this.user.id);
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
    fnt($white-shadow, 1rem, $weight-normal, left)
    transition all .3s
    &--spinner {
      color $text
      font-size 16px
      margin -2px
    }
    &--dark {
      color $text-lighter
    }
    &--invert {
      fnt($text-invert, 12px, $weight-normal, center)
    }
  }

  .pi {
    fl-between()
    padding 4px 16px
    flex 0 0 auto

    &:first-child {
       padding-top 8px
    }
    &:last-child {
       padding-bottom 8px
    }
    &__avatar {
      fl-center()
      circle(32)
      background center center/cover no-repeat
      flex 0 0 auto
    }
    &__title-box {
      display flex
      flex-direction column
      padding-left 8px
    }
    &__title {
      fnt($text-light, 12px, $weight-semibold, left)
      line-height 1
    }
    &__sub-title {
      fnt($text-lighter, 10px, $weight-light, left)
    }
    &__btn {
      fl-center()
      size(26, 26)
    }
  }

  .left {
    cursor pointer
    fl-left()
  }
  .right {
    cursor pointer
    fl-right()
  }

</style>
