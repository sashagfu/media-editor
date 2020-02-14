<template>
  <div class="ProfilePageClipsMain pp-clips-main">
    <ProfilePageClips
      v-bind="$props"
    />
  </div>
</template>

<script>
import ProfilePageClips from './ProfilePageClips';

export default {
  name: 'ProfilePageClipsMain',
  components: {
    ProfilePageClips,
  },
  props: {
    user: {
      type: Object,
      default: () => ({}),
    },
  },
};
</script>
