<template>
  <div class="MyBalance my-balance">
    <MyBalanceManageBar
      v-bind="$props"
      :credit="myCredit"
      :debit="myDebit"
      :filter-debit="filterDebit"
      :filter-credit="filterCredit"
      :search-text="searchText"
      :value-period="valuePeriod"
      @input-search-text="updateSearch"
      @change-period="changePeriod"
      @toggle-debit="toggleDebit"
      @toggle-credit="toggleCredit"
    />
    <div v-if="$apollo.queries.fetchTransactions.loading">
      <font-awesome-icon
        :icon="['fas', 'spinner']"
        spin
        class="fa-icon"
      />
    </div>
    <div
      v-else
      class="my-balance__main box">
      <template
        v-if="filteredTransactions.length"
      >
        <TransactionItem
          v-for="transaction in paginationTransactions[currentPage - 1]"
          :key="`trs-${transaction.id}`"
          :transaction="transaction"
          v-bind="$props"
        />
        <div
          v-if="filteredTransactions.length > recordPerPage"
          class="my-balance__pag"
        >
          <el-pagination
            :page-size="recordPerPage"
            :total="filteredTransactions.length"
            :current-page.sync="currentPage"
            layout="total, prev, pager, next"
            @current-change="handleCurrentChange"
          />
        </div>
      </template>
      <div
        v-else
        class="my-balance__no-items">
        {{ trans('donate.no_items') }}
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment';
import FETCH_TRANSACTIONS from 'Gql/donations/queries/fetchTransactions.graphql';

import MyBalanceManageBar from './MyBalanceManageBar';
import TransactionItem from './TransactionItem';

export default {
  name: 'MyBalance',
  components: {
    MyBalanceManageBar,
    TransactionItem,
  },
  props: {
    user: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      searchText: '',
      fetchTransactions: [],
      filterDebit: false,
      filterCredit: false,

      valuePeriod: {
        from: moment().subtract(1, 'month').startOf('month').format('YYYY-MM-DD HH:mm:ss'),
        to: moment().subtract(1, 'month').endOf('month').format('YYYY-MM-DD HH:mm:ss'),
      },
      recordPerPage: 10,
      currentPage: 1,
    };
  },
  computed: {
    myCredit() {
      let credit = 0;
      this.filteredTransactions.forEach((t) => {
        if (String(t.payerId).toLowerCase() === String(this.user.id).toLowerCase()) {
          credit += t.amount;
        }
      });
      return credit;
    },
    myDebit() {
      let debit = 0;
      this.filteredTransactions.forEach((t) => {
        if (String(t.payeeId).toLowerCase() === String(this.user.id).toLowerCase()) {
          debit += t.amount;
        }
      });
      return debit;
    },
    paginationTransactions() {
      const pagination = [];
      for (let i = 0; i < this.filteredTransactions.length; i += this.recordPerPage) {
        pagination.push(this.filteredTransactions.slice(i, i + this.recordPerPage));
      }
      return pagination;
    },
    filteredTransactions() {
      return this.fetchTransactions.filter((item) => {
        let searchText = true;
        let filterCredit = false;
        let filterDebit = false;

        if (this.searchText) {
          const indexPayerDisplayName = item.payer.displayName.toLowerCase()
            .indexOf(this.searchText.toLowerCase());
          const indexPayerMail = item.payer.email.toLowerCase()
            .indexOf(this.searchText.toLowerCase());
          const indexPayeeDisplayName = item.payer.displayName.toLowerCase()
            .indexOf(this.searchText.toLowerCase());
          const indexPayeeEmail = item.payer.email.toLowerCase()
            .indexOf(this.searchText.toLowerCase());
          const indexAmount = String(item.amount).toLowerCase()
            .indexOf(this.searchText.toLowerCase());
          searchText = (indexPayerDisplayName !== -1)
            || (indexPayerMail !== -1)
            || (indexPayeeDisplayName !== -1)
            || (indexPayeeEmail !== -1)
            || (indexAmount !== -1);
        }

        if (this.filterDebit) {
          filterDebit = (String(item.payeeId).toLowerCase() === String(this.user.id).toLowerCase())
            && (item.type !== 'withdraw');
        }
        if (this.filterCredit) {
          filterCredit = (String(item.payerId).toLowerCase() === String(this.user.id).toLowerCase())
            && (item.type !== 'topup');
        }

        if (!this.filterDebit && !this.filterCredit) {
          filterDebit = true;
          filterCredit = true;
        }
        return searchText && (filterDebit || filterCredit);
      });
    },
  },
  methods: {
    toggleDebit() {
      this.filterDebit = !this.filterDebit;
    },
    toggleCredit() {
      this.filterCredit = !this.filterCredit;
    },
    updateSearch(text) {
      this.searchText = text;
    },
    changePeriod(value) {
      this.valuePeriod = value;
    },
    handleCurrentChange(val) {
      this.currentPage = val;
    },
  },
  apollo: {
    fetchTransactions: {
      query: FETCH_TRANSACTIONS,
      variables() {
        return {
          range: this.valuePeriod,
        };
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
    fnt($grey, 18px, $weight-light, center);
  }
  .my-balance {
    display: flex;
    flex-direction: column;
    margin-bottom: 1.5rem;
    &__main {}
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
