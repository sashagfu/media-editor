<template>
  <div class="CircleMemberTile friend-tile">
    <div class="friend-tile__header">
      <div class="header-photo"/>
      <div class="header-menu level">
        <div class="level-item">
          <a
            :href="`/profile/${member.username}`"
            :style="{backgroundImage: `url(${member.avatar})`}"
            class="friend-avatar"
          />
        </div>
      </div>
      <div class="header-title">
        <a
          :href="`/profile/${member.username}`"
          class="header-title__title"
        >
          {{ member.display_name }}
        </a>
        <a
          :href="`/profile/${member.username}`"
          class="header-title__place"
        >
          @{{ member.username }}
        </a>
      </div>
    </div>
    <div class="friend-tile__main level">
      <div class="level-item friend-tile__main-box">
        <component
          :is="friendsComponentBox"
          :member="member"
        />
      </div>
    </div>
  </div>
</template>

<script>
import StatsAndAction from './StatsAndAction';

const CircleMemberTile = {
  name: 'circle-member-tile',
  components: {
    StatsAndAction,
  },
  props: {
    member: {
      type: Object,
      default() {
        return {};
      },
    },
  },
  data() {
    return {
      friendsComponentBox: 'StatsAndAction',
    };
  },
};

export default CircleMemberTile;
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../../sass/front/components/bulma-theme';

  .header-photo {
    background-color : $primary;
    border-bottom    : 1px solid $border;
    border-radius    : 3px 3px 0 0;
    height           : 120px;
    width            : 100%;
  }

  .header-menu {
    height : 32px;
    width  : 100%;
  }

  .level-item {
    position : relative;
  }

  .friend-avatar {
    background-size : cover;
    border-radius   : 100%;
    border          : 4px solid $background;
    height          : 94px;
    position        : absolute;
    top             : -64px;
    width           : 94px;
  }

  .header-title {
    align-items    : center;
    display        : flex;
    flex-direction : column;
    padding        : 12px 0;

    &__title {
      fnt($text, 16px, $weight-semibold, center);
      padding : 4px;

    }
    &__place {
      fnt($text-lighter, 11px, $weight-normal, center);
    }
  }

  .header-menu.level {
    margin-bottom : 0;
  }

  .slider-dot {
    background-color : transparent;
    border-radius    : 100%;
    border           : 1px solid $border;
    cursor           : pointer;
    height           : 8px;
    margin           : 18px 4px;
    width            : 8px;
    &--active {
      border           : none;
      background-color : $primary;
    }
  }

  .friend-tile__settings {
    fnt($text-lighter, 16px, 900, left);
    &:hover {
      color : $text;
    }
  }

  .pg-tile-settings {
    fnt($text-lighter, 16px, 900, left);
    position : relative;
    &:hover {
      color : $text;
    }
    &__title {
      position : relative;
    }
    &__dropdown-menu {
      display    : none;
      left       : -96px;
      position   : absolute;
      top        : 24px;
      z-index    : 1;

      box-shadow : 10px 10px 30px 5px rgba(45, 47, 62, .05);
    }
    &:hover > &__dropdown-menu {
      display : flex;
    }
  }

  .friend-tile__main {
    height : 152px;
  }

  .friend-tile__main-box {
    width : 216px;
  }

  .arrow {
    background : center center no-repeat;
    height     : 100%;
    outline    : none;
    transition : all .3s;
    width      : 36px;
    &--left {
      background-image : url('../../../../../../img/arrow/arrow-left-n.png');
      opacity          : 0;
      &:hover {
        background-image : url('../../../../../../img/arrow/arrow-left-n.png');
        opacity          : 1;
      }
      &:active {
        background-image : url('../../../../../../img/arrow/arrow-left-n--focus.png');
      }
    }
    &--right {
      background-image : url('../../../../../../img/arrow/arrow-right-n.png');
      opacity          : 0;
      &:hover {
        background-image : url('../../../../../../img/arrow/arrow-right-n.png');
        opacity          : 1;
      }
      &:active {
        background-image : url('../../../../../../img/arrow/arrow-right-n--focus.png');
      }
    }
  }
</style>
