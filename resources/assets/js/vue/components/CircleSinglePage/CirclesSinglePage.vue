<template>
  <div class="profile-page">
    <top-bar/>
    <div class="profile-page__main">
      <left-sidebar/>
      <div class="column profile-page__column">
        <circle-main
          :circle="circle"
          :user="activeUser"
          :feed-items="feedItems"
          :members="members"
          :can-update-settings="canUpdateSettings"
          :can-see-members="canSeeMembers"
          :can-join="canJoin"
          :is-requestioning="isRequestioning"
          :is-member="isMember"
          :requests="requests"
          :form-type="formType"
          :show-form="showForm"
        />
      </div>
    </div>
  </div>
</template>

<script>
/* @flow */
import { mapGetters } from 'vuex';
import TopBar from '../TopBar/TopBar';
import LeftSidebar from '../LeftSidebar/LeftSidebar';
import CircleMain from './circleSinglePageComponents/CircleMain';

const CirclesSinglePage = {
  name: 'circles-single-page',
  components: {
    TopBar,
    LeftSidebar,
    CircleMain,
  },
  props: {
    circle: {
      type: Object,
      default: () => ({}),
    },
    feedItems: {
      type: Object,
      default: () => ({}),
    },
    members: {
      type: Array,
      default: () => [],
    },
    requests: {
      type: Array,
      default: () => [],
    },
    canUpdateSettings: {
      type: Boolean,
      default: false,
    },
    canSeeMembers: {
      type: Boolean,
      default: false,
    },
    canAddPost: {
      type: Boolean,
      default: false,
    },
    showForm: {
      type: Boolean,
      default: false,
    },
    canJoin: {
      type: Boolean,
      default: false,
    },
    isRequestioning: {
      type: Boolean,
      default: false,
    },
    isMember: {
      type: Boolean,
      default: true,
    },
    formType: {
      type: String,
      default: '',
    },
  },
  computed: {
    ...mapGetters('general', [
      'activeUser',
    ]),
  },
};

export default CirclesSinglePage;
</script>
<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .profile-page {
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
