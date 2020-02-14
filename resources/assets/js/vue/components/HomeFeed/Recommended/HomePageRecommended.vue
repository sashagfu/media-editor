<template>
  <div class="HomePageRecommended recommended">
    <!--<div v-if="$apollo.queries.fetchRecommended.loading">-->
    <div
      v-if="false"
      class="recommended__spinner"
    >
      <font-awesome-icon
        :icon="['fas', 'spinner']"
        spin
        class="fa-icon"
      />
    </div>
    <div
      v-else
      class="recommended__main"
    >
      <div
        v-for="(item, index) in clips"
        :key="`rc-${index}`"
        class="recommended__item"
      >
        <HomeFeedItemMini
          :item="item"
          :users-data="usersData"
        />
      </div>
    </div>
  </div>
</template>

<script>
import HomeFeedItemMini from 'Pages/MediaItem/HomeFeedItemMini';
import RecommendedNavBar from './RecommendedNavBar';

export default {
  name: 'HomePageRecommended',
  components: {
    HomeFeedItemMini,
    RecommendedNavBar,
  },
  props: {
    // TODO remove if add apollo query fetchRecommended
    recommendedClips: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      usersData: true,
      showAll: false,
    };
  },
  computed: {
    clips() {
      if (this.showAll) {
        return this.recommendedClips;
      }
      // TODO remove if add apollo query fetchRecommended
      return this.recommendedClips.slice(0, 10);
    },
  },
  methods: {
    toggleShowAll() {
      this.showAll = !this.showAll;
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
  .recommended {
    display: flex;
    flex-direction: column;
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
      height: 256px;
      padding: 0 18px 26px 0;
    }
  }
</style>
