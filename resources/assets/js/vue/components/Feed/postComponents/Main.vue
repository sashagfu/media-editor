<template>
  <div class="Main main">
    <div class="main__text">
      <div
        class="main__text-item"
        v-html="content"
      />
    </div>
  </div>
</template>

<script>
export default {
  components: {},
  props: {
    content: {
      type: String,
      default: '',
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .main {
    &__text {
      fnt($text-light, 14px, $weight-light, left);
      padding    : 0 26px;
      text-align : left;
    }
    &__text-item {
      padding-bottom : 24px;
      &:last-child {
        padding-bottom : 8px;
      }
    }
  }
</style>
