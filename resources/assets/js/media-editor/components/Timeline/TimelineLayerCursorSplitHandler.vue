<template>
  <div
    v-if="isShowHandler"
    :style="{ left: `${cursorPositionPx}px` }"
    class="TimelineLayerCursorSplitHandler me-tl-layer-cursor-split-handler"
    @click.stop="onClick"
  />
</template>

<script>
import { mapGetters, mapActions } from 'vuex';

export default {
  name: 'TimelineLayerCursorSplitHandler',
  props: {
    layerId: {
      type: [Number, String],
      required: true,
    },
  },
  computed: {
    ...mapGetters('timeline', [
      'itemsUnderCursor',
      'isShowCursor',
      'isSplitTool',
      'cursorPosition',
      'cursorPositionPx',
    ]),
    layerItemsUnderCursor() {
      return this.itemsUnderCursor.filter(item => item.layerId === this.layerId);
    },
    isItemsUnderCursor() {
      return this.layerItemsUnderCursor.length;
    },
    isShowHandler() {
      return this.isItemsUnderCursor && this.isSplitTool;
    },
  },
  methods: {
    ...mapActions('project', [
      'splitItem',
    ]),
    onClick() {
      this.layerItemsUnderCursor.forEach((item) => {
        const splitPosition = this.cursorPosition - item.position;
        this.splitItem({ item, splitPosition });
      });
    },
  },
};
</script>
