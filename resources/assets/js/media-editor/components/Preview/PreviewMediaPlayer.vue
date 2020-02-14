<template>
  <div
    v-loading="loading"
    class="MediaPlayer mp"
  >
    <video
      ref="video"
      :src="src"
      preload="auto"
      @durationchange="updateDuration"
      @loadstart="setLoadingStart"
      @canplay="onCanPlay"
      @seeking="setLoadingStart"
      @seeked="setLoadingStop"
      @waiting="setLoadingStart"
    />
  </div>
</template>

<script>
export default {
  name: 'PreviewMediaPlayer',
  props: {
    src: {
      type: String,
      default: '',
    },
    thumb: {
      type: String,
      default: '',
    },
    playing: {
      type: Boolean,
      default: false,
    },
    currentTime: {
      type: Number,
      default: 0,
    },
  },
  data() {
    return {
      lastUpdatedCurrentTime: 0,
      loading: false,
    };
  },
  computed: {
    el() {
      return this.$refs.video;
    },
  },
  watch: {
    playing(playing) {
      this.setPlaying(playing);
    },
    currentTime(currentTime) {
      if (this.lastUpdatedCurrentTime !== currentTime) {
        this.el.currentTime = currentTime;
      }
    },
  },
  methods: {
    updateCurrentTime() {
      const { currentTime } = this.el;
      this.$emit('update:currentTime', currentTime);
      this.lastUpdatedCurrentTime = currentTime;


      if (this.playing) {
        window.requestAnimationFrame(this.updateCurrentTime);
      }
    },
    updateDuration() {
      this.$emit('update:duration', this.el.duration);
    },
    updateLoading() {
      this.$emit('update:loading', this.loading);
    },
    onCanPlay() {
      this.setLoadingStop();
      this.setPlaying();
    },
    setLoadingStart() {
      this.loading = true;
      this.updateLoading();
    },
    setLoadingStop() {
      this.loading = false;
      this.updateLoading();
    },
    setPlaying(playing = this.playing) {
      if (!this.loading) {
        if (playing) {
          this.el.play();
          this.updateCurrentTime();
        } else {
          this.el.pause();
        }
      }
    },
  },
};
</script>

<style lang="stylus" scoped>
  @import '../../../../sass/front/components/bulma-theme';

  .mp {
    height: 100%;
    .el-loading-mask {
      background-color: rgba($white, .5);
    }
  }
</style>
