<template>
  <div class="menu-box">
    <div class="menu-box__button-box">
      <div class="button-box">
        <div class="fa-icon__box">
          <font-awesome-icon
            icon="key"
            class="fa-icon fa-icon--accented"
          />
        </div>
      </div>
      <div
        class="button-box button-box--login"
        @click="goToLogin"
      >
        <div class="fa-icon__box">
          <font-awesome-icon
            icon="sign-in-alt"
            class="fa-icon fa-icon--hoverable"
          />
        </div>
      </div>
    </div>
    <div class="menu-box__form-box">
      <div class="register-box">
        <div class="register-box__item">

          <div class="register-box__title">
            {{ trans('auth.verify_text') }}
          </div>
        </div>
        <div class="register-box__item">
          <form
            class="register-box__form"
            @submit.prevent="resetPassword()"
          >
            <div class="input-box email-box">
              <label
                class="input-box__label"
                for="email-box-form__email"
              >
                {{ trans('users.email') }}
              </label>
              <input
                id="email-box-form__email"
                v-model="email"
                :disabled="disabledInput"
                class="input-box__input"
                type="email"
              >
              <template v-if="errors.email">
                <div
                  v-for="(error, key) in errors.email"
                  :key="key"
                  class="input-box__error"
                >
                  {{ error }}
                  <div class="input-box__diamond-error"/>
                </div>
              </template>
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
                :disabled="disabledInput"
                class="input-box__input"
                type="password"
              >
              <input
                v-show="passwordVisibility"
                v-model="password"
                :disabled="disabledInput"
                type="text"
                class="input-box__input"
              >
            </div>
            <div class="input-box password-confirm-box">
              <label
                class="input-box__label"
                for="password-box-form__password-confirm"
              >
                {{ trans('auth.password_confirm') }}
              </label>
              <input
                v-show="!passwordVisibility"
                id="password-box-form__password-confirm"
                :disabled="disabledInput"
                v-model="passwordConfirm"
                class="input-box__input
                                          password-box-form__password-confirm"
                type="password"
              >
              <input
                v-show="passwordVisibility"
                :disabled="disabledInput"
                v-model="passwordConfirm"
                type="text"
                class="input-box__input"
              >
              <template v-if="errors.password">
                <div
                  v-for="(error, key) in errors.password"
                  :key="key"
                  class="input-box__error"
                >
                  {{ error }}
                  <div class="input-box__diamond-error"/>
                </div>
              </template>
            </div>
            <div class="password-visibility-box">
              <div class="password-visibility-box__item is-pulled-left">
                <input
                  id="password-form__checkbox"
                  :disabled="disabledInput"
                  v-model="passwordVisibility"
                  type="checkbox"
                  class="password-visibility-box-form__checkbox"
                >
                <label
                  class="password-visibility-box__label"
                  for="password-form__checkbox"
                >
                  {{ trans('auth.show_password') }}
                </label>
              </div>
            </div>
            <div class="register-button-box">
              <button
                :class="{disabled: disabledInput}"
                class="register-button-box__button"
              >
                <font-awesome-icon
                  v-if="resetLoading"
                  icon="spinner"
                  spin
                  class="fa-box__icon fa-box__icon--invert"
                />
                <template v-else>
                  {{ trans('common.submit') }}
                </template>
              </button>
              <template v-if="errors.error">
                <div
                  v-for="(error, key) in errors.error"
                  :key="key"
                  class="input-box__error input-box__error--error"
                >
                  {{ error }}
                  <div class="input-box__diamond-error"/>
                </div>
              </template>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import RESET_PASSWORD from 'Gql/users/mutations/resetPassword.graphql';

export default {
  data() {
    return {
      email: '',
      password: '',
      passwordConfirm: '',
      passwordVisibility: false,
      successMessage: '',
      disabledInput: false,
      resetLoading: false,
      errors: {},
    };
  },
  methods: {
    goToLogin() {
      window.location.href = '/';
    },
    async resetPassword() {
      this.errors = {};
      if (!this.email) {
        this.errors.email = [this.trans('auth.email_error')];
        return;
      }
      if (this.password === this.passwordConfirm) {
        try {
          this.resetLoading = true;
          const { data: { resetPassword } } = await this.$apollo.mutate({
            mutation: RESET_PASSWORD,
            variables: {
              token: this.$route.params.token,
              email: this.email,
              password: this.password,
              passwordConfirmation: this.passwordConfirm,
            },
          });
          this.resetLoading = false;
          this.disabledInput = true;
          this.successMessage = resetPassword.message;
          setTimeout(() => {
            this.$router.push({
              name: 'LoginPage',
            });
          }, 5000);
        } catch (error) {
          this.resetLoading = false;
          if (error.graphQLErrors[0].extensions.errors.length) {
            [this.errors] = error.graphQLErrors[0].extensions.errors;
          } else {
            this.errors = error.response.data;
          }
        }
      } else {
        this.errors.password = [this.trans('auth.password_mismatch')];
      }
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

  .fa-icon {
    color     : $text-light;
    font-size : 1rem;
    &--hoverable {
      &:hover {
        opacity : .75;
      }
    }
    &--accented {
      color : $accented;
    }
    &__box {
      fl-center()
      flex            : 0 0 auto;
      height          : 24px;
      transition      : all .3s;
      width           : 24px;
    }
  }

  .menu-box {
    background-color $white;
    border 1px solid $border;
    border-radius $radius;
    box-shadow 0px 0px 20px 0px rgba($grey-darker, .1);
    display flex;
    height 620px;
    width 468px;
    +tablet() {
      flex-direction : column;
      width          : 100%;
    }
    &__button-box {
      border-right : 1px solid $border;
      display         : flex;
      flex-direction  : column;
      height          : 100%;
      justify-content : center;
      width           : 68px;
      +tablet() {
        border-bottom: 1px solid $border;
        border-right: none;
        flex-direction: row;
        height: 52px;
        width: 100%;
      }
    }
    &__form-box {
      width : 400px;
      +tablet() {
        width : 100%;
      }
    }
  }

  .button-box {
    fl-center();
    height          : 310px;
    transition      : all .3s;
    width           : 100%;
    +tablet() {
      height : 100%;
      width  : 50%;
    }
    &:not(:last-child) {
      border-bottom: 1px solid $border;
      +tablet() {
        border-right : 1px solid $border;
        border-bottom: none;
      }
    }
    &--login {
      cursor : pointer;
      &:hover .fa-icon {
        color : $primary;
      }
    }
  }

  .register-box {
    display        : flex;
    flex-direction : column;
    align-items    : center;
    width          : 100%;

    &__item {
      position : relative;
      width    : 100%;
    }
    &__title {
      fl-left();
      fnt($text, 14px, $weight-semibold, left);
      border-bottom : 1px solid $border;
      height        : 60px;
      padding       : 0 26px;
      width         : 100%;
      +tablet() {
        height : 52px;
      }
    }

    &-check-email {
      fl-left();
      color        : $text;
      height       : 60px;
      padding-left : 26px;
      width        : 400px;
    }
    &__form {
      padding : 26px;
      padding-top : 36px;
      width       : 100%;
      +tablet() {
        padding : 16px;
        width   : 100%;
      }
    }
  }

  .input-box {
    background-color : $background-light;
    border-radius    : 3px;
    border           : 1px solid $border;
    display          : flex;
    flex-direction   : column;
    padding          : 0 18px;
    position         : relative;
    &__label {
      fnt($text-light, 10px, $weight-normal, left);
      display         : flex;
      flex-direction  : column;
      height          : 20px;
      justify-content : flex-end;
    }

    &__input {
      fnt($text-light, 12px, $weight-normal, left);
      background-color : $background-light;
      border           : none;
      height           : 32px;
      outline          : none;
      width            : 100%;
    }
    &--is-success {
      border-color : $success;
    }
    &--is-warning {
      border-color : $warning;
    }

    &--gender-box {
      background : $background-light;
      font-size  : 12px;
      width      : 100%;
      border     : none;
    }
    &__error {
      fnt($warning-invert, .75rem, $weight-normal, left);
      background-color : $warning; //$background;
      border           : 1px solid $warning;
      border-radius    : 3px;
      box-shadow       : 0 2px 2px 0 rgba($black-bis, 0.16), 0 0 0 1px rgba($black-bis, 0.08);
      cursor           : pointer;
      padding          : 4px 12px;
      position         : absolute;
      top              : 50px;
      transition       : box-shadow .3s;
      z-index          : 2;
      &:hover {
        box-shadow : 0 3px 8px 0 rgba($black-bis, 0.2), 0 0 0 1px rgba($black-bis, 0.08);
      }
      &--accept-box {
        left : 20px;
        top  : 20px;
      }
      &--error {
        top : 54px;
      }
    }
    &__diamond-error {
      deg45();
      background-color : $warning;
      height           : 8px;
      position         : absolute;
      top              : -4px;
      width            : 8px;
      z-index          : 1;
    }
  }

  .register-button-box {
    position : relative;

    &__button {
      fnt($text-invert, 13px, $weight-semibold, center);
      fl-center()
      cursor           : pointer;
      height           : 52px;
      background-color : $purple;
      border           : 1px solid $purple;
      border-radius    : 3px;
      outline          : none;
      transition       : all .3s;
      width            : 100%; //344px;
      +tablet() {
        height : 34px;
      }
      &:hover {
        color            : $purple !important;
        background-color : transparent;
      }
      &:focus, &:active {
        outline : none;
      }
    }
    &__fa {
      font-size   : 20px;
      margin-left : 8px;
    }
  }

  .password-box,
  .password-confirm-box,
  .register-button-box {
    margin-top : 20px;
    width      : 100%;
  }

  .password-visibility-box {
    margin-top : 5px;
    display    : flex;
    &__label {
      fnt($text-light, 13px, $weight-normal, left);
    }
  }
</style>
