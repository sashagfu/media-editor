<template>
  <div
    :class="[
      'GalleryClipsList',
      'me-gallery-list',
      {'me-gallery-list--grid': !showList}
    ]"
  >
    <template v-if="itemsClip.length && showList">
      <gallery-clips-list-item
        v-for="(item, $index) in filteredItems"
        :key="$index"
        :item="item"
      />
    </template>
    <template v-else-if="itemsClip.length && !showList">
      <gallery-clips-grid-item
        v-for="(item, $index) in filteredItems"
        :key="$index"
        :item="item"
      />
    </template>
    <div
      v-if="$apollo.queries.fetchSavedAssets.loading"
      class="me-gallery-list__empty mgl-empty"
    >
      <font-awesome-icon
        :icon="['fas', 'spinner']"
        spin
        class="fa-icon fa-icon--paperclip"
      />
    </div>
    <div
      v-else-if="itemsClip.length && !filteredItems.length"
      class="me-gallery-list__empty mgl-empty">
      <font-awesome-icon
        :icon="['fas', 'paperclip']"
        class="fa-icon fa-icon--paperclip fa-icon--pointer"
      />
      <div class="mgl-empty__title">
        Clips list is empty
      </div>
    </div>
  </div>
</template>

<script>
import * as constants from 'Helpers/constants';
import { mapGetters, mapActions } from 'vuex';
import FETCH_CLIPS from 'Gql/clips/queries/fetchClips.graphql';
import GalleryClipsListItem from './GalleryClipsListItem';
import GalleryClipsGridItem from './GalleryClipsGridItem';

export default {
  name: 'GalleryClipsItems',
  components: {
    GalleryClipsListItem,
    GalleryClipsGridItem,
  },
  data() {
    return {
      constants,
    };
  },
  apollo: {
    fetchSavedAssets: {
      query: FETCH_CLIPS,
    },
  },
  computed: {
    ...mapGetters('gallery', [
      'loading',
      'itemsClip',
      'showList',
      'searchText',
      'filtersFull',
      'filtersVideos',
      'filtersAudios',
    ]),
    filteredItems() {
      if (!this.itemsClip.length) {
        return [];
      }
      return this.itemsClip.filter((item) => {
        let searchText = true;
        let filtersVideos = false;
        let filtersAudios = false;
        let filtersFull = false;

        if (this.searchText) {
          const index = item.projectTitle.toLowerCase()
            .indexOf(this.searchText.toLowerCase());
          searchText = index !== -1;
        }
        if (this.filtersVideos) {
          filtersVideos = item.type === constants.VIDEO;
        }
        if (this.filtersAudios) {
          filtersAudios = item.type === constants.AUDIO;
        }
        if (this.filtersFull) {
          filtersFull = item.type === constants.FULL;
        }
        if (!this.filtersVideos && !this.filtersAudios && !this.filtersFull) {
          filtersVideos = true;
          filtersAudios = true;
          filtersFull = true;
        }
        return searchText && (filtersVideos || filtersAudios || filtersFull);
      });
    },
  },
  watch: {
    fetchSavedAssets() {
      this.fetchClips(this.fetchSavedAssets);
    },
  },
  methods: {
    ...mapActions('gallery', [
      'fetchClips',
    ]),
  },
};
</script>

<style lang="stylus" scoped>
    @import '../../../../../sass/front/components/bulma-theme';
    .fa-icon {
        color: $text-light;
        font-size: 1rem;
        transition: all .2s;
        &--paperclip {
            color: $purple;
            font-size: 2rem;
            &:hover {
                opacity: .8;
            }
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
