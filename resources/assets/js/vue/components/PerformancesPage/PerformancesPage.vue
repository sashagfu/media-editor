<template>
  <div class="PerformancesPage performances-page">
    <TopBar />
    <div class="performances-page__main">
      <LeftSidebar />
      <div class="performances-page__column">
        <Feed
          id="feed"
          :user="user"
          :flag-reasons="flagReasons"
        />
      </div>
    </div>
  </div>
</template>

<script>
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

  .fa-icon {
    color $text-light
    font-size 1rem
    margin 16px
  }

  .performances-page {
    display  flex
    flex-direction column
    min-height 100vh
    position relative
    &__main {
      background-color $white-bis
      display flex
      flex-grow 1
      padding-top 73px
      position relative
    }
    &__column {
      margin-left 72px
      padding 32px 32px 0 28px
      width 100%
    }
  }

</style>
