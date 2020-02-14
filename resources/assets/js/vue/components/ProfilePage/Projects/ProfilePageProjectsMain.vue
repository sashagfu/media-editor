<template>
  <div class="ProfilePageProjectsMain pp-projects-main">
    <ProfilePageCreateProjectBar v-if="activeUser.id == user.id"/>
    <ProfilePageProjects
      v-bind="$props"
    />
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

import ProfilePageCreateProjectBar from './ProfilePageCreateProjectBar';
import ProfilePageProjects from './ProfilePageProjects';

export default {
  name: 'ProfilePageProjectsMain',
  components: {
    ProfilePageCreateProjectBar,
    ProfilePageProjects,
  },
  props: {
    user: {
      type: Object,
      default: () => ({}),
    },
  },
  computed: {
    ...mapGetters('general', ['activeUser']),
  },
};
</script>
