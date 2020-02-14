<template>
  <div class="GalleryControlBar me-gallery-control-bar">
    <div class="me-gallery-control-bar__row">
      <gallery-tabs/>
    </div>
    <div
      class="me-gallery-control-bar__row
                    me-gallery-control-bar__row--p"
    >
      <component :is="activeComponent"/>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import GalleryTabs from './GalleryTabs';
import MeMedia from './Media/GalleryMediaMenu';
import MeText from './Text/GalleryTextMenu';
import MeProjects from './RecentProjects/GalleryRecentProjectsMenu';
import MeClips from './Clips/GalleryClipsMenu';

export default {
  name: 'GalleryControlBar',
  components: {
    GalleryTabs,
    MeMedia,
    MeText,
    MeProjects,
    MeClips,
  },
  data() {
    return {
      activeName: '',
    };
  },
  computed: {
    ...mapGetters('project', [
      'id',
      'title',
    ]),
    ...mapGetters('gallery', [
      'activeComponent',
    ]),
  },
};
</script>

<style lang="stylus" scoped>
  @import '../../../../sass/front/components/bulma-theme';

  .fa-icon {
    color: $text-light;
    font-size: 1rem;
    &:hover {
      color: $text-lighter;
    }
    &--invert {
      color: $white;
      &:hover {
        color: $white-bis;
      }
    }
    &--pointer {
      cursor: pointer;
    }
    &--disabled {
      color: $grey-lighter;
      cursor: auto;
      &:hover {
        color: $grey-lighter;
      }
    }
    &__box {
      padding: 0 4px 0;
    }
  }
  .me-gallery-control-bar {
    border-bottom: 1px solid $border;
    height: 60px;
    width: 100%;

    &__row {
      fl-between();
      height: 24px;
      width: 100%;
      &--p {
        height: 36px;
        padding: 0 24px;
      }
    }
  }
</style>
