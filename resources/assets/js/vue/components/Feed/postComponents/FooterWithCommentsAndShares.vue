<template>
  <div class="FooterWithCommentsAndShares">
    <Divider/>
    <div class="post-footer-with-coments-and-shares level is-marginless">
      <div class="level-left blog-post-likes">
        <div class="blog-post-likes__icon"/>
        <div class="blog-post-likes__number">
          8
        </div>
        <div class="blog-post-likes__title">
          Likes
        </div>
      </div>
      <div class="level-right">
        <div class="level-item blog-post-comments">
          <div class="blog-post-comments__icon"/>
          <div class="blog-post-comments__number">
            12
          </div>
          <div class="blog-post-comments__title">
            Comments
          </div>
        </div>
        <div class="level-item blog-post-shares">
          <div class="blog-post-shares__icon"/>
          <div class="blog-post-shares__number">
            0
          </div>
          <div class="blog-post-shares__title">
            Shares
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
/* @flow */
import Divider from './Divider';

export default {
  components: {
    Divider,
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .post-footer-with-coments-and-shares {
    height  : 72px;
    width   : 100%;
    padding : 0 26px;
  }

  .blog-post-likes {
    &__icon {
      background-image : url('../../../../../img/heart-icon--light.png')
      center center/90% no-repeat;
      flex             : 0 0 auto;
      height           : 24px;
      margin-right     : 4px;
      width            : 24px;
    }
    &__number {
      fnt($text-lighter, 14px, $weight-normal, left);
      margin-right : 8px;
    }
    &__title {
      fnt($text-lighter, 14px, $weight-normal, left);
      margin-right : 8px;
    }
  }

  .blog-post-comments {
    &__icon {
      background : url('../../../../../img/speech-balloon-icon--light.png')
      center center/90% no-repeat;
      flex       : 0 0 auto;
      height     : 24px;
      width      : 24px;
    }
    &__number {
      fnt($text-lighter, 14px, $weight-normal, left);
      margin-left : 4px;
    }
    &__title {
      fnt($text-lighter, 14px, $weight-normal, left);
      margin-left : 4px;
    }
  }

  .blog-post-shares {
    &__icon {

      background  : url('../../../../../img/share-icon--light.png') center center no-repeat;
      //background-size: 90%;
      flex        : 0 0 auto;
      height      : 24px;
      margin-left : 16px;
      width       : 24px;
    }
    &__number {
      color       : $text-lighter;
      font-size   : 14px;
      font-weight : $weight-normal;
      margin-left : 8px;
    }
    &__title {
      fnt($text-lighter, 14px, $weight-normal, left);
      margin-left : 8px;
    }
  }
</style>
