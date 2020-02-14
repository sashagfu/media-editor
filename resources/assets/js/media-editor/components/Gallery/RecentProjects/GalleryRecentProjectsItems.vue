<template>
  <div
    :class="[
      'GalleryRecentProjectsList',
      'me-gallery-list',
      {'me-gallery-list--grid': !showList}
    ]"
  >
    <div
      v-if="$apollo.queries.fetchProjects.loading"
      class="me-gallery-list__empty mgl-empty"
    >
      <font-awesome-icon
        :icon="['fas', 'spinner']"
        spin
        class="fa-icon fa-icon--spinner"
      />
    </div>
    <template v-if="itemsRecentProjects.length && showList">
      <GalleryListItem
        v-for="(item, $index) in filteredItems"
        :key="$index"
        :item="item"
      />
    </template>
    <template v-else-if="itemsRecentProjects.length && !showList">
      <GalleryGridItem
        v-for="(item, $index) in filteredItems"
        :key="$index"
        :item="item"
      />
    </template>
    <div
      v-if="!$apollo.queries.fetchProjects.loading && !itemsRecentProjects.length"
      class="me-gallery-list__empty mgl-empty"
    >
      <font-awesome-icon
        :icon="['fas', 'briefcase']"
        class="fa-icon fa-icon--briefcase fa-icon--pointer"
      />
      <div class="mgl-empty__title">
        Recent project list is empty
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';

import * as constants from 'Helpers/constants';

import FETCH_PROJECTS from 'Gql/projects/queries/fetchProjects.graphql';

import GalleryListItem from './GalleryRecentProjectListItem';
import GalleryGridItem from './GalleryRecentProjectGridItem';

export default {
  name: 'GalleryRecentProjectsList',
  components: {
    GalleryListItem,
    GalleryGridItem,
  },
  data() {
    return {
      fetchProjects: [],
    };
  },
  computed: {
    ...mapGetters('gallery', [
      'itemsRecentProjects',
      'showList',
      'searchText',
    ]),
    filteredItems() {
      return this.itemsRecentProjects.filter((item) => {
        let searchText = true;

        if (this.searchText) {
          const index = item.project.title.toLowerCase()
            .indexOf(this.searchText.toLowerCase());
          searchText = index !== -1;
        }
        return searchText;
      });
    },
  },
  watch: {
    fetchProjects(projects) {
      /*
        We need in vuex list of actual FULL assets, not projects, (to ability drag into timeline
        assets instead of projects, so lets generate array of actual assets
      */
      // Filter projects w/o valid assets
      let recentProjectsAssets = projects.filter(p => p.assets.length);
      recentProjectsAssets = recentProjectsAssets
        .map(p => p.assets.find(a => a.type === constants.FULL));

      this.fetchRecentProjects(recentProjectsAssets);
    },
  },
  methods: {
    ...mapActions('gallery', [
      'fetchRecentProjects',
    ]),
  },
  apollo: {
    fetchProjects: {
      query: FETCH_PROJECTS,
      variables: {
        status: 'published',
      },
    },
  },
};
</script>

<style lang="stylus" scoped>
  @import '../../../../../sass/front/components/bulma-theme';
  .fa-icon {
    color: $text-light;
    font-size: 1rem;
    transition: all .2s;
    &--briefcase {
      color: $turquoise;
      font-size: 2rem;
      &:hover {
        opacity: .8;
      }
    }
    &--spinner {
      color: $turquoise;
      font-size: 2rem;
    }
    &--pointer {
      cursor: pointer;
    }
    &__box {
      padding: 0 4px 0;
    }
    & + & {
      margin-left: 8px;
    }
  }
  .mgl-empty {
    &__title {
      fnt($text-light, 1rem, $weight-semibold, center);
    }
  }
</style>
