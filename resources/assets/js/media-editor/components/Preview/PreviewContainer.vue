<template>
  <div class="PreviewContainer">
    <div class="me-player">
      <div
        v-loading="isLoading"
        class="me-player__screen"
      >
        <preview-gallery v-if="isGalleryPreview"/>
        <preview-project v-else/>
      </div>
      <preview-player-controls/>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import PreviewGallery from './PreviewGallery';
import PreviewPlayerControls from './PreviewPlayerControls';
import PreviewProject from './PreviewProject';

export default {
  name: 'PreviewContainer',
  components: {
    PreviewGallery,
    PreviewPlayerControls,
    PreviewProject,
  },
  data() {
    return {
      isGalleryPreview: false,
    };
  },
  computed: {
    ...mapGetters('preview/project', [
      'canPlay',
    ]),
    ...mapGetters('preview', [
      'galleryItemId',
    ]),
    isLoading() {
      return !this.canPlay;
    },
  },
  watch: {
    galleryItemId(galleryItemId) {
      this.isGalleryPreview = !!galleryItemId;
    },
  },
};
</script>

