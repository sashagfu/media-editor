<template>
  <div class="profile-page__column">
    <div>
      <font-awesome-icon
        v-if="$apollo.queries.fetchUser.loading"
        :icon="['fas', 'spinner']"
        spin
        class="fa-icon"
      />
    </div>
    <ProfilePageNavBar
      v-if="fetchUser"
      :active-component="activeComponent"
      :user="fetchUser"
      @change-component="setComponent"
    />
    <component
      v-if="fetchUser"
      :is="activeComponent"
      :user="fetchUser"
    />
  </div>
</template>

<script>
import FETCH_USER from 'Gql/users/queries/fetchUser.graphql';

import ProfilePageNavBar from './ProfilePageNavBar';
import ProfilePageAboutMain from './About/ProfilePageAboutMain';
import ProfilePagePlaylistsMain from './Playlists/ProfilePagePlaylistsMain';
import ProfilePageProjectsMain from './Projects/ProfilePageProjectsMain';
import ProfilePageClipsMain from './SavedClips/ProfilePageClipsMain';

export default {
  components: {
    ProfilePageNavBar,
    ProfilePageAboutMain,
    ProfilePagePlaylistsMain,
    ProfilePageProjectsMain,
    ProfilePageClipsMain,
  },
  props: {
    socials: {
      type: Object,
      default: () => ({}),
    },
    videos: {
      type: [Array, Object],
      default: () => [],
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
    showForm: {
      type: Boolean,
      default: false,
    },
    formType: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      activeComponent: 'ProfilePageAboutMain',
    };
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
        case 'ProfilePageAboutMain':
          window.location.hash = 'about';
          break;
        case 'ProfilePageProjectsMain':
          window.location.hash = 'projects';
          break;
        case 'ProfilePageClipsMain':
          window.location.hash = 'clips';
          break;
        case 'ProfilePagePlaylistsMain':
          window.location.hash = 'playlists';
          break;
        default:
          window.location.hash = 'main';
      }
      this.activeComponent = component;
    },
    setComponentByHash() {
      const { hash } = window.location;
      switch (hash) {
        case '#about':
          this.activeComponent = 'ProfilePageAboutMain';
          break;
        case '#projects':
          this.activeComponent = 'ProfilePageProjectsMain';
          break;
        case '#clips':
          this.activeComponent = 'ProfilePageClipsMain';
          break;
        case '#playlists':
          this.activeComponent = 'ProfilePagePlaylistsMain';
          break;
        default:
          this.activeComponent = 'ProfilePageAboutMain';
      }
    },
  },
  apollo: {
    fetchUser: {
      query: FETCH_USER,
      variables() {
        return {
          username: this.$route.params.username,
        };
      },
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
    fnt($text-light, 1rem, $weight-normal, center)
  }

  .profile-page {
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
