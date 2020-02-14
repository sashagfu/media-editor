<template>
  <div class="BalanceDropdownMenu bd-menu">
    <div class="menu-diamond"/>
    <div class="bd-menu__head-line">
      <div class="bd-menu__head-title">
        {{ trans('donate.your_balance') }}:
      </div>
    </div>
    <div class="bd-menu__balance-box">
      $ {{ balance }}
    </div>
    <!--<template v-if="pendingDonations.length">-->
    <template v-if="true">
      <div class="bd-menu__head-line">
        <div class="bd-menu__head-title">
          {{ trans('donate.your_pending') }}:
        </div>
      </div>
      <template
        v-for="(item, index) in sortedPendingDonations"
        v-if="index < 5"
      >
        <BalancePendingItem
          :key="`bpi-${index}`"
          :donation="item"
        />
      </template>
      <a
        class="bd-menu__more"
        @click="goToDonations"
      >
        {{ trans('donate.show_more') }}
      </a>
    </template>
    <div
      v-else
      class="bd-menu__no-donations"
    >
      {{ trans('donate.no_pending_donations') }}
    </div>
  </div>
</template>

<script>
import moment from 'moment';
import BalancePendingItem from './BalancePendingItem';
import { cloneDeep } from '../../../../helpers/utils';

export default {
  name: 'BalanceDropdownMenu',
  components: {
    BalancePendingItem,
  },
  props: {
    balance: {
      type: [String, Number],
      default: '',
    },
    pendingDonations: {
      type: Array,
      default: () => [],
    },
  },
  computed: {
    sortedPendingDonations() {
      const donations = cloneDeep(this.pendingDonations);
      return donations.sort((donation1, donation2) => {
        if (moment(donation1.createdAt).isAfter(donation2.createdAt)) {
          return -1;
        }
        return 1;
      });
    },
  },
  methods: {
    goToDonations() {
      this.$router.push({
        name: 'DonationsPage',
        hash: '#my-sponsors',
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
  .menu-diamond {
    deg45();
    background-color : $white;
    border-left      : 1px solid $border;
    border-top       : 1px solid $border;
    height           : 8px;
    left             : 169px;
    position         : absolute;
    top              : -5px;
    width            : 8px;
    z-index          : 1;
    +desktop() {
      left : 254px;
    }
  }

  .bd-menu {
    background-color : $white;
    border-radius    : 3px;
    border           : 1px solid $border;
    position         : relative;
    width            : 304px;
    &__head-line {
      fl-between();
      border-bottom: 1px solid $border;
      display: flex;
      height: 36px;
      justify-content: space-between;
      padding: 0 24px;
    }
    &__head-title {
      fnt($text, 9px, $weight-normal, left);
      text-transform: uppercase;
      margin-bottom: -1px;
    }
    &__balance-box {
      fnt($primary, 36px, $weight-bold, center);
      fl-center();
      border-bottom: 1px solid $border;
      height: 64px;
    }
    &__no-donations {
      fnt($text-lighter, 14px, $weight-light, center);
      fl-center();
      height: 64px;
      text-transform: capitalize;
    }
    &__more {
      fnt($primary, 12px, $weight-light, center);
      fl-center();
      cursor: pointer;
      height: 26px;
      padding: 0 24px;
    }
  }

</style>
