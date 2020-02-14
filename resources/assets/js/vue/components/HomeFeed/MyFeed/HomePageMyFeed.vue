<template>
  <div class="HomePageMyFeed my-feed">
    <div
      v-if="$apollo.queries.fetchMyFeed.loading"
      class="my-feed__spinner"
    >
      <font-awesome-icon
        :icon="['fas', 'spinner']"
        spin
        class="fa-icon"
      />
    </div>
    <div
      v-else
      class="my-feed__main"
    >
      <div
        v-for="(item, index) in fetchMyFeed"
        :key="`mf-${index}`"
        class="my-feed__item"
      >
        <HomeFeedItemMini
          :item="item"
          :users-data="usersData"
        />
      </div>
    </div>
    <div
      v-if="!$apollo.queries.fetchMyFeed.loading && !fetchMyFeed.length"
      class="my-feed__message"
    >
      <span>
        There are no projects yet
      </span>
    </div>
  </div>
</template>

<script>
import FETCH_MY_FEED from 'Gql/projects/queries/fetchMyFeed.graphql';

import HomeFeedItemMini from 'Pages/MediaItem/HomeFeedItemMini';
import MyFeedNavBar from './MyFeedNavBar';


export default {
  name: 'HomePageMyFeed',
  components: {
    HomeFeedItemMini,
    MyFeedNavBar,
  },
  data() {
    return {
      usersData: true,
      showAll: false,
      fetchProjects: [],
    };
  },
  computed: {
    clips() {
      if (this.showAll) {
        return this.myFeed;
      }
      // TODO remove if add apollo query fetchMyFeed
      return this.myFeed.slice(0, 5).reverse();
    },
  },
  methods: {
    toggleShowAll() {
      this.showAll = !this.showAll;
    },
  },
  apollo: {
    fetchMyFeed: {
      query: FETCH_MY_FEED,
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .fa-icon
    fnt($grey, 18px, $weight-light, center)

  .my-feed
    display flex
    flex-direction column
    margin-bottom 1.5rem
    &__spinner
      fl-center()
    &__main
      fl-left()
      flex-wrap wrap
      margin-right -26px
    &__item
      width 20%
      height 256px
      padding 0 18px 26px 0
    &__message
      display flex
      justify-content center
      align-items center
      color #dbdbdb

</style>
