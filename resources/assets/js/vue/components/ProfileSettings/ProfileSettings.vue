<template>
  <div class="ProfileSettings profile-settings">
    <TopBar/>
    <div class="profile-settings__main">
      <LeftSidebar/>
      <div class="column profile-settings__column"/>
    </div>
  </div>
</template>

<script>
import TopBar from '../TopBar/TopBar';
import LeftSidebar from '../LeftSidebar/LeftSidebar';

export default {
  name: 'ProfileSettings',
  components: {
    LeftSidebar,
    TopBar,
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
    user: {
      type: Object,
      default: () => ({}),
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
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .profile-settings {
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
      margin-left 72px
      padding 32px 32px 0 28px
    }
  }
</style>
