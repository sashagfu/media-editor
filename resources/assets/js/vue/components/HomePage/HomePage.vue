<template>
  <div class="HomePage home-page home-page__column">
    <div class="home-page__content">
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
        <div class="header__right">
          <div class="nav-item">
            <el-button
              size="mini"
              plain
              @click="showPanel('RegisterBox')"
            >
              {{ trans('auth.reg') }}
            </el-button>
            <el-button
              size="mini"
              plain
              @click="showPanel('LoginBox')"
            >
              {{ trans('auth.login') }}
            </el-button>
          </div>
        </div>
      </div>
      <div class="home-page__body body">
        <div class="body__left">
          <h1 class="body__title">
            Credit for every view
          </h1>
          <p class="has-text-left body__text">
            Add some zest to your videos by creatively
            collaborating and get credit every time you are clipped in another video.
          </p>
          <div class="home-page__btn-box">
            <!-- <el-button
              type="primary"
              plain
              class="home-page__register-btn"
              @click="showPanel('RegisterBox')"
            >
              {{ trans('auth.register') }}
            </el-button> -->
            <el-button
              type="danger"
              plain
              class="home-page__register-btn"
              @click="showPanel('RegisterBetaBox')"
            >
              {{ trans('auth.register_beta') }}
            </el-button>
          </div>
        </div>
        <div class="body__right">
          <component
            v-if="login"
            ref="loginRegisterMenu"
            :is="showBox"
            :login-url="loginUrl"
            :register-url="registerUrl"
            :csrf-token="csrfToken"
            :show-component="showComponent"
          />
          <div
            v-else
            class="body__picture"
          />
        </div>
      </div>
      <div class="home-page__footer footer">
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
import { mapGetters, mapActions } from 'vuex';
import LoginAndRegisterMenu from './LoginAndRegisterMenu';
import ForgetPasswordBox from './ForgetPasswordBox';

const HomePage = {
  name: 'home-page',
  components: {
    LoginAndRegisterMenu,
    ForgetPasswordBox,
  },
  props: {
    loginUrl: {
      type: String,
      default: '',
    },
    registerUrl: {
      type: String,
      default: '',
    },
    csrfToken: {
      type: String,
      default: '',
    },
    errors: {
      type: Array,
      default() {
        return [];
      },
    },
  },
  data() {
    return {
      login: false,
      showComponent: '',
      showBox: 'LoginAndRegisterMenu',
    };
  },
  created() {
    this.login = true;
    // Set register box by default
    if (window.location.pathname === '/' || window.location.pathname === '/register') {
      this.login = true;
      this.setHomePageActiveBox('RegisterBox');
    } else {
      this.setHomePageActiveBox('LoginBox');
    }
  },
  methods: {
    ...mapActions('general', [
      'setHomePageActiveBox',
    ]),
    showPanel(isComponent) {
      this.login = true;
      this.setHomePageActiveBox(isComponent);
    },
  },
  computed: {
    ...mapGetters('general', [
      'homePageActiveBox',
    ]),
  },
  watch: {
    homePageActiveBox(newVal) {
      if (newVal === 'ForgetPasswordBox') {
        this.showBox = 'ForgetPasswordBox';
      } else {
        this.showBox = 'LoginAndRegisterMenu';
      }
    },
  },
};

export default HomePage;
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .home-page {
    min-height 100vh
    position relative

    &__column {
      position: absolute;
      left: 0;
      right: 0;
    }

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

      .nav-item {
        & >>> .el-button {
          box-shadow 0px 0px 20px 0px rgba(54, 54, 54, 0.1)
        }
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
      margin-bottom 140px
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
