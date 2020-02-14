<template>
  <div class="MyDonationsHistoryItems dh-items">
    <div class="dh-items__left"/>
    <div class="dh-items__right">
      <div
        v-for="(item, key) in donationsHistory"
        :key="`dhi-${key}`"
        class="dh-items__row"
      >
        <div class="dh-items__create">
          {{ formatDate(item.createdAt) }}
        </div>
        <div
          :class="[
            'dh-items__amount',
            {'dh-items__amount--pending': item.status === 2},
            {'dh-items__amount--declined': item.status === 3},
            {'dh-items__amount--expired': item.status === 7},
          ]"
        >
          $ {{ item.amount }}
        </div>
        <div
          :class="[
            'dh-items__status',
            {'dh-items__status--pending': item.status === 2},
            {'dh-items__status--declined': item.status === 3},
            {'dh-items__status--expired': item.status === 7},
          ]"
        >
          {{ formatStatus(item.status) }}
        </div>
        <div class="dh-items__update">
          {{ trans('donate.at') }}: {{ formatDate(item.updatedAt) }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment';
import * as constants from 'Helpers/constants';

export default {
  name: 'MyDonationsHistoryItems',
  props: {
    donationsHistory: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      constants,
    };
  },
  methods: {
    formatDate(date) {
      return moment(date).format('MM/DD/YYYY @ hh:mmA');
    },
    formatStatus(status) {
      if (status === 4) {
        return 'accepted';
      }
      if (status === 3) {
        return 'declined';
      }
      if (status === 7) {
        return 'expired';
      }
      return 'pending';
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .dh-items {
    fl-between();
    border-bottom: 1px solid $border;
    background-color: $white-bis;

    &__left {
      fl-center();
      width: 35%;
      // height: 200px;
    }
    &__right {
      width: 65%;
    }
    &__row {
      fl-right();
      padding: 4px 26px;
    }
    &__create {
      fnt($text-light, 14px, $weight-light, left);
      width: 30%;
    }
    &__amount {
      fnt($primary, 14px, $weight-normal, left);
      width: 20%;
      &--pending {
        color: $info;
      }
      &--declined {
        color: $danger;
      }
      &--expired {
        color: $text-light;
      }
    }
    &__status {
      fnt($primary, 14px, $weight-light, left);
      width: 10%;
      &--pending {
        color: $info;
      }
      &--declined {
        color: $danger;
      }
      &--expired {
        color: $text-light;
      }
    }
    &__update {
      fnt($text-light, 14px, $weight-light, left);
      width: 40%;
    }
  }
</style>
