<template>
  <div class="SearchResultBar sr-bar box">
    <div class="sr-bar__left">
      <div
        v-if="header"
        class="sr-bar__title">
        {{ header }}
      </div>
      <slot
        v-else
        name="header"
      />
    </div>
    <div class="sr-bar__right">
      <el-button
        v-if="showButton"
        :disabled="!showMore"
        type="primary"
        class="sr-bar__btn"
        size="mini"
        plain
        @click="handleShowMore"
      >
        {{ trans('common.show_more') }}
      </el-button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SearchResultBar',
  props: {
    header: {
      type: String,
      default: '',
    },
    showMore: {
      type: Boolean,
      default: false,
    },
    showButton: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    handleShowMore() {
      this.$emit('show-more');
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .sr-bar {
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
