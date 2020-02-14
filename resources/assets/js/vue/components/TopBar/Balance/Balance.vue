<template>
  <div class="Balance balance">
    <div class="balance__box">
      <div
        class="fa-box">
        <!--        <font-awesome-icon-->
        <!--          :icon="['fas', 'dollar-sign']"-->
        <!--          class="fa-icon"-->
        <!--        />-->
        <span>
          <svg
            viewBox="0 0 288 512"
            class="fa-icon svg-inline--fa fa-dollar-sign fa-w-9">
            <path
              fill="currentColor"
              d="M211.9 242.1L95.6 208.9c-15.8-4.5-28.6-17.2-31.1-33.5C60.6 150 80.3 128 105
              128h73.8c15.9 0 31.5 5 44.4 14.1 6.4 4.5 15 3.8 20.5-1.7l22.9-22.9c6.8-6.8
              6.1-18.2-1.5-24.1C240.4 74.3 210.4 64 178.8 64H176V16c0-8.8-7.2-16-16-16h-32c-8.8
              0-16 7.2-16 16v48h-2.5C60.3 64 14.9 95.8 3.1 143.6c-13.9 56.2 20.2 111.2 73
              126.3l116.3 33.2c15.8 4.5 28.6 17.2 31.1 33.5C227.4 362 207.7 384 183 384h-73.8c-15.9
              0-31.5-5-44.4-14.1-6.4-4.5-15-3.8-20.5 1.7l-22.9 22.9c-6.8 6.8-6.1 18.2 1.5 24.1 24.6
              19.1 54.6 29.4 86.3 29.4h2.8v48c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16v-48h2.5c49.2
              0 94.6-31.8 106.4-79.6 13.9-56.2-20.2-111.2-73-126.3z"
              class=""/>
          </svg>
        </span>
      </div>
      <div
        v-if="fetchIncomingDonations.length"
        class="balance__tag"
      >
        {{ fetchIncomingDonations.length }}
      </div>
    </div>
    <div class="balance__dropdown-menu">
      <BalanceDropdownMenu
        :balance="activeUser.balance"
        :pending-donations="fetchIncomingDonations"
      />
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import { has } from 'lodash';

import FETCH_NEW_DONATIONS from 'Gql/donations/queries/fetchIncomingDonations.graphql';
import SUBSCRIPTION_DONATION_CREATED from 'Gql/donations/subscriptions/donationCreated.graphql';

import BalanceDropdownMenu from './BalanceDropdownMenu';

export default {
  name: 'Balance',
  components: {
    BalanceDropdownMenu,
  },
  data() {
    return {
      fetchIncomingDonations: [],
    };
  },
  computed: {
    ...mapGetters('general', ['activeUser']),
  },
  watch: {
    activeUser() {
      this.$apollo.queries.fetchIncomingDonations.refetch();
    },
  },
  apollo: {
    fetchIncomingDonations: {
      query: FETCH_NEW_DONATIONS,
      variables() {
        return {
          userId: this.activeUser.id,
          status: 2,
        };
      },
      subscribeToMore: [
        {
          document: SUBSCRIPTION_DONATION_CREATED,
          variables() {
            return {
              payeeId: this.activeUser.id,
            };
          },
          updateQuery: (previousResult, { subscriptionData }) => {
            if (!has(subscriptionData, 'data')) {
              return previousResult;
            }

            const { fetchIncomingDonations } = previousResult;
            const { donationCreated } = subscriptionData.data;

            if (fetchIncomingDonations.find(d => d.id === donationCreated.id)) {
              return previousResult;
            }

            return {
              fetchIncomingDonations: [
                ...fetchIncomingDonations,
                donationCreated,
              ],
            };
          },
        },
      ],
      skip() {
        return !this.activeUser.id;
      },
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
    fnt($text-light, 1.5rem, $weight-light, center)
    transition all .3s

    &__box {
      fl-center()
      size(24, 24)
      flex 0 0 auto
    }
  }

  .balance {
    display flex
    flex-direction column
    position relative

    &__item {
      position relative
    }

    &:hover &__dropdown-menu {
      display flex
    }

    &__box {
      fl-center();
      size(72, 62)
      cursor pointer
      position relative
      transition all .3s
    }

    &__tag {
      fnt($text-invert, 10px, 700, center)
      fl-center()
      circle(19)
      background-color $primary
      position absolute
      right 12px
      top 12px
    }

    &__dropdown-menu {
      align-items flex-start
      box-shadow 0px 0px 20px 0px rgba($grey-darker, .1)
      display none
      flex-direction column
      left -142px
      max-height 400px
      position absolute
      top 56px
      z-index: 1
    }
  }
</style>
