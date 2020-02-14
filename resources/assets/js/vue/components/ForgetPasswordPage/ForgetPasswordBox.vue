<template>
  <div class="menu-box">
    <div class="menu-box__item">
      <div class="button-box button-box--register"/>
    </div>
    <div class="menu-box__item">
      <div class="register-box">
        <div class="register-box__item">
          <div class="register-box-title">
            <div class="register-box-title__title">
              {{ trans('auth.reset_password') }}
            </div>
          </div>
        </div>
        <div class="register-box__item">
          <form
            class="register-box-form"
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
                type="email"
                required
                class="input-box__input"
              >
              <template v-if="errors.email">
                <p
                  v-for="(error, key) in errors.email"
                  :key="key"
                  class="help is-danger"
                >
                  {{ error }}
                </p>
              </template>
            </div>
            <p
              v-if="successMessage"
              class="register-box__message"
            >
              {{ successMessage }}
            </p>
            <div class="register-button-box">
              <button
                :class="{disabled: disabledInput}"
                class="register-button-box__button"
              >
                {{ trans('auth.reset_password') }}
                <i
                  v-if="resetLoading"
                  class="fa fa-spinner fa-spin fa-3x fa-fw has-text-centered"
                />
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  components: {},
  data() {
    return {
      email: '',
      disabledInput: false,
      successMessage: '',
      resetLoading: false,
      errors: {},
    };
  },
  methods: {
    resetPassword() {
      this.errors = [];
      this.resetLoading = !this.resetLoading;
      if (this.email) {
        this.$http.post('/password/forget', {
          email: this.email,
        }).then(({ data }) => {
          this.resetLoading = !this.resetLoading;
          this.disabledInput = true;
          this.successMessage = data;
        }).catch((error) => {
          this.errors = error.response.data;
        });
      } else {
        this.resetLoading = !this.resetLoading;
        this.errors.email = ['Please enter your email'];
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

  .menu-box {
    background-color : $background;
    border-radius    : 0px 3px 3px 0px;
    border           : 1px solid $border;
    box-shadow       : -2px 3px 97px 21px rgba(136, 141, 168, 0.1);
    display          : flex;
    height           : 620px;
    width            : 468px;

    &__item {
      &:first-child {
        border-right    : 1px solid $border;
        display         : flex;
        flex-direction  : column;
        height          : 100%;
        justify-content : center;
        width           : 68px;
      }

      height : 100%;
      width  : 400px;
    }
  }

  .button-box {
    background : center center no-repeat;
    cursor     : pointer;
    height     : 310px;
    transition : all .3s;
    width      : 100%;
    &--register {
      background-image : url('../../../../img/register-icon.png');
    }
  }

  .register-box {
    align-items    : center;
    display        : flex;
    flex-direction : column;
    width          : 400px;

    &__item {
      position : relative;

      &--centered {
        align-items    : center;
        display        : flex;
        flex-direction : column;
      }
    }

    &-title {
      align-items   : center;
      border-bottom : 1px solid $border;
      display       : flex;
      height        : 60px;
      padding-left  : 26px;
      width         : 400px;

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
    &__message {
      align-items : flex-start;
      color       : $text-light;
      display     : flex;
      margin-top  : 10px;
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
      border     : none;
      font-size  : 12px;
      width      : 100%;
    }
    &__error {
      fnt($warning-invert, .75rem, $weight-normal, left);
      background-color : $warning;
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
    &__button {
      fnt($text-invert, 13px, $weight-semibold, center);
      align-items      : center;
      background-color : $purple;
      border           : 1px solid $purple;
      border-radius    : 3px;
      cursor           : pointer;
      display          : flex;
      height           : 52px;
      justify-content  : center;
      margin-top       : 20px;
      outline          : none;
      transition       : all .3s;
      width            : 100%;

      &:hover {
        color            : $purple !important;
        background-color : transparent;
      }
      &:focus, &:active {
        outline : none;
      }
    }
  }
</style>
