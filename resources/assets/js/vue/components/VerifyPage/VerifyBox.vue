<template>
  <div class="menu-box">
    <div class="menu-box__button-box ">
      <div
        class="button-box"
      >
        <div class="fa-icon__box">
          <font-awesome-icon
            :icon="['fas', 'check']"
            class="fa-icon fa-icon--accented"
          />
        </div>
      </div>
    </div>
    <div class="menu-box__item">
      <div class="register-box">
        <div class="register-box__item">
          <div class="register-box-title">
            <div class="register-box-title__title">
              {{ trans('auth.verify_text') }}
            </div>
          </div>
        </div>
        <div class="register-box__item">
          <form
            class="register-box-form"
            @submit.prevent="verifyUser()"
          >
            <div class="input-box username-box">
              <label
                class="input-box__label"
                for="username-box-form__username"
              >
                {{ trans('users.username') }}
              </label>
              <input
                id="username-box-form__username"
                v-model="username"
                class="input-box__input"
                type="text"
              >
              <div
                v-if="errors.username"
                class="input-box__error"
                @click="skipError"
              >
                <div
                  v-for="(error, key) in errors.username"
                  :key="key"
                  class="help is-danger"
                >
                  {{ error }}
                </div>
                <div class="input-box__diamond-error"/>
              </div>
            </div>
            <div class="input-box display_name-box">
              <label
                class="input-box__label"
                for="display_name-box-form__display_name"
              >
                {{ trans('users.display_name') }}
              </label>
              <input
                id="display_name-box-form__display_name"
                v-model="displayName"
                type="text"
                class="input-box__input"
              >
              <div
                v-if="errors.display_name"
                class="input-box__error"
                @click="skipError"
              >
                <div
                  v-for="(error, key) in errors.display_name"
                  :key="key"
                  class="help is-danger"
                >
                  {{ error }}
                </div>
                <div class="input-box__diamond-error"/>
              </div>
            </div>
            <div class="input-box password-box">
              <label
                class="input-box__label"
                for="password-box-form__password"
              >
                {{ trans('users.password') }}
              </label>
              <input
                v-show="!passwordVisibility"
                id="password-box-form__password"
                v-model="password"
                class="input-box__input"
                type="password"
              >
              <input
                v-show="passwordVisibility"
                v-model="password"
                class="input-box__input"
                type="text"
              >
            </div>
            <div class="input-box password_confirm-box">
              <label
                class="input-box__label"
                for="password_confirm-box-form__password_confirm"
              >
                {{ trans('auth.password_confirm') }}
              </label>
              <input
                v-show="!passwordVisibility"
                id="password_confirm-box-form__password_confirm"
                v-model="passwordConfirm"
                class="input-box__input
                                          password_confirm-box-form__password_confirm"
                type="password"
              >
              <input
                v-show="passwordVisibility"
                v-model="passwordConfirm"
                class="input-box__input"
                type="text"
              >
              <div
                v-if="errors.password"
                class="input-box__error"
                @click="skipError"
              >
                <div
                  v-for="(error, key) in errors.password"
                  :key="key"
                  class="help is-danger"
                >
                  {{ error }}
                </div>
                <div class="input-box__diamond-error"/>
              </div>
            </div>
            <div class="password_visibility-box">
              <div class="password_visibility-box__item is-pulled-left">
                <input
                  id="password_visibility-form__checkbox"
                  v-model="passwordVisibility"
                  type="checkbox"
                  class="password_visibility-box-form__checkbox"
                >
                <label
                  class="remember-box-form__label "
                  for="password_visibility-form__checkbox"
                >
                  {{ trans('auth.show_password') }}
                </label>
              </div>
            </div>
            <div class="register-button-box">
              <button class="register-button-box__button">
                {{ trans('common.submit') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

</template>

<script>
import CREATE_USER from 'Gql/users/mutations/createUser.graphql';

export default {
  data() {
    return {
      username: '',
      displayName: '',
      password: '',
      passwordConfirm: '',
      passwordVisibility: false,
      errors: {},
    };
  },
  methods: {
    async verifyUser() {
      if (this.password === this.passwordConfirm) {
        try {
          const { data: { createUser } } = await this.$apollo.mutate({
            mutation: CREATE_USER,
            variables: {
              email: this.$route.params.email,
              username: this.username,
              displayName: this.displayName,
              password: this.password,
            },
          });
          localStorage.setItem('accessToken', createUser.accessToken);
          localStorage.setItem('refreshToken', createUser.refreshToken);
          localStorage.setItem('expiresIn', createUser.expiresIn);
          this.$router.push({
            name: 'FeedPage',
          });
        } catch (error) {
          if (error.graphQLErrors[0].extensions.errors.length) {
            [this.errors] = error.graphQLErrors[0].extensions.errors;
          } else {
            console.log(error);
          }
        }
      } else {
        this.errors = {
          password: [this.trans('auth.password_mismatch')],
        };
      }
    },
    skipError() {
      this.errors = {};
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';
  @import '../../../../sass/front/components/checkbox';

  .register-box {
    display        : flex;
    flex-direction : column;
    align-items    : center;
    width          : 400px;
    &__item {
      position : relative;
      &--centered {
        display        : flex;
        align-items    : center;
        flex-direction : column;
      }
    }

    &-title {
      height        : 60px;
      width         : 400px;
      border-bottom : 1px solid $border;
      align-items   : center;
      display       : flex;
      padding-left  : 26px;
      &__title {
        fnt($text, 14px, $weight-semibold, left);
      }
    }
    &-check-email {
      align-items  : center;
      color        : $text;
      display      : flex;
      height       : 60px;
      padding-left : 26px;
      width        : 400px;
    }
    &-form {
      padding-top : 36px;
      width       : 344px;
    }
  }

  .input-box {
    align-items      : flex-start;
    background-color : $background-light;
    border           : 1px solid $border;
    border-radius    : 3px;
    display          : flex;
    flex-direction   : column;
    padding          : 0 18px;
    position         : relative;

    &__label {
      fnt($text-light, 10px, $weight-normal, left);
      align-items     : flex-start;
      display         : flex;
      flex-direction  : column;
      height          : 20px;
      justify-content : flex-end;

    }
    &__input {
      fnt($text-light, 12px, $weight-normal, left);
      height           : 32px;
      width            : 100%;
      border           : none;
      background-color : $background-light;
      outline          : none;
    }
    &--is-success {
      border-color : $success;
    }
    &--is-danger {
      border-color : $danger;
    }

    &--gender-box {
      background         : $background-light;
      font-size          : 12px;
      width              : 100%;
      border             : none;
    }

    &__error {
      fnt($light-invert, .75rem, $weight-normal, left)
      background-color $warning
      border-radius $radius
      border 1px solid $warning
      box-shadow 0 2px 2px 0 rgba($black-bis, 0.16), 0 0 0 1px rgba($black-bis, 0.08);
      cursor pointer;
      padding 4px 12px
      position absolute
      top 50px;
      transition box-shadow .3s
      z-index 2
      &:hover {
        box-shadow 0 3px 8px 0 rgba($black-bis, 0.2), 0 0 0 1px rgba($black-bis, 0.08)
      }
      &--accept-box {
        left : 20px;
        top  : 20px;
      }
    }
    &__diamond-error {
      deg45()
      background-color $warning
      height 8px
      position absolute
      top -4px
      width 8px
      z-index 1
    }
  }

  .name-box {
    display         : flex;
    justify-content : space-between;
  }

  .first-name-box, .last-name-box {
    width : 164px;
  }

  .email-box, .password-box, .birthday-box, .gender-box, .register-button-box {
    width      : 100%;
    margin-top : 20px;
  }

  .accept-box {
    width      : 100%;
    margin-top : 32px;
    display    : flex;

    &-form__checkbox {
      padding-left : 8px;
    }
    &-form__label {
      font-weight : 400;
      font-size   : 12px;
      color       : $text-light;
    }
  }

  .register-button-box {
    &__button {
      fl-center();
      fnt($text-invert, 13px, $weight-semibold, left);
      height             : 52px;
      width              : 344px;
      background-color   : $purple;
      border-color       : transparent;
      border-radius      : $radius;
      transition         : all .3s;
      outline            : none;
      cursor             : pointer;

      &:hover {
        color              : $purple !important;
        border             : 1px solid $purple;
        background-color   : $background-light;
        outline            : none;
        box-shadow         : none;
      }
      &:focus, &:active {
        outline            : none;
      }
    }
  }

  .text-is-primary {
    color : $primary;
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
</style>

<style lang="stylus">
  .button-box {
    height : 100%;
  }

  .input-box:not(:first-child) {
    margin-top : 20px;
  }

  .password_visibility-box {
    color : #676b8e;
  }

  .password_visibility-box__item {
    margin : 20px 0;
  }

  .menu-box {
    height : 670px;
  }
</style>
