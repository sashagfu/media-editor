<template>
  <div class="HomeFeedMyFeed hf-my">
    <div class="hf-my__column">
      <div class="hf-my__feed-item">
        <home-side-plate
          :clips="clips.slice(9, 12)"
          header="Most Popular on Actionlime"
        />
      </div>
      <div class="hf-my__feed-item">
        <home-side-plate
          :clips="clips.slice(6, 9)"
          header="Most Popular of your friends"
        />
      </div>
    </div>
    <div class="hf-my__column hf-my__column--center">
      <div
        v-for="(clip, key) in myTopClips"
        :key="key"
        class="hf-my__feed-item">
        <home-feed-item
          :item="clip"
          :users-data="false"
          class="hf-my__feed-item-box"
        />
      </div>
    </div>
    <div class="hf-my__column">
      <div class="hf-my__feed-item">
        <home-side-plate
          :clips="clips.slice(3, 6)"
          header="Last projects"
        />
      </div>
      <div class="hf-my__feed-item">
        <home-side-plate
          :clips="clips.slice(0, 3)"
          header="Last clipped"
        />
      </div>
    </div>
  </div>
</template>

<script>
import HomeFeedItem from 'Pages/MediaItem/HomeFeedItem';
import HomeSidePlate from './HomeSidePlate';

export default {
  name: 'HomeFeedMyFeed',
  components: {
    HomeFeedItem,
    HomeSidePlate,
  },
  props: {
    clips: {
      type: Array,
      default: () => [],
    },
  },
  computed: {
    myTopClips() {
      return this.clips.slice(0, 5);
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .hf-my{
    display: flex;

    &__column {
      display: flex;
      flex-direction: column;
      min-height: 100px;
      width: 25%;
      &:first-child {
        padding-right: 13px;
      }
      &:last-child {
        padding-left: 13px;
      }
      &--center {
        padding: 0 13px;
        width: 50%;
      }
    }
    &__feed-item {
      min-height: 400px;
      position: relative;
      &:not(:last-child) {
        margin-bottom: 26px;
      }
    }
    &__feed-item-box {
      position: absolute;
    }
  }
</style>
