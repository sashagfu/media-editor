<template>
  <div class="ProfilePageAboutMain pp-about-main">
    <ProfilePageAbout
      v-bind="$props"
    />
  </div>
</template>

<script>
import ProfilePageAbout from './ProfilePageAbout';

export default {
  name: 'ProfilePageAboutMain',
  components: {
    ProfilePageAbout,
  },
  props: {
    user: {
      type: Object,
      default: () => ({}),
    },
  },
};
</script>
