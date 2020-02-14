<template>
  <div class="HashtagPage hashtag-page">
    <top-bar/>
    <div class="hashtag-page__main">
      <left-sidebar/>
      <div class="column hashtag-page__column">
        <feed
          id="feed"
          :items="items"
          :user="user"
          :flag-reasons="flagReasons"
        />
      </div>
    </div>
  </div>
</template>

<script>
/* @flow */
import TopBar from '../TopBar/TopBar';
import LeftSidebar from '../LeftSidebar/LeftSidebar';
import Feed from '../Feed/Feed';

export default {
  components: {
    TopBar,
    LeftSidebar,
    Feed,
  },
  props: {
    items: {
      type: Object,
      default: () => ({}),
    },
    flagReasons: {
      type: Array,
      default: () => [],
    },
    user: {
      type: Object,
      default: () => ({}),
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .hashtag-page {
    display        : flex;
    flex-direction : column;
    min-height     : 100vh;
    position       : relative;
    &__main {
      background-color : $white-bis;
      display          : flex;
      flex-grow        : 1;
      padding-top      : 73px;
      position         : relative;
    }
    &__column {
      margin-left : 72px;
      padding     : 32px 32px 0 28px;
    }
  }
</style>
