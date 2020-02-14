<template>
  <div class="SearchResultUsers sr-u">
    <SearchResultBar
      :show-more="showBtnShowMore"
      @show-more="showMore"
    >
      <template slot="header">
        <div class="srb-h__title">
          Show {{ fetchUsers.length }} Users results of for:
          <span class="srb-h__search">
            "{{ searchQuery }}"
          </span>
        </div>
      </template>
    </SearchResultBar>
    <div
      v-if="$apollo.queries.fetchUsers.loading"
      class="sr-u__spinner"
    >
      <font-awesome-icon
        :icon="['fas', 'spinner']"
        spin
        class="fa-icon"
      />
    </div>
    <div
      v-else
      class="sr-u__main"
    >
      <div
        v-for="item in sliceShow"
        :key="`ui-${item.id}`"
        class="sr-u__item"
      >
        <UserItem
          :users-data="item"
          :active-user="activeUser"
          :small-btn="true"
          :show-follow-btn="true"
        />
      </div>
    </div>
    <div
      v-if="showBtnShowMore"
      class="sr-u__show-more"
    >
      <ShowMoreButton
        @show-more="showMore"
      />
    </div>
  </div>
</template>

<script>
import FETCH_USERS from 'Gql/users/queries/fetchUsers.graphql';

import UserItem from 'Pages/NetworkingPage/UserItem';
import ShowMoreButton from 'Pages/Common/ShowMoreButton';

import SearchResultBar from './SearchResultBar';

export default {
  name: 'SearchResultUsers',
  components: {
    SearchResultBar,
    UserItem,
    ShowMoreButton,
  },
  props: {
    activeUser: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      searchQuery: '',
      qtyItemShow: 5,
      fetchUsers: [],
    };
  },
  computed: {
    sliceShow() {
      return this.fetchUsers.slice(0, this.qtyItemShow);
    },
    showBtnShowMore() {
      return this.fetchUsers.length > this.qtyItemShow;
    },
  },
  created() {
    this.searchQuery = this.$route.query.q;
  },
  methods: {
    showMore() {
      if (this.showBtnShowMore) {
        this.qtyItemShow += 5;
      }
    },
  },
  apollo: {
    fetchUsers: {
      query: FETCH_USERS,
      variables() {
        return {
          term: this.searchQuery,
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
  @import '../../../../sass/front/components/bulma-theme';

  .fa-icon {
    fnt($text-light, 1rem, $weight-normal, center)
  }

  .srb-h {
    &__title {
      fnt($text, 14px, $weight-semibold, left);
      text-transform: capitalize;
    }
    &__search {
      fnt($primary, 14px, $weight-semibold, left);
      text-transform: capitalize;
    }
  }

  .sr-u {
    display: flex;
    flex-direction: column;
    &__no-items {
      fl-center();
      fnt($grey-lighter, 28px, $weight-light, center);
      line-height: 1;
      padding: 56px 0;
    }
    &__spinner {
      fl-center();
       margin-bottom: 24px;
    }
    &__main {
      fl-left();
      flex-wrap: wrap;
      margin-right: -18px;
    }
    &__item {
      width: 20%;
      height: 292px;
      padding: 0 18px 26px 0;
    }
    &__show-more {
      fl-center();
    }
  }


</style>
