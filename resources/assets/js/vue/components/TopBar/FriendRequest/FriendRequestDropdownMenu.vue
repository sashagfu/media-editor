<template>
  <div class="friend-menu-box">
    <div class="menu-diamond"/>
    <div class="friend-menu-box__item friend-menu-box__title-box">
      <div class="friend-menu-box__title friend-menu-box__title--left">
        <div class="friend-menu-box__title--text">
          Friend Request
        </div>
      </div>
      <div class="friend-menu-box__title friend-menu-box__title--right">
        <a class="friend-menu-box__title--menu">
          Mark all as read
        </a>
        <a class="friend-menu-box__title--menu">
          Settings
        </a>
      </div>
    </div>
    <!--<div class="friend-menu-box__item">-->
    <!--<LikedBox></LikedBox>-->
    <!--</div>-->
    <div class="friend-menu-box__item">
      <FriendRequestContentBox/>
    </div>
    <div class="friend-menu-box__item">
      <div class="view-all">
        <div class="view-all__text">
          View All Request
        </div>
      </div>
    </div>
  </div>
</template>

<script>
/* @flow */
// import LikedBox from '../Notification/LikedBox';
import FriendRequestContentBox from './FriendRequestContentBox';

export default {
  components: {
    // LikedBox,
    FriendRequestContentBox,
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .menu-diamond {
    deg45();
    background-color : $background;
    border-left      : 1px solid $border;
    border-top       : 1px solid $border;
    height           : 8px;
    left             : 198px;
    position         : absolute;
    top              : -5px;
    width            : 8px;
    z-index          : 1;
  }

  .friend-menu-box {
    background-color : $background;
    border           : 1px solid $border;
    border-radius    : 4px;
    position         : relative;
    width            : 380px;
    &__title-box {
      display         : flex;
      height          : 36px;
      justify-content : space-between;
    }

    &__title {
      align-items    : center;
      color          : $grey;
      display        : flex;
      font-size      : 10px;
      height         : 36px;
      text-transform : uppercase;
      width
      &--right {
        justify-content : flex-end;
      }
      &--text {
        font-weight  : $weight-normal;
        padding-left : 24px;
      }
      &--menu {
        color         : $grey !important;
        font-weight   : $weight-bold;
        padding-right : 24px;
      }
    }
    &__item {
      border-bottom : 1px solid $border;
      &:last-child {
        border : none;
      }
    }
  }

  .view-all {
    align-items      : center;
    background-color : $info;
    border-radius    : 0 0 3px 3px;
    display          : flex;
    height           : 52px;
    justify-content  : center;
    width            : 100%;
    &__text {
      fnt($text-invert, 12px, $weight-semibold, left);
      text-transform : capitalize
    }
  }
</style>
