<template>
  <div class="Footer post-footer level is-marginless">
    <div class="level-left blog-post-likes">
      <div class="blog-post-likes__icon"/>
      <div class="blog-post-likes__number">8</div>
      <div class="blog-post-likes__reply">Reply</div>
    </div>
    <div class="level-right"/>
  </div>
</template>

<script>
/* @flow */
export default {
  components: {},
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .post-footer {
    height  : 56px;
    padding : 0 26px;
    width   : 100%;
  }

  .blog-post-likes {
    &__icon {
      background   : url('../../../../../img/heart-icon--light.png') center center/90% no-repeat;
      cursor       : pointer;
      flex         : 0 0 auto;
      height       : 24px;
      margin-right : 4px;
      transition   : all .1s;
      width        : 24px;
      &:hover {
        background-image : url('../../../../../img/heart-icon--dark.png');
      }
      &:active {
        background-image : url('../../../../../img/heart-icon--primary.png');
      }
      &:hover + &__number {
        color : $text-light;
      }
      &:active + &__number {
        color : $primary;
      }

    }
    &__number {
      fnt($text-lighter, 14px, $weight-normal, left);
      margin-right : 24px;
    }
    &__reply {
      fnt($text-lighter, 12px, $weight-normal, left);
      cursor      : pointer;
      margin-left : 8px;
      text-align  : left;
      transition  : all .3s;
      width       : 104px;
      &:hover {
        color       : $text;
        font-weight : $weight-normal+100;
      }
      &:active {
        color       : $primary;
        font-weight : $weight-normal+100;
      }
    }
  }
</style>
