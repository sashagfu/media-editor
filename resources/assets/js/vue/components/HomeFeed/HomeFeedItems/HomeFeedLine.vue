<template>
  <div class="HomeFeedLine hf-line">
    <div
      v-for="(item, key) in clipsLine"
      :key="key"
      class="hf-line__item"
    >
      <home-feed-item
        :item="item"
        :users-data="usersData"
      />
    </div>
  </div>
</template>

<script>
import HomeFeedItem from 'Pages/MediaItem/HomeFeedItem';

export default {
  name: 'HomeFeedLine',

  components: {
    HomeFeedItem,
  },

  props: {
    clips: {
      type: Array,
      default: () => [],
    },
    usersData: {
      type: Boolean,
      default: true,
    },
  },
  computed: {
    clipsLine() {
      return this.clips.slice(0, 4);
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
