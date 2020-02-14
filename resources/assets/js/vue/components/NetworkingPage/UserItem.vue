<template>
  <div class="UserItem u-item box is-marginless">
    <div class="u-item__header">
      <div class="u-item__left"/>
      <div class="u-item__right">
        <ActionBox
          :user="usersData"
          :active-user="activeUser"
          :show-follow-btn="showFollowBtn"
        />
      </div>
    </div>
    <div class="u-item__main">
      <Avatar
        v-if="usersData.avatar.includes('/profile/default/avatar')"
        :username="usersData.displayName"
        :size="96"
        custom-class="u-item__avatar"
        @click="goToProfile"
      />
      <div
        v-else
        :style="{'background-image': `url(${usersData.avatar})`}"
        class="u-item__avatar"
        @click="goToProfile"
      />
      <div
        class="u-item__title-box"
        @click="goToProfile"
      >
        <div class="u-item__title">
          {{ usersData.displayName }}
        </div>
        <div class="u-item__sub-title">
          @{{ usersData.username }}
        </div>
      </div>
      <div class="u-item__btn-box">
        <FollowButton
          v-if="showFollowBtn && activeUser.uuid !== usersData.uuid"
          :following="usersData"
          :active-user="activeUser"
          :round="smallBtn"
          class="u-item__btn"
        />
        <MessageButton
          :round="smallBtn"
          :user="usersData"
          class="u-item__btn"
        />
      </div>
      <div class="u-item__counts-box">
        <div class="u-item__counts-item">
          <font-awesome-icon
            :icon="['far', 'star']"
            class="fa-icon fa-icon--star"
          />
          <div class="u-item__count">
            <!--{{ usersData.total_likes + usersData.total_stars }}-->
            245
          </div>
        </div>
        <div class="u-item__counts-item">
          <font-awesome-icon
            :icon="['fas', 'paperclip']"
            class="fa-icon fa-icon--paperclip"
          />
          <div class="u-item__count">
            <!--{{ usersData.clipCount }}-->
            2
          </div>
        </div>
        <div class="u-item__counts-item">
          <font-awesome-icon
            :icon="['far', 'user']"
            class="fa-icon fa-icon--user"
          />
          <div class="u-item__count">
            <!--{{ usersData.followers.length }}-->
            35
          </div>
        </div>
      </div>
    </div>
    <div class="u-item__footer"/>
  </div>
</template>

<script>
import Avatar from 'Pages/ProfilePage/Avatar';
import FollowButton from 'Pages/Common/FollowButton';
import MessageButton from 'Pages/Common/MessageButton';
import ActionBox from 'Pages/NetworkingPage/ActionBox';

export default {
  name: 'UserItem',
  components: {
    ActionBox,
    Avatar,
    FollowButton,
    MessageButton,
  },
  props: {
    activeUser: {
      type: Object,
      default: () => ({}),
    },
    usersData: {
      type: Object,
      default: () => ({}),
    },
    smallBtn: {
      type: Boolean,
      default: false,
    },
    showFollowBtn: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    goToProfile() {
      this.$router.push({
        name: 'ProfilePage',
        params: {
          username: this.usersData.username,
        },
      });
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
    fnt($text-light, 12px, $weight-light, left);
    transition : all .3s;
    &--star {
      color: $yellow;
    }
    &--paperclip {
      color: $purple;
    }
    &--user {
      color: $grey;
    }
  }

  .u-item {
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
    position: relative;
    width: 100%;
    &__header {
      fl-between();
      flex: 0 0 auto;
      padding: 12px 24px;
      position: absolute;
      top: 0;
      width: 100%;
    }
    &__main {
      fl-center();
      flex-direction: column;
      flex: 1 1 auto;
    }
    &__footer {
      fl-center();
      flex: 0 0 auto;
    }
    &__left {
      fl-left();
    }
    &__right {
      fl-right();
    }
    &__center {
      fl-center();
    }
    &__avatar {
      fl-center();
      background: center center/cover no-repeat;
      border-radius: 50%;
      height: 96px;
      width: 96px;
      cursor: pointer;
    }
    &__title-box {
      padding: 8px 0;
      cursor: pointer;
    }
    &__title {
      fnt($text, 14px, $weight-semibold, center);
      line-height: 1;
    }
    &__sub-title {
      fnt($text-light, 10px, $weight-semibold, center);
    }
    &__btn-box {
      fl-center();
      padding-top: 8px;
    }
    &__btn {
      margin-bottom: 8px;
    }
    &__btn + &__btn {
      margin-left: 12px;
    }
    &__counts-box {
      fl-center();
      padding-top: 8px;
    }
    &__counts-item {
      fl-center();
      flex-direction: column;
    }
    &__counts-item + &__counts-item {
      margin-left: 26px;
    }
    &__count {
      fnt($text-light, 10px, $weight-semibold, center);
    }
  }

</style>
