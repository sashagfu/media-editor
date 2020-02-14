<template>
  <div class="Following fli">
    <FollowingManageBar
      :search-text="searchText"
      @input-search-text="updateSearch"
    />
    <div v-if="$apollo.queries.fetchFollowing.loading">
      <font-awesome-icon
        :icon="['fas', 'spinner']"
        spin
        class="fa-icon"
      />
    </div>
    <div v-else-if="!fetchFollowing.length">
      <div class="fli__no-items">
        {{ trans('network.no_items') }}
      </div>
    </div>
    <div
      v-else
      class="fli__main"
    >
      <div
        v-for="user in filteredFollowing"
        :key="`fi-${user.uuid}`"
        class="fli__item"
      >
        <UserItem
          :users-data="user"
          :active-user="activeUser"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

import FETCH_FOLLOWING from 'Gql/users/queries/fetchFollowing.graphql';

import FollowingManageBar from './FollowingManageBar';
import UserItem from '../UserItem';

export default {
  name: 'Following',
  components: {
    FollowingManageBar,
    UserItem,
  },
  props: {
    user: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      fetchFollowing: [],
      searchText: '',
    };
  },
  computed: {
    ...mapGetters('general', ['activeUser']),
    filteredFollowing() {
      return this.fetchFollowing.filter((item) => {
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
    updateSearch(text) {
      this.searchText = text;
    },
  },
  apollo: {
    fetchFollowing: {
      query: FETCH_FOLLOWING,
      variables() {
        return {
          userId: this.user.id,
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

  .fli {
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
  }

</style>
