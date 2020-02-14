<template>
  <div class="TopSponsors top-sponsors">
    <TopSponsorsManageBar
      :search-text="searchText"
      @input-search-text="updateSearch"
      @change-period="changePeriod"
    />
    <div v-if="$apollo.queries.fetchGlobalTopSponsors.loading">
      <font-awesome-icon
        :icon="['fas', 'spinner']"
        spin
        class="fa-icon"
      />
    </div>

    <div
      v-else
      class="top-sponsors__main box">
      <template
        v-if="filteredSponsors.length"
      >
        <TopSponsorsUser
          v-for="sponsor in paginationSponsors[currentPage - 1]"
          :key="`ts-${sponsor.id}`"
          :sponsor="sponsor"
          :active-user="user"
        />
        <div
          v-if="filteredSponsors.length > recordPerPage"
          class="top-sponsors__pag"
        >
          <el-pagination
            :page-size="recordPerPage"
            :total="filteredSponsors.length"
            :current-page.sync="currentPage"
            layout="total, prev, pager, next"
            @current-change="handleCurrentChange"
          />
        </div>
      </template>
      <div
        v-else
        class="top-sponsors__no-items">
        {{ trans('donate.no_items') }}
      </div>
    </div>
  </div>
</template>

<script>
import * as constants from 'Helpers/constants';
import FETCH_TOP_SPONSORS from 'Gql/users/queries/fetchGlobalTopSponsors.graphql';

import TopSponsorsManageBar from './TopSponsorsManageBar';
import TopSponsorsUser from './TopSponsorsUser';

export default {
  name: 'TopSponsors',
  components: {
    TopSponsorsManageBar,
    TopSponsorsUser,
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
      valuePeriod: constants.LAST_MONTH,
      searchText: '',
      recordPerPage: 10,
      currentPage: 1,
    };
  },
  computed: {
    paginationSponsors() {
      const pagination = [];
      for (let i = 0; i < this.filteredSponsors.length; i += this.recordPerPage) {
        pagination.push(this.filteredSponsors.slice(i, i + this.recordPerPage));
      }
      return pagination;
    },
    filteredSponsors() {
      return this.fetchGlobalTopSponsors.filter((item) => {
        let searchText = true;

        if (this.searchText) {
          const indexDisplayName = item.displayName.toLowerCase()
            .indexOf(this.searchText.toLowerCase());
          const indexEmail = item.email.toLowerCase()
            .indexOf(this.searchText.toLowerCase());
          const indexAmount = String(item.donationsSum).toLowerCase()
            .indexOf(this.searchText.toLowerCase());
          searchText = (indexDisplayName !== -1) || (indexEmail !== -1) || (indexAmount !== -1);
        }
        return searchText;
      });
    },
  },
  methods: {
    changePeriod(value) {
      this.valuePeriod = value;
    },
    updateSearch(text) {
      this.searchText = text;
    },
    handleCurrentChange(val) {
      this.currentPage = val;
    },
  },
  apollo: {
    fetchGlobalTopSponsors: {
      query: FETCH_TOP_SPONSORS,
      variables() {
        return {
          period: this.valuePeriod,
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

  .top-sponsors {
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
