<template>
  <div
    ref="rulerContainer"
    class="TimelineRuler me-tl-ruler"
    @mousedown="onClick"
  >
    <timeline-ruler-canvas/>
    <timeline-ruler-cursor/>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
import TimelineRulerCanvas from './TimelineRulerCanvas';
import TimelineRulerCursor from './TimelineRulerCursor';
import {
  addMultiEventListener,
  removeMultiEventListener,
} from '../../utils/helpers';

export default {
  name: 'TimelineRuler',
  components: {
    TimelineRulerCanvas,
    TimelineRulerCursor,
  },
  mounted() {
    this.setCoordinates();
    this.setDomListeners();
  },
  beforeDestroy() {
    this.removeDomListeners();
  },
  methods: {
    ...mapActions('coordinates', [
      'setElementCoordinates',
    ]),
    ...mapActions('timeline', [
      'setCursorPosition',
    ]),
    onClick(e) {
      this.setCursorPosition({
        cursorPosition: e.offsetX,
        inPx: true,
        manual: true,
      });
    },
    setCoordinates() {
      this.setElementCoordinates({
        elem: this.$refs.rulerContainer,
        name: 'timelineRulerContainer',
      });
    },
    setDomListeners() {
      addMultiEventListener(['resize', 'scroll'], window, this.setCoordinates);
    },
    removeDomListeners() {
      removeMultiEventListener(['resize', 'scroll'], window, this.setCoordinates);
    },
  },
};
</script>
