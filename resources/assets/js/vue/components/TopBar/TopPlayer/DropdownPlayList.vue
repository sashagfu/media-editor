<template>
  <div class="dropdown-play-list box">
    <div class="menu-diamond"/>
    <div class="dropdown-play-list__headline">
      <div class="level">
        <div class="level-left">
          <div class="dropdown-play-list__previous-song"/>
          <div class="dropdown-play-list__action"/>
          <div class="dropdown-play-list__next-song"/>
          <div class="dropdown-play-list__time">
            3:28/4:16
          </div>
        </div>
        <div class="level-right">
          <div class="dropdown-play-list__sound level-item"/>
          <div class="dropdown-play-list__repeat level-item"/>
          <div class="dropdown-play-list__shuffle level-item"/>
        </div>
      </div>
      <div class="progress-bar">
        <div class="progress-bar__body">
          <div class="progress-bar__progress"/>
        </div>
      </div>
    </div>
    <div class="dropdown-play-list__main">
      <PlayListItem/>
      <PlayListItem/>
      <PlayListItem/>
      <PlayListItem/>
      <PlayListItem/>
      <PlayListItem/>
    </div>
    <div class="dropdown-play-list__footer level">
      <div class="level-left">
        <div class="dropdown-play-list__name-playlist level-item">
          Das Wohltemperierte Klavier
        </div>
      </div>
      <div class="level-right">
        <div class="dropdown-play-list__settings"/>
        <div class="dropdown-play-list__like"/>
        <div class="dropdown-play-list__add-to-favorite"/>
        <div class="dropdown-play-list__share"/>
      </div>

    </div>
  </div>
</template>
<script>
/* @flow */
import PlayListItem from './PlayListItem';

export default {
  components: {
    PlayListItem,
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
    left             : 192px;
    position         : absolute;
    top              : -5px;
    width            : 8px;
    z-index          : 1;
  }

  .dropdown-play-list {
    position : relative;
    width    : 380px;
    &__headline {
      width   : 100%;
      padding : 4px 24px;
    }
    &__previous-song {
      background      : center center/65% no-repeat;
      cursor          : pointer;
      height          : 36px;
      transition      : all .3s;
      width           : 28px;
    }
    &__action {
      background      : center center/40% no-repeat;
      cursor          : pointer;
      height          : 36px;
      transition      : all .3s;
      width           : 36px;
    }
    &__next-song {
      background      : center center/65% no-repeat;
      cursor          : pointer;
      height          : 36px;
      transition      : all .3s;
      width           : 28px;
    }
    &__time {
      fl-center();
      fnt($grey-light, 10px, $weight-semibold, left);
      cursor: pointer;
      height: 36px;
      padding: 0 4px;
      transition: all .3s;
      &:hover {
        color : $grey;
      }
    }
    &__repeat {
      background      : center center/65% no-repeat;
      cursor          : pointer;
      height          : 36px;
      transition      : all .3s;
      width           : 28px;
    }
    &__shuffle {
      background      : center center/65% no-repeat;
      cursor          : pointer;
      height          : 36px;
      transition      : all .3s;
      width           : 28px;
    }
    &__sound {
      background      : left center/65% no-repeat;
      cursor          : pointer;
      height          : 36px;
      transition      : all .3s;
      width           : 32px;
      &:active {
        background left center/60% no-repeat
      }
    }
    &__main {
      min-height    : 140px;
      max-height    : 640px;
      overflow      : auto;
      margin-bottom : 32px;
    }
    &__footer {
      background-color : $background;
      border-top       : 1px solid $border;
      bottom           : 0;
      height           : 32px;
      padding          : 0 24px;
      position         : absolute;
      width            : 100%;
    }
    &__name-playlist {
      txt-ellipsis();
      fnt($text, 10px, $weight-bold, left);
      overflow: hidden;
      width: 166px;
    }
    &__settings {
      background      : center center/50% no-repeat;
      cursor          : pointer;
      height          : 36px;
      transition      : all .3s;
      width           : 28px;
    }
    &__like {
      background      : center center/50% no-repeat;
      cursor          : pointer;
      height          : 36px;
      transition      : all .3s;
      width           : 28px;
    }
    &__add-to-favorite {
      background      : center center/50% no-repeat;
      cursor          : pointer;
      height          : 36px;
      transition      : all .3s;
      width           : 28px;
    }
    &__share {
      background      : center center/65% no-repeat;
      cursor          : pointer;
      transition      : all .3s;
      height          : 36px;
      width           : 28px;
    }
  }

  .progress-bar {
    cursor : pointer;
    &__body {
      align-items      : center;
      background-color : $grey-light;
      border-radius    : 2px;
      display          : flex;
      height           : 3px;
      width            : 100%;
    }
    &__progress {
      background-color : $primary;
      border-radius    : 2px;
      height           : 3px;
      width            : 26%;
    }
  }

  .level {
    margin : 0;
  }
</style>
