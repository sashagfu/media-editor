<template>
  <div class="CirclePage circle-page">
    <top-bar/>
    <div class="circle-page__main">
      <left-sidebar/>
      <div class="column circle-page__column circles">
        <div class="circles__row columns is-multiline is-marginless">
          <div
            v-for="(circle, key) in circlesResults"
            :key="key"
            class="circles__box column is-4"
            @click="goToCircle(circle)"
          >
            <div class="circles__box-element box">
              <circle-tile :circle="circle"/>
            </div>
          </div>
          <div class="column is-12">
            <infinite-loading
              ref="scrollCircles"
              @infinite="scrollCircles"
            >
              <span slot="no-more"/>
              <span slot="no-results"/>
            </infinite-loading>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
/* @flow */
import InfiniteLoading from 'vue-infinite-loading';
import TopBar from '../TopBar/TopBar';
import LeftSidebar from '../LeftSidebar/LeftSidebar';
import CircleTile from './circlePageComponents/CircleTile';

export default {
  components: {
    TopBar,
    LeftSidebar,
    CircleTile,
    InfiniteLoading,
  },
  props: {
    circles: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      circlesResults: [],
    };
  },
  mounted() {
    this.nextPageUrl = this.makeNextPageLink(this.circles.next_page_url);
    this.circlesResults = this.circles.data;
  },
  methods: {
    scrollCircles() {
      if (this.nextPageUrl === null) {
        this.$refs.scrollCircles.$emit('$InfiniteLoading:complete');
        return;
      }
      this.$http.get(this.nextPageUrl).then((response) => {
        this.circlesResults = this.circlesResults.concat(response.data.data);
        this.$refs.scrollCircles.$emit('$InfiniteLoading:loaded');
        this.nextPageUrl = this.makeNextPageLink(response.data.next_page_url);
      });
    },
    makeNextPageLink(link: String) {
      return link ? `/circles/get/${link.split('/').pop()}` : null;
    },
    goToCircle(circle) {
      window.location.href = `/circles/${circle.slug}`;
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .circle-page {
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

  .circles {
    &__column {
      display        : flex;
      flex-direction : column;
      &--left {
        padding : 26px 13px 26px 32px;
      }
      &--main {
        padding : 26px 13px;
      }
      &--right {
        padding : 26px 32px 26px 13px;
      }
    }
    &__box {
      padding : 10px 13px;
      cursor  : pointer;
    }
    &__box-element {
      height : 100%;
    }
    &__row {
      padding : 13px 0;
      &:first-child {
        padding-top : 0;
      }
      &:last-child {
        padding-bottom : 0;
      }
    }
  }
</style>
