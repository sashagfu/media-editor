<template>
  <div class="ProfilePagePlaylistItem playlist">
    <ProfilePagePlaylistBar
      v-bind="$props"
    />
    <ProfilePagePlaylistLine
      v-bind="$props"
    />
  </div>
</template>

<script>
import ProfilePagePlaylistBar from './ProfilePagePlaylistBar';
import ProfilePagePlaylistLine from './ProfilePagePlaylistLine';

export default {
  name: 'ProfilePagePlaylistItem',
  components: {
    ProfilePagePlaylistBar,
    ProfilePagePlaylistLine,
  },
  props: {
    playlist: {
      type: Object,
      default: () => ({}),
    },
  },
};
</script>
