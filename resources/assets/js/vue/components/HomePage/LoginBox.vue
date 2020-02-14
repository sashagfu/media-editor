<template>
  <div class="LoginBox login-box">
    <div class="login-box__headline">
      <div class="login-box__title">
        {{ trans('auth.login_form_text') }}
      </div>
    </div>
    <div class="login-box__item">
      <form
        class="login-box__form"
        @submit.prevent="loginMe"
      >
        <div class="input-box email-box">
          <label
            class="input-box__label"
            for="login-box-form__e-mail"
          >
            {{ trans('auth.email') }}
          </label>
          <input
            id="login-box-form__e-mail"
            v-model="email"
            type="email"
            name="email"
            class="input-box__input"
          >
          <div
            v-if="error.email"
            class="input-box__error"
            @click="skipError"
          >
            {{ error.email[0] ? error.email[0] : error.email }}
            <div class="input-box__diamond-error"/>
          </div>
        </div>
        <div class="input-box password-box">
          <label
            class="input-box__label"
            for="login-box-form__password">
            {{ trans('auth.pass') }}
          </label>
          <input
            id="login-box-form__password"
            v-model="password"
            name="password"
            type="password"
            class="input-box__input"
          >
          <div
            v-if="error.password"
            class="input-box__error"
            @click="skipError"
          >
            {{ (error.password[0]) ? error.password[0] : error.password }}
            <div class="input-box__diamond-error"/>
          </div>
        </div>
        <div class="remember-box">
          <div class="remember-box__item">
            <input
              id="remember-box-form__remember"
              v-model="rememberMe"
              class="remember-box-form__checkbox"
              type="checkbox"
            >
            <label
              for="remember-box-form__remember"
              class="remember-box-form__label "
            >
              {{ trans('auth.remember_me') }}
            </label>
          </div>
          <div class="remember-box__item">
            <div
              class="forgot-pass"
              @click="forgetPassword"
            >
              {{ trans('auth.forget_pass') }}
            </div>
          </div>
        </div>
        <div class="login-button-box">
          <button class="login-button-box__button">
            <font-awesome-icon
              v-if="handleClick"
              :icon="['fas', 'spinner']"
              spin
              class="fa-icon"
            />
            <template v-else>
              {{ trans('auth.login') }}
            </template>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
<script>
/* eslint-disable camelcase */

import { mapActions } from 'vuex';
import LOGIN_USER_MUTATION from 'Gql/users/mutations/loginUser.graphql';
import FETCH_ME from 'Gql/users/queries/fetchMe.graphql';

export default {
  props: {
    loginUrl: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      email: '',
      password: '',
      rememberMe: false,
      error: {},
      handleClick: false,
    };
  },
  methods: {
    ...mapActions('general', [
      'setHomePageActiveBox',
      'setActiveUser',
    ]),
    skipError() {
      this.error = '';
    },
    forgetPassword() {
      this.setHomePageActiveBox('ForgetPasswordBox');
    },
    async loginMe() {
      try {
        this.handleClick = true;
        const { data: { loginUser } } = await this.$apollo.mutate({
          mutation: LOGIN_USER_MUTATION,
          variables: {
            email: this.email,
            password: this.password,
            rememberMe: this.rememberMe,
          },
        });
        localStorage.setItem('accessToken', loginUser.accessToken);
        localStorage.setItem('refreshToken', loginUser.refreshToken);
        localStorage.setItem('expiresIn', loginUser.expiresIn);
        // Refetch user
        await this.refetchUser();
        this.$router.push({
          name: 'FeedPage',
        });
      } catch (error) {
        if (error.graphQLErrors[0].extensions.errors.length) {
          [this.error] = error.graphQLErrors[0].extensions.errors;
        } else {
          console.log(error);
        }
        this.handleClick = false;
      }
    },
    async refetchUser() {
      await this.$apollo.query({
        query: FETCH_ME,
        update: (store, { data: fetchMe }) => {
          const data = store.readQuery(FETCH_ME);
          data.fetchMe = fetchMe;
          store.writeQuery({ query: FETCH_ME, data });
        },
      });
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
        color $white
        font-size 1rem
    }

    .login-box {
        align-items center
        display flex
        flex-direction column
        width 100%

        &__item {
            position relative
            width 100%
        }

        &__headline {
            fl-left()
            border-bottom 1px solid $border
            height 60px
            width 100%
            +tablet() {
                height 52px
            }
        }

        &__title {
            fnt($text, 14px, $weight-semibold, left)
            padding-left 26px
        }

        &__form {
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

    .email-box {
        margin-top 20px
        +tablet() {
            margin-top 8px
        }
    }

    .password-box {
        margin-top 20px
    }

    .input-box {
        align-items flex-start
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
            width 100%
        }

        &--is-success {
            border-color $success
        }

        &--is-warning {
            border-color $warning
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
                left: 20px;
                top: 20px;
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

    .remember-box {
        display flex
        height 42px
        justify-content space-between
        margin 20px 0 12px 0
        width 100%
        +tablet() {
            height 24px
        }

        &__item {
            height 100%
            width 50%

            &:first-child {
                display flex
            }

            &:last-child {
                display flex
                justify-content flex-end
            }
        }

        &-form__label {
            fnt($text-light, 13px, $weight-semibold, left)
        }
    }

    .forgot-pass {
        color $text-light !important
        cursor pointer;
        font-size 13px !important
        font-weight 500 !important

        :hover {
            color $primary !important
        }
    }

    .login-button-box {
        &__button {
            fnt($text-invert, 13px, $weight-semibold, center)
            fl-center()
            background-color $primary
            border-radius $radius
            border 1px solid $primary
            cursor pointer
            height 52px
            margin-bottom 8px
            outline none
            transition all .3s
            width 100%
            +tablet() {
                height 34px
            }

            &:hover {
                background-color rgba($primary, 0.7)
            }

            &--facebook {
                background-color $facebook
                border 1px solid $facebook
                color $facebook !important
                visibility hidden

                &:hover {
                    background-color rgba($facebook, 0.7)
                }
            }

            &--twitter {
                border 1px solid $twitter
                background-color $twitter
                color $twitter !important
                visibility hidden

                &:hover {
                    background-color rgba($twitter, 0.7)
                }
            }
        }

        &--facebook {
            padding-top 26px
        }

        &--twitter {
            padding-top 12px
        }
    }

    .label-or {
        fnt($border, 10px, $weight-normal, left)
        fl-center()
        background-color $background
        position absolute
        text-transform uppercase
        top -8px
        width 70px
    }

    .p-box {
        padding-top 32px
        width 344px

        &__item {
            fnt($text, 13px, $weight-normal, left)
        }
    }

    .text-is-primary {
        color $primary !important
    }

    .remember-box-form {
        &__checkbox {
            height 18px
            margin 0 3px 0 0
            vertical-align top
            width 18px
        }

        &__checkbox + label {
            cursor pointer
        }

        &__checkbox:not(checked) {
            opacity 0
            position absolute
        }

        &__checkbox:not(chekced) + label {
            padding 0 0 0 32px
            position relative
        }

        &__checkbox:not(chekced) + label:before {
            background $white-shadow
            border-radius 8px
            box-shadow none
            content ''
            height 16px
            left 0
            position absolute
            top 2px
            transition all .3s
            width 24px
        }

        &__checkbox:not(chekced) + label:after {
            background $white
            border-radius 6px
            box-shadow none
            content ''
            height 12px
            left 2px
            position absolute
            top 4px
            transition all .3s
            width 12px
        }

        &__checkbox:checked + label:before {
            background rgba($lime, .8)
        }

        &__checkbox:checked + label:after {
            left 10px
        }
    }
</style>
