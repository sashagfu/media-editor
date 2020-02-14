<template>
  <div class="ProfilePagePlaylistLine hf-line">
    <div
      v-for="(project, key) in playlist.projects"
      :key="key"
      class="hf-line__item"
    >
      <HomeFeedItem
        :item="project"
        :users-data="usersData"
      />
    </div>
  </div>
</template>

<script>
import HomeFeedItem from 'Pages/MediaItem/HomeFeedItem';

export default {
  name: 'ProfilePagePlaylistLine',

  components: {
    HomeFeedItem,
  },
  props: {
    playlist: {
      type: Object,
      default: () => ({}),
    },
    usersData: {
      type: Boolean,
      default: true,
    },
  },
  computed: {
    clipsLine() {
      return this.playlist.clips.slice(0, 4);
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .hf-line
    fl-left()
    flex-wrap wrap
    margin-right -26px
    &__item
      width 25%
      height 320px
      padding 0 26px 26px 0

</style>
