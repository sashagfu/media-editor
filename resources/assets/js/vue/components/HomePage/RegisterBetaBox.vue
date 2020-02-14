<template>
  <div class="RegisterBetaBox register-box">
    <div class="register-box__headline">
      <div class="register-box__title">
        {{ trans('auth.registration_beta') }}
      </div>
    </div>
    <div
      v-if="registrationPass"
      class="register-box__item"
    >
      <div class="register-box-check-email">
        <h3>
          {{ registrationPass }}
        </h3>
      </div>
    </div>
    <div
      v-if="!registrationPass"
      class="register-box__item"
    >
      <form
        class="register-box__form"
        @submit.prevent="registerMe()"
      >
        <div class="input-box email-box">
          <label
            class="input-box__label"
            for="register-box-form__e-mail"
          >
            {{ trans('auth.email') }}
          </label>
          <input
            id="register-box-form__e-mail"
            v-model="email"
            type="email"
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
        <div class="accept-box">
          <input
            id="accept-box-form__terms"
            v-model="termsConditions"
            class="accept-box-form__checkbox accept-box-form__checkbox--danger"
            type="checkbox"
          >
          <label
            class="accept-box-form__label "
            for="accept-box-form__terms">
            {{ trans('auth.terms_conditions') }}
          </label>
          <template v-if="errors.termsConditions">
            <div
              v-for="(error, key) in errors.termsConditions"
              :key="key"
              class="input-box__error input-box__error--accept-box"
            >
              {{ error }}
              <div class="input-box__diamond-error"/>
            </div>
          </template>
        </div>
        <div class="register-button-box">
          <el-button
            :disabled="!termsConditions"
            type="danger"
            plain
            class="register-button-box__btn"
            @click="registerMe()"
          >
            <font-awesome-icon
              v-if="handleClick"
              :icon="['fas', 'spinner']"
              spin
              class="fa-icon fa-icon--spinner"
            />
            <template v-else>
              {{ trans('auth.register') }}
            </template>
          </el-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import REGISTER_USER from 'Gql/users/mutations/registerUser.graphql';

export default {
  props: {
    registerUrl: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      email: '',
      termsConditions: false,
      registrationPass: '',
      errors: [],
      handleClick: false,
    };
  },
  methods: {
    async registerMe() {
      try {
        this.handleClick = true;
        const { data: { registerUser } } = await this.$apollo.mutate({
          mutation: REGISTER_USER,
          variables: {
            email: this.email,
            termsConditions: this.termsConditions,
          },
        });
        this.registrationPass = registerUser.message;
        this.handleClick = false;
      } catch (error) {
        if (error.graphQLErrors[0].extensions.errors.length) {
          [this.errors] = error.graphQLErrors[0].extensions.errors;
        } else {
          console.log(error);
        }
        this.handleClick = false;
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
    color $white
    font-size 1rem
  }

  .register-box {
    display flex
    flex-direction column
    align-items center
    width  100%
    &__item {
      position relative
      width 100%
    }
    &__headline {
      fl-left();
      border-bottom 1px solid $border
      height 60px
      width 100%
      +tablet() {
        height 52px
      }
    }
    &__title {
      fnt($text, 14px, $weight-semibold, left);
      padding-left 26px
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
      outline  none
      width 100%
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
      width 100%
    }
    &__error {
      fnt($warning-invert, .75rem, $weight-normal, left)
      background-color $warning
      border 1px solid $warning
      border-radius 3px
      box-shadow 0 2px 2px 0 rgba($black-bis, 0.16), 0 0 0 1px rgba($black-bis, 0.08)
      cursor pointer
      padding 4px 12px
      position absolute
      top 50px
      transition box-shadow .3s
      z-index 2
      &:hover {
        box-shadow : 0 3px 8px 0 rgba($black-bis, 0.2), 0 0 0 1px rgba($black-bis, 0.08);
      }
      &--accept-box {
        left 20px
        top 20px
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
    display flex
    justify-content space-between
  }

  .first-name-box, .last-name-box {
    width 164px
  }

  .email-box, .password-box, .birthday-box, .gender-box, .register-button-box {
    margin-top 20px
  }

  .accept-box {
    display flex
    margin-top 32px
    position relative

    &-form__checkbox {
      padding-left 8px
    }
    &-form__label {
      fnt($text-light, 12px, $weight-normal, left)
    }
  }

  .register-button-box {
    &__btn {
      font-size 13px
      height 52px
      transition all .3s
      width 100%
    }
  }

  .text-is-primary {
    color $primary
  }
  .accept-box-form {
    &__checkbox {
      height 18px
      margin 0 3px 0 0
      vertical-align top
      width 18px
    }
    &__checkbox+ label {
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
      background rgba($lime, .6)
    }
    &__checkbox--danger:checked + label:before {
      background rgba($danger, .8)
    }
    &__checkbox:checked + label:after {
      left 10px
    }
  }
</style>
