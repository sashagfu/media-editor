<template>
  <div class="hero is-fullheight is-primary">
    <div class="nav">
      <div class="nav-left ">
        <div class="nav-item">
          <div class="nav-title__logo"/>
          <h1 class="title nav-title__title">
            {{ trans('common.yuustar') }}
          </h1>
        </div>
      </div>
      <div class="nav-right">
        <div class="nav-item"/>
      </div>
    </div>

    <div class="hero-body">
      <div class="container">
        <div class="columns is-gapless">
          <div class="column">
            <div class="hero-body__item is-pulled-left">
              <h1 class="title is-1 has-text-left title--puled-left">
                Welcome to the Biggest Social Network
                in the World
              </h1>
            </div>
            <div class="hero-body__item hero-body__item--with-limit is-pulled-left">
              <p class="has-text-left ">
                We are the best and biggest social network with 5 billion active
                users all around the world. Share you thoughts,
                write blog posts, show your
                favourite music via Stopify, earn badges and much more!
              </p>
            </div>
          </div>
          <div class="column">
            <div class="hero-body-box is-pulled-left">
              <ForgetPasswordBox/>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
</template>

<script>
import ForgetPasswordBox from './ForgetPasswordBox';

export default {
  components: {
    ForgetPasswordBox,
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .nav-title__logo {
    background : url('../../../../img/logo/logo-dark.png') center center no-repeat;
    height     : 72px;
    width      : 72px;
  }

  .nav-title__title {
    font-weight : 900;
  }

  .nav-item {
    padding-left : 100px;
    padding-top  : 32px;
  }

  .hero.is-primary .nav, .hero.is-primary .nav {
    box-shadow : none;
  }

  .title--puled-left {
    text-align : left;
    font-size  : 3.2rem;
  }

  .column {
    display         : flex;
    flex-direction  : column;
    justify-content : center;
  }

  .hero-body__item {
    padding-top : 40px;
    &:first-child {
      padding-top : 0;
    }
    &:last-child {
      padding-top : 64px;
    }
    &--with-limit {
      width : 428px;
    }
  }

  .button {
    transition  : all .3s;
    font-size   : 14px;
    font-weight : 700;
    height      : 54px;
    width       : 200px;
    &:hover {
      //color: $primary !important;
    }
  }
</style>
