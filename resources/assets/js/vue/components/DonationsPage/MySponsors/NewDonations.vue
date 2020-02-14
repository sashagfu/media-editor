<template>
  <div class="NewDonations new-dn">
    <NewDonationsManageBar
      :search-text="searchText"
      :pending="pending"
      :accepted="accepted"
      :declined="declined"
      :expired="expired"
      :user="user"
      @toggle-pending="togglePending"
      @toggle-accepted="toggleAccepted"
      @toggle-declined="toggleDeclined"
      @toggle-expired="toggleExpired"
      @input-search-text="updateSearch"
    />
    <div v-if="$apollo.queries.fetchIncomingDonations.loading">
      <font-awesome-icon
        :icon="['fas', 'spinner']"
        spin
        class="fa-icon"
      />
    </div>
    <div
      v-else
      class="new-dn__main box">
      <template
        v-if="filteredDonations.length"
      >
        <NewDonationsUser
          v-for="donation in paginationNewDonations[currentPage - 1]"
          :key="`ndu-${donation.id}`"
          :new-donation="donation"
        />
        <div
          v-if="filteredDonations.length > recordPerPage"
          class="new-dn__pag"
        >
          <el-pagination
            :page-size="recordPerPage"
            :total="filteredDonations.length"
            :current-page.sync="currentPage"
            layout="total, prev, pager, next"
            @current-change="handleCurrentChange"
          />
        </div>
      </template>
      <div
        v-else
        class="new-dn__no-items">
        {{ trans('donate.no_items') }}
      </div>
    </div>
  </div>
</template>

<script>
import * as constants from 'Helpers/constants';

import FETCH_NEW_DONATIONS from 'Gql/donations/queries/fetchIncomingDonations.graphql';

import NewDonationsManageBar from './NewDonationsManageBar';
import NewDonationsUser from './NewDonationsUser';

export default {
  name: 'NewDonations',
  components: {
    NewDonationsManageBar,
    NewDonationsUser,
  },
  props: {
    user: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      constants,
      searchText: '',
      pending: false,
      accepted: false,
      declined: false,
      expired: false,
      recordPerPage: 10,
      currentPage: 1,
    };
  },
  computed: {
    paginationNewDonations() {
      const pagination = [];
      for (let i = 0; i < this.filteredDonations.length; i += this.recordPerPage) {
        pagination.push(this.filteredDonations.slice(i, i + this.recordPerPage));
      }
      return pagination;
    },
    filteredDonations() {
      return this.newDonations.filter((item) => {
        let searchText = true;
        let filtersPending = false;
        let filtersAccepted = false;
        let filtersDeclined = false;
        let filtersExpired = false;

        if (this.searchText) {
          const indexDisplayName = item.user.displayName.toLowerCase()
            .indexOf(this.searchText.toLowerCase());
          const indexEmail = item.user.email.toLowerCase()
            .indexOf(this.searchText.toLowerCase());
          const indexAmount = String(item.amount).toLowerCase()
            .indexOf(this.searchText.toLowerCase());
          searchText = (indexDisplayName !== -1) || (indexEmail !== -1) || (indexAmount !== -1);
        }

        if (this.pending) {
          filtersPending = item.status === constants.DONATIONS_STATUS_PENDING;
        }
        if (this.accepted) {
          filtersAccepted = item.status === constants.DONATIONS_STATUS_ACCEPTED;
        }
        if (this.declined) {
          filtersDeclined = item.status === constants.DONATIONS_STATUS_DECLINED;
        }
        if (this.expired) {
          filtersExpired = item.status === constants.DONATIONS_STATUS_EXPIRED;
        }

        if (!this.pending && !this.accepted && !this.declined && !this.expired) {
          filtersPending = true;
          filtersAccepted = true;
          filtersDeclined = true;
          filtersExpired = true;
        }
        return searchText
          && (filtersPending || filtersAccepted || filtersDeclined || filtersExpired);
      });
    },
    newDonations() {
      return this.fetchIncomingDonations.map(dnt => ({
        __typename: dnt.__typename,
        id: dnt.id,
        payeeId: dnt.payeeId,
        user: {
          __typename: dnt.payer.__typename,
          id: dnt.payer.id,
          avatar: dnt.payer.avatar,
          email: dnt.payer.email,
          username: dnt.payer.username,
          displayName: dnt.payer.displayName,
          isFollowing: dnt.payer.isFollowing,
          createdAt: dnt.payer.createdAt,
        },
        amount: dnt.amount,
        status: this.getStatusDonation(dnt.status),
        createdAt: dnt.createdAt,
        updatedAt: dnt.updatedAt,
      }));
    },
  },
  apollo: {
    fetchIncomingDonations: {
      query: FETCH_NEW_DONATIONS,
      variables() {
        return {
          userId: this.user.id,
          // status: 2,
        };
      },
    },
  },
  methods: {
    handleCurrentChange(val) {
      this.currentPage = val;
    },
    togglePending() {
      this.pending = !this.pending;
    },
    toggleAccepted() {
      this.accepted = !this.accepted;
    },
    toggleDeclined() {
      this.declined = !this.declined;
    },
    toggleExpired() {
      this.expired = !this.expired;
    },
    updateSearch(text) {
      this.searchText = text;
    },
    getStatusDonation(status) {
      if (status === 1) {
        return constants.DONATIONS_STATUS_PENDING;
      }
      if (status === 2) {
        return constants.DONATIONS_STATUS_PENDING;
      }
      if (status === 4) {
        return constants.DONATIONS_STATUS_ACCEPTED;
      }
      if (status === 3) {
        return constants.DONATIONS_STATUS_DECLINED;
      }
      if (status === 7) {
        return constants.DONATIONS_STATUS_EXPIRED;
      }
      return '';
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
    fnt($grey, 18px, $weight-light, center);
  }

  .new-dn {
    display: flex;
    flex-direction: column;
    margin-bottom: 1.5rem;
    &__pag {
      fl-right();
      height: 40px;
      padding: 0 26px;
    }
    &__no-items {
      fl-center();
      fnt($grey-lighter, 28px, $weight-light, center);
      line-height: 1;
      padding: 56px 0;
    }
  }

</style>
