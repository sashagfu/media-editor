<template>
  <div class="FooterWithMedallions post-footer is-marginless">
    <div class="post-footer__left">
      <div class="post-footer__item">
        <Likes v-bind="$props"/>
      </div>
      <div class="post-footer__item">
        <UserAvaMedallions
          v-if="post.isPerformance"
          :post="post"
          :actions="post.stars"
        />
        <UserAvaMedallions
          v-else
          :post="post"
          :actions="post.likes"
        />
      </div>
    </div>
    <div class="post-footer__right">
      <div class="post-footer__item">
        <ShowComments :post="post"/>
      </div>
      <div
        v-if="post.assets && post.assets.length"
        class="post-footer__item">
        <SaveContent :post="post"/>
      </div>
      <div class="post-footer__item">
        <PostShares :post="post"/>
      </div>
    </div>
  </div>
</template>

<script>
import UserAvaMedallions from '../footerComponents/UserAvaMedallions';
import Likes from '../footerComponents/Likes';
import ShowComments from '../footerComponents/ShowComments';
import PostShares from '../footerComponents/PostShares';
import SaveContent from '../footerComponents/SaveContent';

const FooterWithMedallions = {
  name: 'footer-with-medallions',
  components: {
    UserAvaMedallions,
    Likes,
    ShowComments,
    PostShares,
    SaveContent,
  },
  props: {
    post: {
      type: Object,
      default() {
        return {};
      },
    },
    clip: {
      type: Object,
      default() {
        return {};
      },
    },
    user: {
      type: Object,
      default() {
        return {};
      },
    },
  },
};

export default FooterWithMedallions;
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .post-footer {
    fl-between()
    height 44px
    padding 0px 24px 2px
    width 100%
    +tablet() {
      flex-direction column
      align-items flex-start
    }
    &__left {
      fl-left()
      height 100%
      +tablet() {
        justify-content flex-start
      }
    }
    &__right {
      fl-right()
      height 100%
      +tablet() {
        justify-content flex-start
        align-items flex-start
      }
    }
    &__item {
      fl-left()
      height 100%
    }
    &__item + &__item {
      margin-left : 4px;
    }
  }

</style>
