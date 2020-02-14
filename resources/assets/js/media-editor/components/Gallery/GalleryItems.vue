<template>
  <div class="GalleryItems gi">
    <component :is="activeComponent"/>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import MeMedia from './Media/GalleryMediaItems';
import MeText from './Text/GalleryTextItems';
import MeProjects from './RecentProjects/GalleryRecentProjectsItems';
import MeClips from './Clips/GalleryClipsItems';

export default {
  name: 'GalleryItems',
  components: {
    MeMedia,
    MeText,
    MeProjects,
    MeClips,
  },
  computed: {
    ...mapGetters('gallery', [
      'activeComponent',
    ]),
  },
};
</script>

<style lang="stylus" scoped>
  .gi {
    width: 100%;
    position: relative;
  }
</style>
