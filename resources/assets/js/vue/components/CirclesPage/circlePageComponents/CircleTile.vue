<template>
  <div class="pg-tile">
    <div class="pg-tile__header">
      <div
        :style="{'background-image':`url(${circle.lastCover})`}"
        class="header-photo"
      />
      <div class="header-menu level">
        <div class="level-item "/>
      </div>
      <div class="header-title">
        <div class="header-title__title">
          {{ circle.title }}
        </div>
        <div class="header-title__sub-title">
          {{ trans('circles.type') }}: {{ circle.type }}
        </div>
      </div>
    </div>
    <div class="pg-tile__main level">
      <div class="level-item pg-tile__main-box">
        <StatsAndAction :circle="circle"/>
      </div>
    </div>
  </div>
</template>

<script>
/* @flow */
import StatsAndAction from './StatsAndAction';

export default {
  components: {
    StatsAndAction,
  },
  props: {
    circle: {
      type: Object,
      default: () => ({}),
    },
  },
  methods: {},
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .header-photo {
    width            : 100%;
    height           : 120px;
    background-color : $turquoise;
    box-shadow       : inset 0px -48px 106px -24px rgba($black-ter, 0.6);
    border-radius    : 3px 3px 0 0;
    border-bottom    : 1px solid $border;
  }

  .header-menu {
    height : 32px;
    width  : 100%;
  }

  .level-item {
    position : relative;
  }

  .pg-tile-avatar {
    position      : absolute;
    top           : -64px;
    width         : 94px;
    height        : 94px;
    border        : 4px solid $background;
    border-radius : 50%;
    background    : center center no-repeat $grey-lighter;
  }

  .header-title {
    padding        : 12px 0;
    display        : flex;
    flex-direction : column;
    align-items    : center;

    &__title {
      padding     : 4px;
      font-size   : 16px;
      font-weight : $weight-semibold;
      color       : $text;
    }
    &__sub-title {
      font-size   : 11px;
      font-weight : $weight-normal;
      color       : $text-lighter;
    }
  }

  .header-menu.level {
    margin-bottom : 0;
  }

  .slider-dot {
    cursor           : pointer;
    width            : 8px;
    height           : 8px;
    border-radius    : 100%;
    border           : 1px solid $border;
    background-color : transparent;
    margin           : 18px 4px;
    &--active {
      border           : none;
      background-color : $primary;
    }
  }

  .pg-tile-settings {
    position    : relative;
    font-size   : 16px;
    font-weight : 900;
    color       : $text-lighter;
    &:hover {
      color : $text;
    }
    &__title {
      position : relative;
    }
    &__dropdown-menu {
      position   : absolute;
      top        : 24px;
      left       : -96px;
      display    : none;
      z-index    : 1;

      box-shadow : 10px 10px 30px 5px rgba(45, 47, 62, 0.05);
    }
    &:hover > &__dropdown-menu {
      display : flex;
    }
  }

  .pg-tile {
    &__main {
      min-height : 152px;
    }
    &__main-box {
      width : 216px;
    }
  }

  .arrow {
    cursor     : pointer;
    height     : 100%;
    width      : 36px;
    background : center center no-repeat;
    transition : all .3s;
    outline    : none;
    &--left {
      background-image : url('../../../../../img/arrow/arrow-left-n.png');
      opacity          : 0;
      &:hover {
        background-image : url('../../../../../img/arrow/arrow-left-n.png');
        opacity          : 1;
      }
      &:active {
        background-image : url('../../../../../img/arrow/arrow-left-n--focus.png');
      }
    }
    &--right {
      background-image : url('../../../../../img/arrow/arrow-right-n.png');
      opacity          : 0;
      &:hover {
        background-image : url('../../../../../img/arrow/arrow-right-n.png');
        opacity          : 1;
      }
      &:active {
        background-image : url('../../../../../img/arrow/arrow-right-n--focus.png');
      }
    }
  }
</style>
