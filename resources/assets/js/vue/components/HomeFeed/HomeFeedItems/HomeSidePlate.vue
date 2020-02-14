<template>
  <div class="HomeSidePlate box hs-plate">
    <div class="hs-plate__header">
      {{ header }}
    </div>
    <div class="hs-plate__main">
      <div
        v-for="(clip, index) in topClips"
        :key="index"
        class="hs-plate__item"
      >
        <home-feed-item
          :item="clip"
          :users-data="false"
          :with-footer="false"
        />
      </div>
    </div>
  </div>
</template>

<script>
import HomeFeedItem from 'Pages/MediaItem/HomeFeedItem';

export default {
  name: 'HomeSidePlate',
  components: {
    HomeFeedItem,
  },
  props: {
    header: {
      type: String,
      default: '',
    },
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
    topClips() {
      return this.clips.slice(0, 3);
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .hs-plate
    display flex
    flex-direction column
    &__header
      fl-left()
      fnt($text, 14px, $weight-semibold, left)
      height 52px
      width 100%
      border-bottom 1px solid $border
      padding 0 24px
    &__main
      display flex
      flex-direction column
      padding 16px 24px 24px
    &__item
      height 160px
      &:not(:last-child)
        margin-bottom 24px

</style>
