<template>
  <div class="ForgetPasswordBox menu-box">
    <div class="menu-box__button-box">
      <div class="button-box button-box--forget-pass">
        <div class="fa-icon__box">
          <font-awesome-icon
            icon="key"
            class="fa-icon fa-icon--accented"
          />
        </div>
      </div>
      <div
        class="button-box button-box--hoverable"
        @click="changeActiveBox"
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
            {{ trans('auth.reset_password') }}
          </div>
        </div>
        <div class="register-box__item">
          <div class="register-box__form">
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
            <div class="register-button-box">
              <button
                :class="{disabled: disabledInput}"
                class="register-button-box__button"
                @click="resetPassword()"
              >
                <font-awesome-icon
                  v-if="resetLoading"
                  icon="spinner"
                  spin
                  class="fa-icon fa-icon--invert"
                />
                <template v-else>
                  {{ trans('auth.reset_password') }}
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
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex';

import SEND_RESET_PASSWORD from 'Gql/users/mutations/sendResetPassword.graphql';

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
    ...mapActions('general', [
      'setHomePageActiveBox',
    ]),
    notify() {
      this.$notify({
        type: 'success',
        title: 'Password reset',
        iconClass: 'el-icon-circle-check',
        dangerouslyUseHTMLString: true,
        message: '<span>Please check e-mail</span>',
      });
    },
    changeActiveBox() {
      this.setHomePageActiveBox('LoginBox');
    },
    async resetPassword() {
      this.errors = [];
      this.resetLoading = true;
      if (this.email) {
        try {
          const { data: { sendResetPassword } } = await this.$apollo.mutate({
            mutation: SEND_RESET_PASSWORD,
            variables: {
              email: this.email,
            },
          });
          this.resetLoading = false;
          this.disabledInput = true;
          this.successMessage = sendResetPassword.message;
          this.notify();
          this.changeActiveBox();
        } catch (error) {
          this.resetLoading = false;
          if (error.graphQLErrors[0].extensions.errors.length) {
            [this.errors] = error.graphQLErrors[0].extensions.errors;
          } else {
            this.errors = error.response.data;
          }
        }
      } else {
        this.resetLoading = false;
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

  .fa-icon {
    color $text-light
    font-size 1rem
    &--hoverable {
      &:hover {
        opacity .75
      }
    }
    &--invert {
      color $text-invert
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
    border-radius $radius
    border  1px solid $border
    box-shadow 0px 0px 20px 0px rgba($grey-darker, .1)
    display flex
    width 468px
    +tablet() {
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
      }
      +desktop() {
        border-right 1px solid $border
      }
      +widescreen() {
        border-right 1px solid $border
      }
    }
    &__form-box {
      +tablet() {
        height 325px
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
    fl-center()
    height 310px
    transition all .3s
    &--hoverable {
      cursor pointer
      &:hover .fa-icon {
        color $lime
      }
    }
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
    &--forget-pass {
      cursor auto
    }
  }

  .register-box {
    display flex
    flex-direction  column
    align-items center
    &__item {
      position relative
      width 100%
    }
    &__title {
      fnt($text, 14px, $weight-semibold, left)
      fl-left()
      border-bottom 1px solid $border
      height 60px
      padding 0 26px
      +tablet() {
        height 52px
      }
    }
    &-check-email {
      fl-left()
      color $text
      height 60px
      padding-left 26px
      width 400px
    }
    &__form {
      padding-top 36px
      +tablet() {
        padding 16px
      }
      +desktop() {
        padding 26px
      }
      +widescreen() {
        padding 26px
      }
    }
  }

  .input-box {
    background-color $background-light
    border-radius $radius
    border 1px solid $border
    display flex
    flex-direction column
    padding 0 18px
    position relative
    &__label {
      fnt($text-light, 10px, $weight-normal, left)
      display flex
      flex-direction column
      height 20px
      justify-content flex-end
    }
    &__input {
      fnt($text-light, 12px, $weight-normal, left)
      background-color $background-light
      border none
      height 32px
      outline none
    }
    &--is-success {
      border-color $success
    }
    &--is-warning {
      border-color $warning
    }
    &--gender-box {
      background $background-light
      border none
      font-size 12px
    }
    &__error {
      fnt($warning-invert, .75rem, $weight-normal, left)
      background-color $warning
      border 1px solid $warning
      border-radius $radius
      box-shadow 0 2px 2px 0 rgba($black-bis, 0.16), 0 0 0 1px rgba($black-bis, 0.08)
      cursor pointer
      padding 4px 12px
      position absolute
      top 50px;
      transition box-shadow .3s
      z-index 2
      &:hover {
        box-shadow 0 3px 8px 0 rgba($black-bis, 0.2), 0 0 0 1px rgba($black-bis, 0.08)
      }
      &--accept-box {
        left 20px
        &--error {
          top 54px
        }
        top 20px
      }
      &--error {
        top 54px
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

  .register-button-box {
    position relative
    &__button {
      fnt($text-invert, 13px, $weight-semibold, center);
      fl-center();
      background-color $purple
      border-radius $radius
      border 1px solid $purple
      cursor pointer
      height 52px
      outline none
      transition all .3s
      width 100%
      +tablet() {
        height 34px
      }
      &:hover {
        background-color rgba($purple, 0.7)
      }
    }
  }

  .email-box, .register-button-box {
    margin-top 20px
    +tablet() {
      margin-top 8px
    }
  }
</style>
