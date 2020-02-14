<template>
  <div class="CommentMain coment-main">
    <div class="main__text">
      <p class="main__text-item">
        {{ text }}
      </p>
    </div>
  </div>
</template>

<script>
/* @flow */
export default {
  props: {
    comment: {
      type: Object,
      default: () => ({}),
    },
    text: {
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
      padding : 0 24px;
    }
    &__text-item {
      padding-bottom : 24px;
      &:last-child {
        padding-bottom : 8px;
      }
    }
  }
</style>
