<template>
  <div class="MyFeedNavBar myn-bar box">
    <div class="myn-bar__left">
      <div class="myn-bar__title">
        {{ trans('feed.my_feed') }}
      </div>
    </div>
    <div class="myn-bar__right">
      <el-button
        :type="showAll ? '' : 'primary'"
        class="myn-bar__btn"
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
  name: 'MyFeedNavBar',
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

  .myn-bar
    fl-between()
    height 56px
    margin-bottom 24px
    padding 0 24px
    &__left
      fl-left()
    &__right
      fl-right()
    &__avatar
      background center center/cover no-repeat $grey-lighter
      border-radius 50%
      height 40px
      width 40px
    &__title
      fnt($text, 14px, $weight-semibold, left)
      text-transform capitalize
    &__btn
      width 88px

</style>
