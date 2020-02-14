<template>
  <div class="ResetPasswordPage home-page">
    <div class="home-page__content">
      <!-- HEADER -->
      <div class="home-page__header header">
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
        <div class="header__right"/>
      </div>
      <!-- MAIN -->
      <div class="home-page__body body">
        <div class="body__left">
          <h1 class="body__title">
            Welcome to the Biggest Social Network in the World
          </h1>
          <p class="has-text-left body__text">
            We are the best and biggest social network with 5 billion active
            users all around the world. Share you thoughts, write blog posts, show your
            favourite music via Spotify, earn badges and much more!
          </p>
        </div>
        <div class="body__right">
          <ResetPasswordBox/>
        </div>
      </div>
      <!-- FOOTER -->
      <div class="home-page__footer footer">
        <div class="footer__title">
          copyright
          <a
            href="#"
            class="footer__link"
          >
            {{ trans('common.logo') }}
          </a>
          all rights reserved 2018
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ResetPasswordBox from './ResetPasswordBox';

export default {
  components: {
    ResetPasswordBox,
  },
  props: {},
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .home-page {
    position   : relative;
    min-height : 100vh;
    &__bg {
      background-color : rgba($white, .97);
      height           : 100%;
      left             : 0;
      overflow         : hidden;
      position         : absolute;
      top              : 0;
      width            : 100%;
    }
    &__bg-pic {
      background : url('../../../../img/bg/landing-users.png');
      bottom     : 0;
      filter     : grayscale(1);
      height     : 400%;
      left       : 0;
      position   : absolute;
      width      : 200%;
    }
    &__content {
      position        : relative;
      display         : flex;
      flex-direction  : column;
      justify-content : space-between;
      min-height      : 100vh;
    }

  }

  .header {
    align-items     : center;
    display         : flex;
    justify-content : center;
    padding         : 1.5rem;

    &__left {
      // border: 1px solid red;
      align-items : center;
      display     : flex;
      width       : 47%;
      +tablet() {
        width           : 100%;
        justify-content : space-between;
      }
    }
    &__right {
      // border: 1px solid green;
      align-items     : center;
      display         : flex;
      justify-content : flex-end;
      width           : 47%;
      +tablet() {
        display : none;
      }
    }
    &__item {
    }
    &__title {
      fnt($text-strong, 2rem, 900, left);
      padding-left : 2rem;
    }
    &__logo {
      background : url('../../../../img/logo/actionime-logo.png') center center/contain no-repeat;
      height     : 72px;
      width      : 72px;
      +tablet() {
        height : 42px;
        width  : 42px;
      }
    }
    &__menu-item {
      fnt($primary, 1rem, 900, right);
      transition      : all .3s;
      cursor          : pointer;
      text-decoration : underline;
      text-transform  : lowercase;
      &:hover {
        color : $lime-hover;
      }
    }
  }

  .footer {
    // border: 1px solid green;
    width           : 100%;
    align-items     : center;
    display         : flex;
    justify-content : center;
    padding         : 1.5rem;
    background      : transparent;
    +tablet() {
      padding : 0 0 1rem 0 !important;
    }
    &__title {
      fnt($text, .8rem, $weight-light, center);
      text-transform : capitalize;
    }
    &__link {
      fnt($primary, .8rem, $weight-light, center);
      text-decoration : none;
    }
  }

  .body {
    display         : flex;
    justify-content : center;
    padding         : 1.5rem;
    +tablet() {
      flex-direction : column;
      align-items    : center;
    }
    &__left {
      // border: 1px solid green;
      display         : flex;
      flex-direction  : column;
      justify-content : center;
      width           : 47%;
      +tablet() {
        width         : 100%;
        margin-bottom : 1rem;
      }
    }
    &__right {
      // border: 1px solid red;
      align-items     : center;
      display         : flex;
      justify-content : center;
      width           : 47%;
      +tablet() {
        width : 100%;
      }
    }
    &__title {
      fnt($text-strong, 3.5rem, $weight-semibold, left);
      margin-bottom : 1.5rem;
      line-height   : 1.1;
      +tablet() {
        fnt($text-strong, 2rem, $weight-semibold, left);
        margin-bottom : 1rem;
        line-height   : 1;
      }
      +desktop() {
        fnt($text-strong, 2rem, $weight-semibold, left);
        margin-bottom : 1rem;
        line-height   : 1;
      }
    }
    &__text {
      fnt($text, 1rem, $weight-normal, left);
      margin-bottom : 1.5rem;

      +tablet() {
        fnt($text, .875rem, $weight-normal, left);
        margin-bottom : 1rem;
      }
      +desktop() {
        fnt($text, .875rem, $weight-normal, left);
        margin-bottom : 1rem;
      }
    }
    &__picture {
      background : url('../../../../img/bg/greet-lime.png') center center/contain no-repeat;
      height     : 472px;
      width      : 472px;
      +tablet() {
        height : 379px;
        width  : 272px;
      }
    }
  }
</style>
