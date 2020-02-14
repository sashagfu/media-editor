<template>
  <div class="Followers flw">
    <FollowersManageBar
      :search-text="searchText"
      @input-search-text="updateSearch"
    />
    <div v-if="$apollo.queries.fetchFollowers.loading">
      <font-awesome-icon
        :icon="['fas', 'spinner']"
        spin
        class="fa-icon"
      />
    </div>
    <div v-else-if="!fetchFollowers.length">
      <div class="flw__no-items">
        {{ trans('network.no_items') }}
      </div>
    </div>
    <div
      v-else
      class="flw__main"
    >
      <div
        v-for="user in filteredFollowers"
        :key="`fw-${user.uuid}`"
        class="flw__item"
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
import { has } from 'lodash';

import FETCH_FOLLOWERS from 'Gql/users/queries/fetchFollowers.graphql';
import SUBSCRIPTION_FOLLOWER_CREATED from 'Gql/users/subscriptions/followerCreated.graphql';
import SUBSCRIPTION_FOLLOWER_DELETED from 'Gql/users/subscriptions/followerDeleted.graphql';

import FollowersManageBar from './FollowersManageBar';
import UserItem from '../UserItem';

export default {
  name: 'Followers',
  components: {
    FollowersManageBar,
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
      fetchFollowers: [],
      searchText: '',
    };
  },
  computed: {
    ...mapGetters('general', ['activeUser']),
    filteredFollowers() {
      return this.fetchFollowers.filter((item) => {
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
    fetchFollowers: {
      query: FETCH_FOLLOWERS,
      variables() {
        return {
          userId: this.user.id,
        };
      },
      subscribeToMore: [
        {
          document: SUBSCRIPTION_FOLLOWER_CREATED,
          variables() {
            return {
              userId: this.activeUser.id,
            };
          },
          updateQuery: (previousResult, { subscriptionData }) => {
            const { fetchFollowers } = previousResult;
            if (!has(subscriptionData, 'data')) {
              return { ...fetchFollowers };
            }
            const { followerCreated } = subscriptionData.data;
            if (!has(followerCreated, 'uuid') || fetchFollowers.find(flw => flw.uuid === followerCreated.uuid)) {
              return previousResult;
            }
            return {
              fetchFollowers: [
                ...fetchFollowers,
                followerCreated,
              ],
            };
          },
        },
        {
          document: SUBSCRIPTION_FOLLOWER_DELETED,
          variables() {
            return {
              userId: this.activeUser.id,
            };
          },
          updateQuery: (previousResult, { subscriptionData }) => {
            const { fetchFollowers } = previousResult;
            if (!has(subscriptionData, 'data')) {
              return { ...fetchFollowers };
            }
            const { followerDeleted } = subscriptionData.data;
            const followers = fetchFollowers.filter(flw => flw.uuid !== followerDeleted.uuid);
            return {
              fetchFollowers: [
                ...followers,
              ],
            };
          },
        },
      ],
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

  .flw {
    display: flex;
    flex-direction: column;
    &__no-items {
      fl-center;
      fnt($grey-lighter, 28px, $weight-light, center);
      line-height: 1;
      padding: 56px 0;
    }
    &__spinner {
      fl-center;
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
