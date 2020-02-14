<template>
  <div
    :class="[
      'MyNewDonationsUser',
      'nd-item',
      {'nd-item--accepted': isAccepted},
      {'nd-item--declined': isDeclined},
      {'nd-item--expired': isExpired}
    ]"
  >
    <div
      class="nd-item__left"
      @click="goToUser"
    >
      <Avatar
        v-if="newDonation.user.avatar.includes('/profile/default/avatar')"
        :username="newDonation.user.displayName"
        :size="36"
        custom-class="nd-item__avatar"
      />
      <div
        v-else
        :style="{'background-image': `url(${newDonation.user.avatar})`}"
        class="nd-item__avatar"
      />
      <div class="nd-item__title-box">
        <div class="nd-item__title">
          {{ newDonation.user.displayName }}
        </div>
        <div
          :class="[
            'nd-item__sub-title',
            {'nd-item__sub-title--pending': isPending},
            {'nd-item__sub-title--declined': isDeclined || isExpired}
          ]"
        >
          {{ trans('donate.on_al_from') }}: {{ formatDate(newDonation.user.createdAt) }}
        </div>
      </div>
      <div class="nd-item__btn-box">
        <FollowButton
          :following="newDonation.user"
          :active-user="activeUser"
        />
        <MessageButton
          :user="newDonation.user"
          class="nd-item__btn"
        />
      </div>
    </div>
    <div class="nd-item__center">
      <div class="nd-item__donate-box">
        <div class="nd-item__plus">
          {{ isAccepted ? '+' : '' }}
        </div>
        <div
          :class="[
            'nd-item__donate-amount',
            {'nd-item__donate-amount--pending': isPending},
            {'nd-item__donate-amount--declined': isDeclined},
            {'nd-item__donate-amount--expired': isExpired}
          ]"
        >
          $ {{ Number.parseInt(newDonation.amount, 10) }}
        </div>
        <div class="nd-item__donate-date">
          {{ formatDateTime(newDonation.createdAt) }}
        </div>
      </div>
    </div>
    <div
      :class="[
        'nd-item__right',
        {'nd-item__right--expired': isExpired},
        {'nd-item__right--pending': isPending},
      ]"
    >
      <div
        v-if="isExpired"
        class="nd-item__status-box"
      >
        <div class="nd-item__status-title-box">
          <div class="nd-item__status-title nd-item__status-title--expired">
            {{ trans('donate.expired') }}
          </div>
          <div class="nd-item__status-date nd-item__status-date--expired">
            {{ trans('donate.at') }}: {{ formatDateTime(newDonation.updatedAt) }}
          </div>
        </div>
      </div>
      <div
        v-if="isAccepted"
        class="nd-item__status-box"
      >
        <div class="nd-item__status-title-box">
          <div class="nd-item__status-title nd-item__status-title--accepted">
            {{ trans('donate.accepted') }}
          </div>
          <div class="nd-item__status-date nd-item__status-date--accepted">
            {{ trans('donate.at') }}: {{ formatDateTime(newDonation.updatedAt) }}
          </div>
        </div>
        <div class="nd-item__right-box"/>
      </div>
      <div
        v-if="isDeclined"
        class="nd-item__status-box"
      >
        <div class="nd-item__status-title-box">
          <div class="nd-item__status-title nd-item__status-title--declined">
            {{ trans('donate.declined') }}
          </div>
          <div class="nd-item__status-date nd-item__status-date--declined">
            {{ trans('donate.at') }}: {{ formatDate(newDonation.updatedAt) }}
          </div>
        </div>

        <div class="nd-item__right-box"/>
      </div>
      <div
        v-if="isPending"
        class="nd-item__status-box"
      >
        <div class="nd-item__status-title-box">
          <div class="nd-item__status-title nd-item__status-title--pending">
            {{ trans('donate.pending') }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment';
import * as constants from 'Helpers/constants';

import Avatar from 'Pages/ProfilePage/Avatar';
import FollowButton from 'Pages/Common/FollowButton';
import MessageButton from 'Pages/Common/MessageButton';

export default {
  name: 'MyNewDonationsUser',
  components: {
    Avatar,
    FollowButton,
    MessageButton,
  },
  props: {
    newDonation: {
      type: Object,
      default: () => ({}),
    },
    activeUser: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      constants,
      toggle: false,
      acceptLoading: false,
      declineLoading: false,
    };
  },
  computed: {
    isPending() {
      return this.newDonation.status
        === constants.DONATIONS_STATUS_PENDING;
    },
    isAccepted() {
      return this.newDonation.status
        === constants.DONATIONS_STATUS_ACCEPTED;
    },
    isDeclined() {
      return this.newDonation.status
        === constants.DONATIONS_STATUS_DECLINED;
    },
    isExpired() {
      return this.newDonation.status
        === constants.DONATIONS_STATUS_EXPIRED;
    },
  },
  methods: {
    formatDate(date) {
      return moment(date).format('MM/DD/YYYY');
    },
    formatDateTime(date) {
      return moment(date).format('MM/DD/YYYY @ hh:mma');
    },
    toggleFollow() {},
    goToUser() {
      this.$router.push({
        name: 'ProfilePage',
        params: {
          username: this.newDonation.user.username,
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
  @import '../../../../../sass/front/components/bulma-theme';

  .fa-icon {
    fnt($text-light, 14px, $weight-light, left);
    transition : all .3s;
    &--user {
      color: $grey;
    }
    &--follow {
      color: $grey-light;
      font-size: 11px;
    }
    &--envelope {
      color: $grey-light;
      font-size: 11px;
    }
    &--spinner {
      color     : $text-invert;
      font-size : 16px;
      margin    : -2px;
    }
  }

  .nd-item {
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
      fl-between();
      width: 30%;
      &--expired {
        fl-left();
      }
      &--pending {
        fl-right();
      }
    }
    &__avatar {
      fl-center();
      background: center center/cover no-repeat $grey-light;
      border-radius: 50%;
      cursor: pointer;
      flex: 0 0 auto;
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
      &--pending {
        color: $info;
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
    &__plus {
      fnt($primary, 18px, $weight-normal, right);
      line-height: 1;
      width: 12px;
    }
    &__donate-amount {
      fnt($primary, 18px, $weight-normal, left);
      padding-left: 8px;
      line-height: 1;
      width: 56px;
      &--pending {
        color: $info;
      }
      &--declined,
      &--expired {
        color: $text-light;
      }
    }
    &__donate-date {
      fnt($text-lighter, 14px, $weight-normal, left);
      padding-left: 8px;
      line-height: 1;
    }
    &__status-box {
      fl-between();
      width: 100%;
      &--accepted {
        fl-right();
      }
    }
    &__status-title-box {
      fl-left();
    }
    &__right-box {
      fl-right();
    }
    &__status-date {
      fnt($text-light, 12px, $weight-light, left);
      line-height: 1;
      &--pending {
        color: rgba($info, 0.5);
      }
      &--accepted {
        color: rgba($primary, 0.6);
      }
      &--declined {
        color: rgba($danger, 0.6);
      }
    }
    &__status-title {
      fnt($text-lighter, 18px, $weight-semibold, left);
      text-transform: uppercase;
      line-height: 1;
      padding-right: 8px;
      &--pending {
        color: rgba($info, 0.5);
      }
      &--accepted {
        color: rgba($primary, 0.6);
      }
      &--declined {
        color: rgba($danger, 0.6);
      }
    }
    &__btn {
      margin-left: 8px;
    }
    &__btn-count {
      fnt($grey-light, 12px, $weight-normal, center);
    }
  }
</style>
