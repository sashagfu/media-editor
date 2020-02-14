<template>
  <div class="networking-page__column">
    <NetworkingPageNavBar
      :active-component="activeComponent"
      :user="activeUser"
      @change-component="setComponent"
    />
    <component
      :is="activeComponent"
      :user="activeUser"
    />
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

import TopBar from '../TopBar/TopBar';
import LeftSidebar from '../LeftSidebar/LeftSidebar';
import RightSidebar from '../RightSidebar/RightSidebar';
import NetworkingPageNavBar from './NetworkingPageNavBar';


import Following from './Following/Following';
import Followers from './Followers/Followers';

export default {
  name: 'NetworkingPage',
  components: {
    TopBar,
    LeftSidebar,
    RightSidebar,
    NetworkingPageNavBar,

    Following,
    Followers,
  },
  data() {
    return {
      activeComponent: 'Following',
    };
  },
  computed: {
    ...mapGetters('general', [
      'activeUser',
    ]),
  },
  mounted() {
    this.setComponentByHash();
    window.addEventListener('hashchange', this.setComponentByHash, false);
  },
  destroyed() {
    window.removeEventListener('hashchange', this.setComponentByHash, false);
  },
  methods: {
    setComponent(component) {
      switch (component) {
        case 'Following':
          window.location.hash = 'following';
          break;
        case 'Followers':
          window.location.hash = 'followers';
          break;
        default:
          window.location.hash = 'following';
      }
      this.activeComponent = component;
    },
    setComponentByHash() {
      const { hash } = window.location;
      switch (hash) {
        case '#following':
          this.activeComponent = 'Following';
          break;
        case '#followers':
          this.activeComponent = 'Followers';
          break;
        default:
          this.activeComponent = 'Following';
      }
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .networking-page {
    display flex
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
      margin 0 0 0 72px
      padding 32px 32px 0 28px
      width 100%
    }
  }
</style>
