<template>
  <div
    :title="trans('common.show_more')"
    class="ShowMoreButton shm-btn"
    @click="showMore"
  >
    <!-- <font-awesome-icon
      :icon="['fas', 'ellipsis-h']"
      class="fa-icon"
    /> -->
    <el-button
      class="shm-btn__btn"
      size="mini"
      plain
    >
      {{ trans('common.show_more') }}
    </el-button>
  </div>
</template>

<script>
export default {
  name: 'ShowMoreButton',
  methods: {
    showMore() {
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

  .fa-icon {
    fnt($text-lighter, 16px, $weight-normal, center);
    transition: all .3s;
  }

  .shm-btn
    fl-center()
    cursor pointer
    margin-bottom 1.5rem
    transition all .3s
    &:hover .fa-icon
      color $text-light
    &__btn
      width 88px

</style>
