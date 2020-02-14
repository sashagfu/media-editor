<template>
  <div class="UserDropdownMenu user-menu-box user-dm">
    <div class="user-dm__group-title">
      {{ trans('users.your_account') }}
    </div>
    <div
      class="user-dm__item user-dm__item--first"
      @click="openProfileSettings"
    >
      <span class="fa-icon__box">
        <font-awesome-icon
          :icon="['fas', 'cogs']"
          class="fa-icon"
        />
      </span>
      <span class="user-dm__item-text">
        {{ trans('users.profile_set') }}
      </span>
    </div>
    <div
      class="user-dm__item"
      @click="openTopUp"
    >
      <span class="fa-icon__box fa-icon__box--d-sign">
        <font-awesome-icon
          :icon="['fas', 'dollar-sign']"
          class="fa-icon fa-icon--d-sign"
        />
      </span>
      <span class="user-dm__item-text">
        {{ trans('users.top_up_balance') }}
      </span>
    </div>
    <div
      class="user-dm__item"
      @click="withdrawFunds"
    >
      <span class="fa-icon__box">
        <!--:icon="['fas', 'dollar-sign']"-->
        <font-awesome-icon
          :icon="['fas', 'hand-holding-usd']"
          class="fa-icon"
        />
      </span>
      <span class="user-dm__item-text">
        {{ trans('users.withdraw_funds') }}
      </span>
    </div>
    <div
      class="user-dm__item"
      @click="changePassword"
    >
      <span class="fa-icon__box">
        <font-awesome-icon
          :icon="['fas', 'key']"
          class="fa-icon"
        />
      </span>
      <span class="user-dm__item-text">
        {{ trans('users.change_pass') }}
      </span>
    </div>
    <div
      class="user-dm__item user-dm__item--last"
      @click="logOut"
    >
      <span class="fa-icon__box">
        <font-awesome-icon
          :icon="['fas', 'sign-out-alt']"
          class="fa-icon"
        />
      </span>
      <span class="user-dm__item-text">
        {{ trans('users.log_out') }}
      </span>
    </div>
    <div class="user-dm__group-title">
      {{ trans('users.about') }}
    </div>
    <div class="user-dm__item user-dm__item-ab user-dm__item--first-ab">
      <span class="user-dm__item-text">
        {{ trans('users.terms') }}
      </span>
    </div>
    <div class="user-dm__item user-dm__item-ab">
      <span class="user-dm__item-text">
        {{ trans('users.faqs') }}
      </span>
    </div>
    <div class="user-dm__item user-dm__item-ab">
      <span class="user-dm__item-text">
        {{ trans('users.careers') }}
      </span>
    </div>
    <div class="user-dm__item user-dm__item-ab user-dm__item--last-ab">
      <span class="user-dm__item-text">
        {{ trans('users.contact') }}
      </span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'UserDropdownMenu',
  methods: {
    openTopUp() {
      this.$emit('open-top-up');
    },
    openProfileSettings() {
      this.$emit('open-settings');
    },
    withdrawFunds() {
      this.$emit('withdraw-funds');
    },
    changePassword() {
      this.$emit('change-password');
    },
    logOut() {
      this.$emit('log-out');
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .fa-icon {
    fnt($text-light, 16px, $weight-light, left);
    transition: all .3s;
    &:hover {
      opacity: 0.8;
    }
    &__box {
      fl-left();
      transition: all .3s;
      width: 24px;
      &--d-sign {
        fl-center();
        background-color: $text-light;
        border-radius: 50%;
        height: 18px;
        margin: 0 2px;
        width: 18px;
      }
    }
    &--d-sign {
      fnt($white, 14px, $weight-light, center);
    }
  }

  .user-dm {
    background-color: $white;
    border: 1px solid $border;
    border-radius: $radius;
    position: relative;
    width: 232px;

    &__group-title {
      fnt($text-lighter, 9px, $weight-normal, left);
      fl-left();
      border-bottom: 1px solid $border;
      height: 36px;
      text-transform: uppercase;
      padding: 0 24px;
    }
    &__item {
      fl-left();
      cursor: pointer;
      padding: 12px 24px;
      transition: all .3s;
      &--ab {
        padding: 8px 24px;
      }
      &--first {
        padding-top: 20px;
      }
      &--first-ab {
        padding-top: 16px;
      }
      &--last {
        padding-bottom: 20px;
        border-bottom: 1px solid $border;
      }
      &--last-ab {
        padding-bottom: 16px;
      }
        &:hover {
        background-color: rgba($primary, 0.1);
      }
    }
    &__item-text {
      fnt($text-light, 12px, $weight-normal, left);
      transition: all .3s;
      &:not(:first-child) {
        padding-left: 8px;
      }
    }
    &__item:hover &__item-text,
    &__item:hover .fa-icon {
      color: $primary;
    }
    &__item:hover .fa-icon--d-sign {
      color: $white;
    }
    &__item:hover .fa-icon__box--d-sign {
      background-color: $primary;
    }
    &__about-item {
      fl-left();
      cursor: pointer;
      padding: 8px 24px;
      transition: all .3s;
      &--first {
        padding-top: 14px;
      }
      &--last {
        padding-bottom: 14px;
      }
      &:hover {
        background-color: rgba($primary, 0.05);
      }
    }
  }
</style>
