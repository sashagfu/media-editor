<template>
  <div class="PreviewGallery preview-gallery">
    <preview-image
      v-if="isImage"
      :src="galleryItem.filePath"
    />
    <preview-text
      v-else-if="isText"
      :frame="galleryItem"
    />
    <preview-media-player
      v-else
      :src="galleryItem.filePath"
      :thumb="galleryItem.thumb"
      :playing="playing"
      :current-time="currentTime"
      @update:currentTime="setCurrentTime"
      @update:duration="setDuration"
      @update:loading="setLoading"
    />
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import PreviewMediaPlayer from './PreviewMediaPlayer';
import PreviewImage from './PreviewImage';
import PreviewText from './PreviewText';
import * as fileTypes from '../../config/file-types';

export default {
  name: 'PreviewGallery',
  components: {
    PreviewMediaPlayer,
    PreviewImage,
    PreviewText,
  },
  computed: {
    ...mapGetters('preview', [
      'galleryItem',
    ]),
    ...mapGetters('player', [
      'playing',
      'currentTime',
    ]),
    isImage() {
      return this.galleryItem.fileType === fileTypes.IMAGE;
    },
    isText() {
      return this.galleryItem.fileType === fileTypes.SLIDE;
    },
  },
  methods: {
    ...mapActions('player', [
      'setCurrentTime',
      'setDuration',
      'setLoading',
    ]),
  },
};
</script>

<style lang="stylus" scoped>
    .preview-gallery {
        height: 100%;
    }
</style>
