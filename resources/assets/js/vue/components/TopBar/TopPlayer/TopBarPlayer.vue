<template>
  <div class="player">
    <div class="player__top">
      <div class="player__next-icon">
        <div class="player__action-icon">
          <i
            class="fa fa-lg fa-play-circle"
            aria-hidden="true"
          />
          <!-- <i class="fa fa-pause-circle" aria-hidden="true"></i> -->
        </div>
      </div>
    </div>
    <div class="player__dropdown-menu">
      <DropdownPlayList/>
    </div>
  </div>
</template>

<script>

import DropdownPlayList from './DropdownPlayList';

export default {
  components: {
    DropdownPlayList,
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .player {
    position : relative;
    &__top {
      background      : center center no-repeat;
      cursor          : pointer;
      display         : flex;
      height          : 72px;
      justify-content : flex-end;
      padding         : 16px 14px 0 0;
      position        : relative;
      width           : 62px;

    }
    &__action-icon {
      align-items      : center;
      background-color : $white-bis; //$turquoise
      border-radius    : 100%;
      color            : $turquoise;
      display          : flex;
      font-size        : 10px;
      height           : 18px;
      justify-content  : center;
      width            : 18px;
    }
    &__dropdown-menu {
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
  }

  .player
    &:hover &__dropdown-menu {
    display : flex;
  }

  .fa {
    font-size : 22px;
  }
</style>
