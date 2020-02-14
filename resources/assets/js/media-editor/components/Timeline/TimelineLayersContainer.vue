<template>
  <div
    ref="layersContainer"
    class="TimelineLayersContainer me-tl__layers-frame"
    @scroll="onTimelineScroll"
  >
    <div
      :style="{width: `${timelineLayerWidth}px`}"
      class="me-tl__layers-container"
    >
      <timeline-layer
        v-for="layer in layers"
        :key="layer.id"
        :id="layer.id"
        :height="layer.height"
        :items="getLayerItems(layer.id)"
      />
    </div>
    <timeline-max-length-line/>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import TimelineLayer from './TimelineLayer';
import TimelineMaxLengthLine from './TimelineMaxLengthLine';
import {
  addMultiEventListener,
  removeMultiEventListener,
} from '../../utils/helpers';

export default {
  name: 'TimelineLayersContainer',
  components: {
    TimelineLayer,
    TimelineMaxLengthLine,
  },
  computed: {
    ...mapGetters('project', [
      'items',
      'layers',
      'pendingProjectValue',
      'projectData',
    ]),
    ...mapGetters('timeline', [
      'scrollH',
      'timelineLayerWidth',
    ]),
    ...mapGetters('player', [
      'playing',
    ]),
  },
  watch: {
    scrollH(scrollH) {
      this.setScroll(scrollH);
    },
    playing(value) {
      // Update timeline items after stop playing
      if (!value && this.pendingProjectValue.length) {
        const { projectData } = this;
        this.setProjectData({
          ...projectData,
          value: this.pendingProjectValue,
        });
        // Clear pending project value
        this.setPendingProjectValue({});
      }
    },
  },
  mounted() {
    this.setCoordinates();
    this.setDomListeners();
  },
  beforeDestroy() {
    this.removeDomListeners();
  },
  methods: {
    ...mapActions('project', [
      'setProjectData',
      'setPendingProjectValue',
    ]),
    ...mapActions('timeline', [
      'setScrollH',
    ]),
    ...mapActions('coordinates', [
      'setElementCoordinates',
    ]),
    onTimelineScroll(e) {
      this.setScrollH(e.target.scrollLeft);
    },
    getLayerItems(layerId) {
      return this.items.filter((item) => {
        const itemLayerId = parseInt(item.layerId, 10);
        return itemLayerId === parseInt(layerId, 10);
      });
    },
    setScroll(scrollH = this.scrollH) {
      this.$refs.layersContainer.scrollLeft = scrollH;
    },
    setCoordinates() {
      this.setElementCoordinates({
        elem: this.$refs.layersContainer,
        name: 'timelineLayersContainer',
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
