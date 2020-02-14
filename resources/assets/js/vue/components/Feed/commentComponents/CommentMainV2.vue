<template>
  <div class="CommentMainV2 comment-main-v2">
    <div class="comment-main-v2__ava"/>
    <div class="comment-main-v2__text">
      <span class="comment-main-v2__user">
        Elaine Dreyfuss
      </span>
      Sed ut perspiciatis unde omnis iste natus
      error sit lorem voluptatem accusantium der doloremque laudantium.
    </div>
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

  .comment-main-v2 {
    align-items : center;
    display     : flex;
    padding     : 26px;
    &__ava {
      background    : url('http://lorempixel.com/26/26/people') center center no-repeat $grey-light;
      border-radius : 100%;
      flex          : 0 0 auto;
      height        : 26px;
      margin-right  : 16px;
      width         : 26px;
    }
    &__text {
      color       : $text-light;
      font-size   : 14px;
      font-weight : $weight-light;
      text-align  : left;
    }
    &__user {
      color       : $text;
      font-weight : $weight-semibold;
    }

  }
</style>
