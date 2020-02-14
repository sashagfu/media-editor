<template>
  <div class="CommentMain comment-main">
    <div class="comment-main__text">
      {{ text }}
    </div>
  </div>
</template>

<script>
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

  .comment-main
    &__text
      fnt($text-light, 14px, $weight-light, left)
      padding 0 24px
      &:last-child
        padding-bottom 8px

</style>
