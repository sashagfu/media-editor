<template>
  <div class="menu-box">
    <div class="menu-box__button-box ">
      <div
        :class="[
          'button-box',
          ' button-box--login',
          {'button-box--hoverable':homePageActiveBox !== 'LoginBox'}
        ]"
        @click="setComponent('LoginBox')"
      >
        <div class="fa-icon__box">
          <font-awesome-icon
            :class="[
              'fa-icon',
              {'fa-icon--primary':homePageActiveBox === 'LoginBox'},
              {'fa-icon--hoverable':homePageActiveBox !== 'LoginBox'}
            ]"
            :icon="['fas', 'sign-in-alt']"
          />
        </div>
      </div>
      <div
        :class="[
          'button-box',
          ' button-box--register',
          {'button-box--hoverable':homePageActiveBox !== 'RegisterBetaBox'}
        ]"
        @click="setComponent('RegisterBetaBox')"
      >
        <div class="fa-icon__box">
          <font-awesome-icon
            :class="[
              'fa-icon',
              {'fa-icon--register':homePageActiveBox === 'RegisterBetaBox'},
              {'fa-icon--hoverable':homePageActiveBox !== 'RegisterBetaBox'}
            ]"
            :icon="['fas', 'flask']"
          />
        </div>
      </div>
      <!-- <div
        :class="[
          'button-box',
          ' button-box--accented',
          {'button-box--hoverable':homePageActiveBox !== 'RegisterBox'}
        ]"
        @click="setComponent('RegisterBox')"
      >
        <div class="fa-icon__box">
          <font-awesome-icon
            :class="[
              'fa-icon',
              {'fa-icon--accented':homePageActiveBox === 'RegisterBox'},
              {'fa-icon--hoverable':homePageActiveBox !== 'RegisterBox'}
            ]"
            :icon="['fas', 'user-plus']"
          />
        </div>
      </div> -->
    </div>
    <div class="menu-box__form-box">
      <component
        :login-url="loginUrl"
        :register-url="registerUrl"
        :csrf-token="csrfToken"
        :is="homePageActiveBox"
      />
    </div>
  </div>

</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import LoginBox from './LoginBox';
import RegisterBox from './RegisterBox';
import RegisterBetaBox from './RegisterBetaBox';

export default {
  components: {
    LoginBox,
    RegisterBox,
    RegisterBetaBox,
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
    showComponent: {
      type: String,
      default: 'LoginBox',
    },
  },
  data() {
    return {
      loginBox: 'LoginBox',
    };
  },
  computed: {
    ...mapGetters('general', [
      'homePageActiveBox',
    ]),
  },
  methods: {
    ...mapActions('general', [
      'setHomePageActiveBox',
    ]),
    setComponent(isComponent) {
      this.setHomePageActiveBox(isComponent);
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .fa-icon {
    color $text-light
    font-size 1rem
    transition all .2s
    &--hoverable {
      &:hover {
        opacity .75
      }
    }
    &--primary {
      color $primary
    }
    &--register {
      color $danger
    }
    &--accented {
      color $accented
    }
    &__box {
      fl-center()
      flex 0 0 auto
      height 24px
      transition all .3s
      width 24px
    }
  }

  .menu-box {
    background-color $white
    border 1px solid $border
    border-radius $radius
    box-shadow 0px 0px 20px 0px rgba($grey-darker, .1)
    display flex
    width 468px
    +tablet() {
      width 100%
      flex-direction column
    }
    +desktop() {
      height 620px
    }
    +widescreen() {
      height 620px
    }

    &__button-box {
      display flex
      flex-direction column
      height 100%
      justify-content center
      width 68px
      +tablet() {
        border-bottom 1px solid $border
        flex-direction row
        height 52px
        width 100%
      }
      +desktop() {
        border-right 1px solid $border
      }
      +widescreen() {
        border-right : 1px solid $border
      }
    }
    &__form-box {
      +tablet() {
        height 325px
        width 100%
      }
      +desktop() {
        width 400px
      }
      +widescreen() {
        width 400px
      }
    }

  }

  .button-box {
    fl-center();
    height 310px;
    transition all .3s;
    width 100%;
    +tablet() {
      height 100%
      width 50%
    }
    &:not(:last-child) {
      +tablet() {
        border-right 1px solid $border
      }
      +desktop() {
        border-bottom 1px solid $border
      }
      +widescreen() {
        border-bottom 1px solid $border
      }
    }
    &--hoverable {
      cursor pointer;
    }
    &--login {
      &:hover .fa-icon {
        color $primary
      }
    }
    &--register {
      &:hover .fa-icon {
        color $danger
      }
    }
    &--accented {
      &:hover .fa-icon {
        color $accented
      }
    }
  }
</style>
