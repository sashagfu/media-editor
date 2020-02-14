<template>
  <div class="SingleProjectPageVideoPlayer box vp">
    <video-player
      v-if="asset"
      ref="videoPlayer"
      :options="playerOptions"
      :playsinline="true"
      class="vjs-custom-skin"
      @ready="playerReadied"
      @timeupdate="onPlayerTimeUpdate"
    />
  </div>
</template>

<script>
import { has } from 'lodash';
import { videoPlayer } from 'vue-video-player';
import 'video.js/dist/video-js.css';

import INCREMENT_VIEWS from 'Gql/projects/mutations/incrementViews.graphql';

import * as constants from 'Helpers/constants';

export default {
  name: 'SingleProjectPageVideoPlayer',
  components: {
    videoPlayer,
  },
  props: {
    project: {
      type: Object,
      default: () => ({}),
    },
    height: {
      type: Number,
      default: () => 500,
    },
  },
  data() {
    return {
      viewIncremented: false,
      watchTime: 0,
    };
  },
  computed: {
    playerOptions() {
      return ({
        height: this.height,
        autoplay: false,
        muted: false,
        language: 'en',
        playbackRates: [0.7, 1.0, 1.5, 2.0],
        sources: [{
          type: 'video/mp4',
          src: this.filePath,
        }],
        poster: this.thumbPath,
      });
    },
    player() {
      return this.$refs.videoPlayer.player;
    },
    asset() {
      return this.project.assets.find(a => a.type === constants.FULL);
    },
    filePath() {
      return has(this.asset, 'filePath') ? this.asset.filePath : '';
    },
    thumbPath() {
      return has(this.asset, 'thumbPath') ? this.asset.thumbPath : '';
    },
  },
  methods: {
    // player
    playerReadied(player) {
      player.currentTime(0);
    },

    async onPlayerTimeUpdate() {
      if (this.viewIncremented) return;

      this.watchTime += 0.25; // Average time trigger timeUpdate hook

      if (this.watchTime > 3) {
        await this.incrementViews();
        this.viewIncremented = true;
      }
    },

    async incrementViews() {
      this.$apollo.mutate({
        mutation: INCREMENT_VIEWS,
        variables: {
          id: this.project.id,
        },
      });
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .vp // Video Player
    overflow hidden
    width 100%
    & >>> .video-js .vjs-tech
      width calc(100% - 1px)

</style>
