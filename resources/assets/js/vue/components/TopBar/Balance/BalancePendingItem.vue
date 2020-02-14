<template>
  <div class="BalancePendingItem bp-item">
    <div class="bp-item__left">
      <Avatar
        v-if="donation.payer.avatar.includes('/profile/default/avatar')"
        :username="donation.payer.displayName"
        :size="32"
        custom-class="bp-item__avatar"
      />
      <div
        v-else
        :style="{'background-image': `url(${donation.payer.avatar})`}"
        class="bp-item__avatar"
      />
      <div class="bp-item__title-box">
        <div class="bp-item__title">
          {{ donation.payer.displayName }}
        </div>
        <div class="bp-item__sub-title">
          <!--donated at:{{ formatDateTime(donation.createdAt) }}-->
          {{ formatDateTime(donation.createdAt) }}
        </div>
      </div>
    </div>
    <div class="bp-item__right">
      <div class="bp-item__amount">
        $ {{ donation.amount }}
      </div>
    </div>

  </div>
</template>

<script>
import moment from 'moment';
import Avatar from '../../ProfilePage/Avatar';

export default {
  name: 'BalancePendingItem',
  components: {
    Avatar,
  },
  props: {
    donation: {
      type: Object,
      default: () => ({}),
    },
  },
  methods: {
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

  .bp-item {
    fl-between();
    height: 40px;
    padding: 0 24px;
    border-bottom: 1px solid $border;
    &__left {
      fl-left();
    }
    &__right {
      fl-right();
    }
    &__avatar {
      fl-center();
      background: center center/cover no-repeat;
      border-radius: 50%;
      height: 32px;
      width: 32px;
      line-height: 1;
    }
    &__title-box {
      display: flex;
      flex-direction: column;
      padding-left: 12px;
    }
    &__title {
      fnt($text-light, 12px, $weight-semibold, left);
      line-height: 1;
    }
    &__sub-title {
      fnt($primary, 10px, $weight-light, left);
    }
    &__amount {
      fnt($primary, 18px, $weight-bold, right);
    }
  }
</style>
