<template>
  <div
    :class="[
      'TransactionItem',
      'tr-item',
      {'tr-item--topup':typeTopup},
      {'tr-item--withdraw':typeWithDraw},
    ]"
  >
    <div class="tr-item__left">
      <template v-if="isMy">
        <Avatar
          v-if="transaction.payer.avatar.includes('/profile/default/avatar')"
          :username="transaction.payer.displayName"
          :size="36"
          custom-class="tr-item__avatar"
        />
        <div
          v-else
          :style="{'background-image': `url(${transaction.payer.avatar})`}"
          class="tr-item__avatar"
        />
        <div class="tr-item__title-box">
          <div class="tr-item__type">
            {{ transaction.type }}
          </div>
        </div>
      </template>
      <template v-if="isPayer">
        <Avatar
          v-if="transaction.payer.avatar.includes('/profile/default/avatar')"
          :username="transaction.payer.displayName"
          :size="36"
          custom-class="tr-item__avatar"
        />
        <div
          v-else
          :style="{'background-image': `url(${transaction.payer.avatar})`}"
          class="tr-item__avatar"
        />
        <div class="tr-item__title-box">
          <div class="tr-item__title">
            {{ transaction.payer.displayName }}
          </div>
          <div class="tr-item__sub-title">
            {{ trans('donate.on_al_from') }}: {{ formatDate(transaction.payer.createdAt) }}
          </div>

        </div>
      </template>
      <template v-if="isPayee">
        <Avatar
          v-if="transaction.payee.avatar.includes('/profile/default/avatar')"
          :username="transaction.payee.displayName"
          :size="36"
          custom-class="tr-item__avatar"
        />
        <div
          v-else
          :style="{'background-image': `url(${transaction.payee.avatar})`}"
          class="tr-item__avatar"
        />
        <div class="tr-item__title-box">
          <div class="tr-item__title">
            {{ transaction.payee.displayName }}
          </div>
          <div class="tr-item__sub-title">
            {{ trans('donate.on_al_from') }}: {{ formatDate(transaction.payee.createdAt) }}
          </div>
        </div>
      </template>
    </div>
    <div class="tr-item__center">
      <div class="tr-item__amount tr-item__amount--debit">
        <template v-if="isPayer || typeTopup">
          + {{ transaction.amount }}
        </template>
      </div>
      <div class="tr-item__amount tr-item__amount--credit">
        <template v-if="isPayee || typeWithDraw">
          - {{ transaction.amount }}
        </template>
      </div>
    </div>
    <div class="tr-item__right">
      <!-- TODO if need date for create & accept donations -->
      <!-- <div class="tr-item__donated">
        {{ trans('donate.donated') }}: {{ formatDateTime(transaction.createdAt) }}
      </div>
      <div class="tr-item__accepted">
        {{ trans('donate.accepted') }}: {{ formatDateTime(transaction.updatedAt) }}
      </div> -->
      <div class="tr-item__accepted">
        {{ formatDateTime(transaction.updatedAt) }}
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment';

import * as constants from 'Helpers/constants';

import Avatar from 'Pages/ProfilePage/Avatar';

export default {
  name: 'TransactionItem',
  components: {
    Avatar,
  },
  props: {
    user: {
      type: Object,
      default: () => ({}),
    },
    transaction: {
      type: Object,
      default: () => ({}),
    },
  },
  computed: {
    isMy() {
      return String(this.transaction.payerId).toLowerCase() === String(this.user.id).toLowerCase()
        && String(this.transaction.payeeId).toLowerCase() === String(this.user.id).toLowerCase();
    },
    typeWithDraw() {
      return this.transaction.type === constants.DONATIONS_TYPE_WITHDRAW;
    },
    typeTopup() {
      return this.transaction.type === constants.DONATIONS_TYPE_TOPUP;
    },
    isPayer() {
      return String(this.transaction.payerId).toLowerCase() !== String(this.user.id).toLowerCase();
    },
    isPayee() {
      return String(this.transaction.payeeId).toLowerCase() !== String(this.user.id).toLowerCase();
    },
  },
  methods: {
    formatDate(date) {
      return moment(date).format('MM/DD/YYYY');
    },
    formatDateTime(date) {
      return moment(date).format('MM/DD/YYYY @ hh:mma');
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .tr-item {
    fl-between();
    background: $white;
    height: 56px;
    padding: 0 26px;
    width: 100%;
    &:not(:last-child) {
      border-bottom: 1px solid $border;
    }
    &--topup {
      background: rgba($primary, 0.05);
    }
    &--withdraw {
      background: rgba($danger, 0.05);
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
    &__type {
      fnt($text, 14px, $weight-normal, left);
      text-transform: uppercase;
      line-height: 1;
    }
    &__sub-title {
      fnt($primary, 12px, $weight-light, left);
      line-height: 1;
      &--declined {
        color: $text-light;
      }
    }
    &__amount {
      fl-center();
      fnt($text, 18px, $weight-semibold, center);
      text-transform: uppercase;
      line-height: 1;
      width: 50%;
      &--debit {
        color: $primary;
      }
      &--credit {
        color: $danger;
      }
    }
    &__donated {
      fnt($text-light, 14px, $weight-light, left);
    }
    &__accepted {
      fnt($text-light, 14px, $weight-light, left);
      padding-left: 12px;
    }
  }

</style>
