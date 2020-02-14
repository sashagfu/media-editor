<template>
  <div class="RecommendedNavBar rn-bar box">
    <div class="rn-bar__left">
      <div class="rn-bar__title">
        {{ trans('feed.recommended') }}
      </div>
    </div>
    <div class="rn-bar__right">
      <el-button
        :type="showAll ? '' : 'primary'"
        class="rn-bar__btn"
        size="mini"
        plain
        @click="toggleShowAll"
      >
        <template v-if="showAll">
          {{ trans('feed.show_less') }}
        </template>
        <template v-else>
          {{ trans('feed.show_all') }}
        </template>
      </el-button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RecommendedNavBar',
  props: {
    showAll: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    toggleShowAll() {
      this.$emit('toggle-show-all');
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .rn-bar {
    fl-between();
    height: 56px;
    margin-bottom: 24px;
    padding: 0 24px;
    &__left {
      fl-left();
    }
    &__right {
      fl-right();
    }
    &__avatar {
      background: center center/cover no-repeat $grey-lighter;
      border-radius: 50%;
      height: 40px;
      width: 40px;
    }
    &__title {
      fnt($text, 14px, $weight-semibold, left);
      text-transform: capitalize;
    }
    &__btn {
      width: 88px;
    }
  }
</style>
