<template>
  <div class="MainVideo main-video video">
    <video-player
      ref="videoPlayer"
      :options="playerOptions"
      :playsinline="true"
      class="vjs-custom-skin"
      @ready="playerReadied"
    />
  </div>

</template>

<script>

import { videoPlayer } from 'vue-video-player';
import 'video.js/dist/video-js.css';

export default {
  components: {
    videoPlayer,
  },
  props: {
    media: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      playerOptions: {
        // videojs options
        height: '360',
        autoplay: false,
        muted: false,
        language: 'en',
        playbackRates: [0.7, 1.0, 1.5, 2.0],
        sources: [{
          type: 'video/mp4',
          // src: this.media.filePath,
          src: 'http://vjs.zencdn.net/v/oceans.mp4',
        }],
        poster: 'https://surmon-china.github.io/vue-quill-editor/static/images/surmon-1.jpg',
      },
    };
  },
  computed: {
    player() {
      return this.$refs.videoPlayer.player;
    },
  },
  watch: {
    media(oldVal) {
      this.postVideoPlayer.src(oldVal.filePath);
    },
  },
  mounted() {
    setTimeout(() => {
      this.player.muted(false);
    }, 5000);
  },
  methods: {
    // player is ready
    playerReadied(player) {
      player.currentTime(10);
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .main-video {
    padding : 0 0 26px 26px;
  }

</style>
