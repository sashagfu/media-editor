<template>
  <div class="column sr__column">
    <SearchResultUsers
      :active-user="activeUser"
    />
    <SearchResultProjects
      :active-user="activeUser"
    />
    <SearchResultTags
      :active-user="activeUser"
    />
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

import LeftSidebar from 'Pages/LeftSidebar/LeftSidebar';
import TopBar from 'Pages/TopBar/TopBar';
import RightSidebar from '../RightSidebar/RightSidebar';

import SearchResultUsers from './SearchResultUsers';
import SearchResultProjects from './SearchResultProjects';
import SearchResultTags from './SearchResultTags';

export default {
  name: 'SearchResultPage',
  components: {
    LeftSidebar,
    RightSidebar,
    TopBar,
    SearchResultUsers,
    SearchResultProjects,
    SearchResultTags,
  },
  computed: {
    ...mapGetters('general', ['activeUser']),
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .fa-icon {
    fnt($grey, 18px, $weight-light, center);
  }

  .sr {
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
      margin: 0 0 0 72px;
      padding: 32px 32px 0 28px;
      width: 100%;
    }
  }

</style>
