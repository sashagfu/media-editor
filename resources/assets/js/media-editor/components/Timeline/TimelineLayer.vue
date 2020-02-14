<template>
  <div
    ref="tlLayer"
    :class="{'me-tl-layer--height--high' : height === 2}"
    class="me-tl-layer"
  >
    <droppable
      ref="droppable"
      :style="droppableStyle"
      :tolerance="draggingDroppableTolerance"
      :disbled="isCanDrop"
      class="me-tl-layer__droppable"
      id-attr-name="item-id"
      uuid-attr-name="item-uuid"
      @drop="onDrop"
      @over="onDraggingDroppableOver"
      @out="onDraggingDroppableOut"
      @click.native="onHandleClick"
    />
    <timeline-layer-item
      v-for="item in items"
      :key="item.id"
      :item="item"
    />
    <timeline-layer-cursor-split-handler
      :layer-id="id"
    />
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import Droppable from '../common/Droppable';
import TimelineLayerItem from './TimelineLayerItem';
import TimelineLayerCursorSplitHandler from './TimelineLayerCursorSplitHandler';
// import { ITEM_THUMB } from '../../config/settings';

export default {
  name: 'TimelineLayer',
  components: {
    Droppable,
    TimelineLayerItem,
    TimelineLayerCursorSplitHandler,
  },
  props: {
    id: {
      type: [Number, String],
      default: 0,
    },
    height: {
      type: Number,
      default: 0,
    },
    items: {
      type: Array,
      default: () => [],
    },
  },
  computed: {
    ...mapGetters('timeline', [
      'zoomedPxPerSec',
      'scrollH',
    ]),
    ...mapGetters('dragging', [
      'draggingDroppableTolerance',
      'draggingOverLayerId',
      'draggingTimelineItem',
      'isCanDrop',
      'isDraggingTimelineItem',
      'maxDraggingInsertLength',
    ]),
    ...mapGetters('coordinates', [
      'timelineLayersContainer',
    ]),
    ...mapGetters('player', [
      'playing',
    ]),
    droppableStyle() {
      return {
        width: `${this.timelineLayersContainer.width}px`,
        left: `${this.scrollH}px`,
      };
    },
    isCanDrop() {
      return !this.playing;
    },
  },
  methods: {
    ...mapActions('project', [
      'addItem',
      'changeItem',
    ]),
    ...mapActions('timeline', [
      'setSelectedItems',
    ]),
    ...mapActions('dragging', [
      'setDraggingOverLayerId',
      'resetDragging',
    ]),
    ...mapActions('coordinates', [
      'setElementCoordinates',
    ]),
    onHandleClick(e) {
      if (!e.ctrlKey && !e.shiftKey && !e.metaKey) {
        this.setSelectedItems([]);
      }
    },
    onDrop(params) {
      if (this.isCanDrop) {
        if (this.draggingTimelineItem.id) {
          this.changeItem({
            layerId: this.id,
            id: this.draggingTimelineItem.id,
            position: params.position.left / this.zoomedPxPerSec,
          });
        } else {
          this.addItem({
            layerId: this.id,
            itemId: params.id,
            itemUuid: params.uuid,
            position: (params.innerPosition.left + this.scrollH) / this.zoomedPxPerSec,
            maxLength: this.maxDraggingInsertLength,
          });
        }
      }
    },
    onDraggingDroppableOver() {
      this.setDraggingOverLayerId(this.id);
      // Set droppable coordinates to snap dragging to layer
      this.setElementCoordinates({
        elem: this.$refs.droppable.$el,
        name: 'activeDroppable',
      });
    },
    onDraggingDroppableOut() {
      if (this.draggingOverLayerId === this.id) {
        this.setDraggingOverLayerId(null);
      }
    },
  },
};
</script>
