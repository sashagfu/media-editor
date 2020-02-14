<template>
  <div class="ProfilePagePlaylistsMain pp-playlists-main">
    <ProfilePagePlaylists
      v-bind="$props"
    />
  </div>
</template>

<script>
import ProfilePagePlaylists from './ProfilePagePlaylists';

export default {
  name: 'ProfilePagePlaylistsMain',
  components: {
    ProfilePagePlaylists,
  },
  props: {
    user: {
      type: Object,
      default: () => ({}),
    },
  },
};
</script>
