<template>
  <div class="HomeFeedRecommended hf-recommended">
    <div
      v-for="(item, key) in clips"
      :key="key"
      class="hf-recommended__item"
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
  name: 'HomeFeedRecommended',
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
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .hf-recommended
    fl-left()
    flex-wrap wrap
    margin-right -26px
    &__item
      width 25%
      height 320px
      padding 0 26px 26px 0

</style>
