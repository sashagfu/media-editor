<template>
  <div class="VerifyPage verify-page">
    <div class="verify-page__bg-pic"/>
    <div class="verify-page__bg"/>
    <div class="verify-page__content">
      <div class="verify-page__header header">
        <div class="header__left">
          <div class="header__item">
            <div class="header__logo"/>
          </div>
          <div class="header__item">
            <h1 class="header__title">
              {{ trans('common.logo') }}
            </h1>
          </div>
        </div>
        <div class="header__right">
          <div class="nav-item"/>
        </div>
      </div>
      <div class="verify-page__body body">
        <div class="body__left">
          <h1 class="body__title">
            Welcome to the Biggest Social Network in the World
          </h1>
          <p class="has-text-left body__text">
            We are the best and biggest social network with 5 billion active
            users all around the world. Share you thoughts, write blog posts, show your
            favourite music via Spotify, earn badges and much more!
          </p>
          <div class="verify-page__btn-box">
            <!-- <el-button
              type="primary"
              plain
              class="verify-page__register-btn"
              @click="showPanel('RegisterBox')"
            >
              {{ trans('auth.register') }}
            </el-button> -->
          </div>
        </div>
        <div class="body__right">
          <VerifyBox
            :csrf-token="csrfToken"
            :verify-link="verifyLink"
          />
        </div>
      </div>
      <div class="verify-page__footer footer">
        <div class="footer__title">
          copyright
          <a
            href="#"
            class="footer__link"
          >
            {{ trans('common.logo') }}
          </a> all rights reserved 2018
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import VerifyBox from './VerifyBox';

export default {
  components: {
    VerifyBox,
  },
  props: {
    csrfToken: {
      type: String,
      default: '',
    },
    verifyLink: {
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
  @import '../../../../sass/front/components/bulma-theme';

  .verify-page {
    min-height 100vh
    position relative
    &__bg {
      background-color rgba($white, .97)
      height 100%
      left 0
      overflow hidden
      position absolute
      top 0
      width 100%
    }
    &__bg-pic {
      background url('../../../../img/bg/landing-users.png')
      bottom 0
      filter grayscale(1)
      height 400%
      left 0
      position absolute
      width 100%
    }
    &__content {
      display flex
      flex-direction column
      justify-content space-between
      min-height 100vh
      position relative
    }
    &__register-btn {
      font-size 13px
      height 56px
      transition all .3s
      width 224px
    }
    &__btn-box {
      fl-left()
    }
  }

  .header {
    fl-center()
    padding 1.5rem
    &__left {
      fl-left()
      width 47%
      +tablet() {
        width 100%
        justify-content space-between
      }
    }
    &__right {
      fl-right()
      width 47%
      +tablet() {
        display none
      }
    }
    &__title {
      fnt($text-strong, 2rem, 900, left)
      padding-left 2rem
    }
    &__logo {
      background url('../../../../img/logo/actionime-logo.png') center center/contain no-repeat
      height 72px
      width 72px
      +tablet() {
        height 42px
        width 42px
      }
    }
    &__menu-item {
      fnt($primary, 1rem, 900, right)
      cursor pointer
      text-decoration underline
      text-transform lowercase
      transition all .3s
      &:hover {
        color $lime-hover
      }
    }
  }

  .footer {
    fl-center()
    background transparent
    padding 1.5rem
    width 100%
    +tablet() {
      padding 0 0 1rem 0 !important
    }
    &__title {
      fnt($text, .8rem, $weight-light, center)
      text-transform capitalize
    }
    &__link {
      fnt($primary, .8rem, $weight-light, center)
      text-decoration none
    }
  }

  .body {
    display flex
    justify-content center
    padding 1.5rem
    +tablet() {
      align-items center
      flex-direction column
    }
    &__left {
      display flex
      flex-direction column
      justify-content center
      width 47%
      +tablet() {
        width 100%
        margin-bottom 1rem
      }
    }
    &__right {
      fl-center();
      width 47%
      +tablet() {
        width 100%
      }
    }
    &__title {
      fnt($text-strong, 3.5rem, $weight-semibold, left)
      margin-bottom 1.5rem
      line-height 1.1
      +tablet() {
        fnt($text-strong, 2rem, $weight-semibold, left)
        margin-bottom 1rem
        line-height 1
      }
      +desktop() {
        fnt($text-strong, 2rem, $weight-semibold, left)
        margin-bottom 1rem
        line-height 1
      }
    }
    &__text {
      fnt($text, 1rem, $weight-normal, left)
      margin-bottom 1.5rem

      +tablet() {
        fnt($text, .875rem, $weight-normal, left)
        margin-bottom 1rem
      }
    }
    &__picture {
      background url('../../../../img/bg/greet-lime.png') center center/contain no-repeat
      height 472px
      width 472px
      +tablet() {
        height 379px
        width 272px
      }
    }
  }

  .button {
    background-color $white
    font-size 14px
    font-weight 700
    height 54px
    transition all .3s
    width 200px
    &:hover {
      background-color $lime !important
    }
    +tablet() {
      height 34px
      width 100%
    }
  }
</style>
