<template>
  <div class="CommentFooterV2 comment-footer level is-marginless">
    <div class="level-left comment-likes">
      <div class="comment-likes__icon"/>
      <div class="comment-likes__number">
        8
      </div>
      <div class="comment-likes__reply">
        Reply
      </div>
      <div class="comment-likes__time">
        1 hour ago
      </div>
      <div class="comment-likes__settings"/>
    </div>
    <div class="level-right"/>
  </div>
</template>

<script>
export default {
  components: {},
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .comment-footer {
    height  : 56px;
    width   : 100%;
    padding : 0 26px;
  }

  .comment-likes {
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
      color        : $text-lighter;
      font-size    : 14px;
      font-weight  : $weight-normal;
      margin-right : 24px;
    }
    &__reply {
      color        : $text-lighter;
      cursor       : pointer;
      font-size    : 12px;
      font-weight  : $weight-normal;
      margin-right : 24px;
      text-align   : left;
      transition   : all .3s;
      &:hover {
        color       : $text;
        font-weight : $weight-normal+100;
      }
      &:active {
        color       : $primary;
        font-weight : $weight-normal+100;
      }
    }
    &__time {
      color        : $text-lighter;
      font-size    : 12px;
      font-weight  : $weight-normal;
      margin-right : 16px;
    }
    &__settings {
      background : url('../../../../../img/three-dots-icon--light.png') center center no-repeat;
      height     : 40px;
      width      : 44px;
      &:hover {
        background-image : url('../../../../../img/three-dots-icon--dark.png');
      }
    }
  }
</style>
