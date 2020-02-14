<template>
  <div class="head-line level is-marginless">
    <div class="level-left">
      <div class="head-line__title level-item">
        {{ header }}
      </div>
    </div>
    <div
      v-if="menu.length"
      class="level-right"
    >
      <div class="head-line__setting-box level-item">
        <div class="head-line__settings">
          <font-awesome-icon
            icon="ellipsis-h"
            class="fa-icon fa-icon--pointer"
          />
        </div>
        <div class="head-line__dropdown box">
          <a
            v-for="(menuItem, key) in menu"
            :key="key"
            :href="menuItem.url"
            class="head-line__menu-item"
          >
            {{ menuItem.title }}
          </a>
        </div>
      </div>

    </div>
  </div>
</template>

<script>

export default {
  components: {},
  props: {
    header: {
      type: String,
      default: '',
    },
    menu: {
      type: [Array, Object],
      default: () => [],
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
    color      : $text-light;
    transition : all .3s;
    &:hover {
      color : $text-lighter;
    }
    &--pointer {
      cursor : pointer;
    }
  }

  .head-line {
    fl-between();
    border-bottom : 1px solid $border;
    height        : 60px;
    padding       : 0 26px;
    width         : 100%;
    &__title {
      fnt($text, 14px, $weight-semibold, left);
    }
    &__settings {
      fl-right();
      cursor          : pointer;
      height          : 60px;
      width           : 40px;
    }
    &__setting-box {
      height   : 60px;
      position : relative;
      width    : 40px;
    }
    &__dropdown {
      display        : none;
      flex-direction : column;
      padding        : 16px 18px;
      position       : absolute;
      right          : 8px;
      top            : 36px;
      z-index        : 5;
    }
    &__setting-box:hover &__dropdown {
      display : flex;
    }

    &__menu-item {
      fnt($text-light, 12px, $weight-normal, left);
      &:hover {
        color       : $text;
        font-weight : $weight-semibold;
      }
    }
  }
</style>
