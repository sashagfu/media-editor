<template>
  <v-d-r
    v-if="isShowCursor"
    :resizable="false"
    :z="11"
    :x="position"
    :w="1"
    :h="1"
    :minw="1"
    :minh="1"
    axis="x"
    @dragging="move"
    @mousedown.native.stop
  >
    <div class="TimelineRulerCursor me-tl-ruler-cursor">
      <div class="me-tl-ruler-cursor__head">
        <div class="me-tl-ruler-cursor__time">
          <Duration
            :ms="cursorPosition"
            m-zero
            show-frame
          />
        </div>
      </div>
    </div>
  </v-d-r>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import VDR from '../common/vue-draggable-resizable';
import Duration from '../common/Duration';

export default {
  name: 'TimelineRulerCursor',
  components: {
    VDR,
    Duration,
  },
  data() {
    return {
      hideCursor: false,
    };
  },
  computed: {
    ...mapGetters('timeline', [
      'zoomedPxPerSec',
      'cursorPosition',
      'isShowCursor',
      'scrollH',
    ]),
    position() {
      return (this.cursorPosition * this.zoomedPxPerSec) - this.scrollH;
    },
  },
  methods: {
    ...mapActions('timeline', ['setCursorPosition']),
    move(x) {
      this.setCursorPosition({
        cursorPosition: x,
        inPx: true,
        manual: true,
      });
    },
  },
};
</script>

<style lang="stylus" scoped>
  .me-tl-ruler-cursor__time {
    & >>> time {
      user-select: none;
    }
  }
</style>
