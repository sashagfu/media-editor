<template>
  <div class="DonationsHistoryUser dh-user">
    <div class="dh-user__left">
      <Avatar
        v-if="donationsUser.avatar.includes('/profile/default/avatar')"
        :username="donationsUser.displayName"
        :size="36"
        custom-class="dh-user__avatar"
      />
      <div
        v-else
        :style="{'background-image': `url(${donationsUser.avatar})`}"
        class="dh-user__avatar"
      />
      <div class="dh-user__title-box">
        <div class="dh-user__title">
          {{ donationsUser.displayName }}
        </div>
        <div class="dh-user__sub-title">
          {{ trans('donate.on_al_from') }}: {{ formatDate(donationsUser.createdAt) }}
        </div>
      </div>
      <div class="dh-user__btn-box">
        <FollowButton
          :following="donationsUser"
          :active-user="activeUser"
        />

        <div class="dh-user__btn dh-user__btn--message">
          <font-awesome-icon
            :icon="['fas', 'envelope']"
            class="fa-icon fa-icon--envelope"
          />
          <div class="dh-user__btn-text dh-user__btn-text--message">
            {{ trans('profile_page.send_message') }}
          </div>
        </div>
      </div>
    </div>
    <div class="dh-user__center">
      <div class="dh-user__donate-box">
        <template v-if="donated">
          <div class="dh-user__donate-title">
            {{ trans('donate.donated') }}:
          </div>
          <div class="dh-user__donate-amount">
            <!--$ {{ Number.parseInt(newDonation.amount, 10) }}-->
            $ {{ donated }}
          </div>
        </template>
      </div>
    </div>
    <div class="dh-user__right">
      <div
        :class="[
          'dh-user__chevron',
          {'dh-user__chevron--is-open': isOpen}
        ]"
        @click="toggleOpen"
      >
        <font-awesome-icon
          :icon="['fas', 'angle-down']"
          class="fa-icon fa-icon--chevron"
        />
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment';
import * as constants from 'Helpers/constants';

import Avatar from 'Pages/ProfilePage/Avatar';
import FollowButton from 'Pages/Common/FollowButton';

export default {
  name: 'DonationsHistoryUser',
  components: {
    Avatar,
    FollowButton,
  },
  props: {
    donationsUser: {
      type: Object,
      default: () => ({}),
    },
    donated: {
      type: Number,
      default: 0,
    },
    historyUserIsOpen: {
      type: [String, Number],
      default: '',
    },
    activeUser: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      constants,
    };
  },
  computed: {
    isOpen() {
      return this.donationsUser.id === this.historyUserIsOpen;
    },
  },
  methods: {
    formatDate(date) {
      return moment(date).format('MM/DD/YYYY');
    },
    toggleFollow() {},
    toggleOpen() {
      if (this.isOpen) {
        this.$emit('toggle-history-user', '');
      } else {
        this.$emit('toggle-history-user', this.donationsUser.id);
      }
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
    fnt($text-light, 14px, $weight-light, left);
    transition : all .3s;
    &--user {
      color: $grey;
    }
    &--follow {
      color: $grey;
      font-size: 11px;
    }
    &--envelope {
      color: $blue;
      font-size: 11px;
    }
    &--chevron {
      color: $grey-light;
      font-size: 28px;
    }
  }

  .dh-user {
    fl-between();
    background: $white;
    height: 56px;
    padding: 0 26px;
    width: 100%;
    &--accepted {
      background: rgba($primary, 0.05);
    }
    &--declined {
      background: rgba($danger, 0.05);
    }
    &--expired {
      background: rgba($grey, 0.05);
    }
    &:not(:last-child) {
      border-bottom: 1px solid $border;
    }
    &__left {
      fl-left();
      width: 40%;
    }
    &__center {
      fl-center();
      width: 20%;
    }
    &__right {
      fl-right();
      width: 30%;
    }
    &__avatar {
      fl-center();
      background: center center/cover no-repeat $grey-light;
      border-radius: 50%;
      cursor: pointer;
      flex-shrink: 0;
      flex-grow: 0;
      height: 36px;
      width: 36px;
    }
    &__title-box {
      cursor: pointer;
      display: flex;
      flex-direction: column;
      padding-left: 16px;
      width: 240px;
    }
    &__title {
      fnt($text, 14px, $weight-normal, left);
      line-height: 1;
    }
    &__sub-title {
      fnt($primary, 12px, $weight-light, left);
      line-height: 1;
      &--declined {
        color: $text-light;
      }
    }

    &__btn-box {
      fl-left();
      padding: 0 16px;
    }

    &__donate-box {
      fl-left();
    }
    &__donate-title {
      fnt($text-lighter, 14px, $weight-normal, left);
      line-height: 1;
    }
    &__donate-amount {
      fnt($primary, 18px, $weight-normal, left);
      padding-left: 8px;
      line-height: 1;
      width: 56px;
      &--declined {
        color: $text-light;
      }
    }
    &__btn {
      fl-center();
      border-radius: 9px;
      border: 1px solid $grey-light;
      cursor: pointer;
      height: 18px;
      transition: all .3s;
      width: 86px;
      margin-left: 8px;
      &:hover {
        opacity: 0.5;
      }
      &--followed {
        width: 108px;
      }
      &--message {
        border: 1px solid $info;
        margin-left: 8px;
        width: 120px;
      }
      &--donate {
        border: 1px solid $paypal;
        margin-left: 8px;
        width: 92px;
      }
    }
    &__btn-text {
      fnt($grey-light, 12px, $weight-normal, center);
      padding-left: 8px;
      transition: all .3s;
      &--message {
        color: $info;
      }
    }
    &__chevron {
      fl-center();
      cursor: pointer;
      transition: all .3s;
      &--is-open {
        transform: rotate(180deg);
      }
    }
  }
</style>
