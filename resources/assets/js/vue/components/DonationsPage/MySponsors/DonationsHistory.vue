<template>
  <div class="DonationsHistory dn-history">
    <DonationsHistoryManageBar
      :search-text="searchText"
      @input-search-text="updateSearch"
    />
    <div v-if="$apollo.queries.fetchIncomingDonationsHistory.loading">
      <font-awesome-icon
        :icon="['fas', 'spinner']"
        spin
        class="fa-icon"
      />
    </div>
    <div
      v-else
      class="dn-history__main box">
      <template
        v-if="filteredDonations.length"
      >
        <template
          v-for="item in paginationDonationsHistory[currentPage - 1]"
        >
          <DonationsHistoryUser
            :key="`dh-${item.id}`"
            :donations-user="item"
            :donated="donatedAmount(item)"
            :history-user-is-open="historyUserIsOpen"
            :active-user="user"
            @toggle-history-user="toggleHistoryUser"
          />
          <DonationsHistoryItems
            v-if="historyUserIsOpen === item.id"
            :key="`dhi-${item.id}`"
            :donations-history="item.outcomingDonations"
          />
        </template>
        <div
          v-if="filteredDonations.length > recordPerPage"
          class="dn-history__pag"
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
        class="dn-history__no-items">
        {{ trans('donate.no_items') }}
      </div>
    </div>
  </div>
</template>

<script>
import FETCH_DONATIONS_HISTORY from 'Gql/donations/queries/fetchIncomingDonationsHistory.graphql';

import DonationsHistoryManageBar from './DonationsHistoryManageBar';
import DonationsHistoryUser from './DonationsHistoryUser';
import DonationsHistoryItems from './DonationsHistoryItems';

export default {
  name: 'DonationsHistory',
  components: {
    DonationsHistoryManageBar,
    DonationsHistoryUser,
    DonationsHistoryItems,
  },
  props: {
    user: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      fetchIncomingDonationsHistory: [],
      searchText: '',
      historyUserIsOpen: '',
      recordPerPage: 10,
      currentPage: 1,
    };
  },
  computed: {
    paginationDonationsHistory() {
      const pagination = [];
      for (let i = 0; i < this.filteredDonations.length; i += this.recordPerPage) {
        pagination.push(this.filteredDonations.slice(i, i + this.recordPerPage));
      }
      return pagination;
    },
    filteredDonations() {
      return this.fetchIncomingDonationsHistory.filter((item) => {
        let searchText = true;
        if (this.searchText) {
          const indexDisplayName = item.displayName.toLowerCase()
            .indexOf(this.searchText.toLowerCase());
          const indexEmail = item.email.toLowerCase()
            .indexOf(this.searchText.toLowerCase());
          searchText = (indexDisplayName !== -1) || (indexEmail !== -1);
        }
        return searchText;
      });
    },
  },
  methods: {
    handleCurrentChange(val) {
      this.currentPage = val;
    },
    donatedAmount(donationsUser) {
      let donated = 0;
      donationsUser.outcomingDonations.forEach((d) => {
        if (d.status === 4) {
          donated += d.amount;
        }
      });
      return donated;
    },
    updateSearch(text) {
      this.searchText = text;
    },
    toggleHistoryUser(item) {
      this.historyUserIsOpen = item;
    },
  },
  apollo: {
    fetchIncomingDonationsHistory: {
      query: FETCH_DONATIONS_HISTORY,
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

  .dn-history {
    display: flex;
    flex-direction: column;
    &__main {
      margin-bottom: 1.5rem;
    }
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
