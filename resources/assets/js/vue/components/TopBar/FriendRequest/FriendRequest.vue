<template>
  <div class="friend-request">
    <div class="friend-request__item friend-request-box">
      <div class="tag is-info">
        5
      </div>
    </div>
    <div class="friend-request__item friend-request-dropdown-menu">
      <FriendRequestDropdownMenu/>
    </div>
  </div>
</template>

<script>
/* @flow */
import FriendRequestDropdownMenu from './FriendRequestDropdownMenu';

export default {
  components: {
    FriendRequestDropdownMenu,
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .friend-request {
    display        : flex;
    flex-direction : column;
    align-items    : flex-start;
    position       : relative;

    &__item {
      position : relative;
    }
  }

  .friend-request-box {
    background      : url('../../../../../img/happy-face-icon.png') center center no-repeat;
    display         : flex;
    height          : 72px;
    justify-content : flex-end;
    padding-right   : 14px;
    padding-top     : 16px;
    position        : relative;
    width           : 62px;
    &:hover {
      background-image : url('../../../../../img/happy-face-icon-is-activ.png');
    }
  }

  .friend-request-dropdown-menu {
    align-items    : flex-start;
    box-shadow     : 10px 10px 30px 5px rgba(45, 47, 62, 0.05);
    display        : none;
    flex-direction : column;
    left           : -172px;
    position       : absolute;
    top            : 56px;
    width          : 380px;
    z-index        : 1;
  }

  .friend-request:hover .friend-request-dropdown-menu {
    display : flex;
  }

  .tag {
    fl-center();
    border-radius   : 10px;
    font-size       : 10px;
    font-weight     : 700;
    height          : 18px;
    width           : 18px;
  }

</style>
