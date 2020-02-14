<template>
  <div class="donations-page__column">
    <DonationsPageNavBar
      :active-component="activeComponent"
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

import DonationsPageNavBar from './DonationsPageNavBar';
import MySponsorship from './MySponsorhip/MySponsorship';
import MySponsors from './MySponsors/MySponsors';
import TopSponsors from './TopSponsors/TopSponsors';
import MyBalance from './MyBalance/MyBalance';

export default {
  name: 'DonationsPage',
  components: {
    DonationsPageNavBar,
    MySponsorship,
    MySponsors,
    TopSponsors,
    MyBalance,
  },
  props: {
    socials: {
      type: Object,
      default: () => ({}),
    },
    flagReasons: {
      type: Array,
      default: () => [],
    },
    location: {
      type: Object,
      default: () => ({}),
    },
    gmapKey: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      activeComponent: 'MySponsors',
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
        case 'MySponsorship':
          window.location.hash = 'my-sponsorship';
          break;
        case 'MySponsors':
          window.location.hash = 'my-sponsors';
          break;
        case 'TopSponsors':
          window.location.hash = 'top-sponsors';
          break;
        case 'MyBalance':
          window.location.hash = 'my-balance';
          break;
        default:
          window.location.hash = 'my-sponsors';
      }
      this.activeComponent = component;
    },
    setComponentByHash() {
      const { hash } = window.location;
      switch (hash) {
        case '#my-sponsorship':
          this.activeComponent = 'MySponsorship';
          break;
        case '#my-sponsors':
          this.activeComponent = 'MySponsors';
          break;
        case '#top-sponsors':
          this.activeComponent = 'TopSponsors';
          break;
        case '#my-balance':
          this.activeComponent = 'MyBalance';
          break;
        default:
          this.activeComponent = 'MySponsors';
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

  .donations-page {
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
