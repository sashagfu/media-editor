<template>
  <div
    :class="[
      'GalleryMediaItems',
      'me-gallery-list',
      {'me-gallery-list--grid': !showList}
    ]"
  >
    <template v-if="items.length && showList">
      <gallery-media-list-item
        v-for="(item, $index) in filteredItems"
        :key="$index"
        :item="item"
      />
    </template>
    <template v-else-if="items.length && !showList">
      <gallery-media-grid-item
        v-for="(item, $index) in filteredItems"
        :key="$index"
        :item="item"
      />
    </template>
    <div
      v-if="$apollo.queries.fetchProjectMedia.loading"
      class="me-gallery-list__empty mgl-empty"
    >
      <font-awesome-icon
        :icon="['fas', 'spinner']"
        spin
        class="fa-icon fa-icon--upload"
      />
    </div>
    <div
      v-else-if="items.length && !filteredItems.length"
      class="me-gallery-list__empty mgi-empty"
    >
      <font-awesome-icon
        :icon="['fas', 'search']"
        class="fa-icon fa-icon--upload fa-icon--pointer"
        @click.prevent="openDialog"
      />
      <div class="mgi-empty__title">
        List is empty, please, change your search query.
      </div>
    </div>
    <div
      v-else-if="!items.length"
      class="me-gallery-list__empty mgi-empty"
    >
      <font-awesome-icon
        :icon="['fas', 'cloud-upload-alt']"
        class="fa-icon fa-icon--upload fa-icon--pointer"
        @click.prevent="openDialog"
      />
      <div class="mgi-empty__title">
        Import your photos, videos or music
      </div>
    </div>
    <UploadDialog/>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';

import FETCH_PROJECT_MEDIA from 'Gql/projects/queries/fetchProjectMedia.graphql';
import SUBSCRIPTION_FILES_UPLOADED from 'Gql/projects/subscriptions/projectFilesUploaded.graphql';
import { has } from 'lodash';

import GalleryMediaListItem from './GalleryMediaListItem';
import GalleryMediaGridItem from './GalleryMediaGridItem';
import UploadDialog from './Upload/UploadDialog';

export default {
  name: 'GalleryMediaItems',
  components: {
    GalleryMediaListItem,
    GalleryMediaGridItem,
    UploadDialog,
  },
  computed: {
    ...mapGetters('gallery', [
      'items',
      'showList',
      'searchText',
      'filtersVideos',
      'filtersAudios',
      'filtersImages',
    ]),
    ...mapGetters('project', {
      project: 'projectData',
    }),
    filteredItems() {
      return this.items.filter((item) => {
        let searchText = true;
        let filtersVideos = false;
        let filtersAudios = false;
        let filtersImages = false;

        if (this.searchText) {
          const index = item.name.toLowerCase()
            .indexOf(this.searchText.toLowerCase());
          searchText = index !== -1;
        }
        if (this.filtersVideos) {
          filtersVideos = item.fileType === 'video';
        }
        if (this.filtersAudios) {
          filtersAudios = item.fileType === 'audio';
        }
        if (this.filtersImages) {
          filtersImages = item.fileType === 'image';
        }
        if (!this.filtersVideos && !this.filtersAudios && !this.filtersImages) {
          filtersVideos = true;
          filtersAudios = true;
          filtersImages = true;
        }
        return searchText && (filtersVideos || filtersAudios || filtersImages);
      });
    },
  },
  watch: {
    fetchProjectMedia(projectMedia) {
      this.setProjectMedia(projectMedia);
    },
  },
  methods: {
    ...mapActions('gallery', [
      'setProjectMedia',
    ]),
    ...mapActions('upload', [
      'openDialog',
    ]),
  },
  apollo: {
    fetchProjectMedia: {
      query: FETCH_PROJECT_MEDIA,
      variables() {
        return {
          projectId: this.project.id,
        };
      },
      subscribeToMore: [
        {
          document: SUBSCRIPTION_FILES_UPLOADED,
          variables() {
            return {
              projectId: this.project.id,
            };
          },
          updateQuery: (previousResult, { subscriptionData }) => {
            if (!has(subscriptionData, 'data')) {
              return previousResult;
            }

            const { fetchProjectMedia: projectMedia } = previousResult;
            const { projectFilesUploaded } = subscriptionData.data;

            return {
              fetchProjectMedia: [
                ...projectMedia,
                ...projectFilesUploaded,
              ],
            };
          },
        },
      ],
      skip() {
        return !this.project.id;
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
    &--upload {
      color: $green;
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
  .mgi-empty {
    &__title {
      fnt($text-light, 1rem, $weight-semibold, center);
    }
  }

</style>
