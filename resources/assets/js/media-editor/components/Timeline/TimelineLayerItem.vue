<template>
  <resizable
    :class="{'selected' : isSelected, 'dragging' : isDragging }"
    :style="style"
    :max-width="maxResizingWidth"
    :max-height="maxResizingHeight"
    :disabled="!isCanMove"
    class="TimelineLayerItem me-tl-layer-item"
    handles="w,e"
    @click.native="onClick"
    @resizestart="onResizeStart"
    @resizing="onResizing"
    @resizestop="onResizeStop"
  >
    <draggable
      :item-id="item.id"
      :item-uuid="item.uuid"
      :success="isCanDrop"
      :error="!isCanDrop"
      :disabled="!isCanMove"
      containment=".me-tl__layers-container"
      append-to=".me-tl__layers-container"
      @dragstop="onDragStop"
      @dragging="onDragging"
      @dragstart="onDragStart"
    >
      <div
        :class="[
          'me-tl-layer-item__thumb',
          {'me-tl-layer-item__thumb--split' : isSplitTool}
        ]"
      >
        <item-thumb-wrapper
          v-bind="item"
        />
      </div>
    </draggable>
  </resizable>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import Draggable from '../common/Draggable';
import Resizable from '../common/Resizable';
import ItemThumbWrapper from '../Thumbs/ItemThumbWrapper';
import * as fileTypes from '../../config/file-types';

export default {
  name: 'TimelineLayerItem',
  components: {
    ItemThumbWrapper,
    Draggable,
    Resizable,
  },
  props: {
    item: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      resizeDirection: null,
    };
  },
  computed: {
    ...mapGetters('timeline', [
      'zoomedPxPerSec',
      'selectedItems',
      'isSplitTool',
    ]),
    ...mapGetters('project', [
      'items',
    ]),
    ...mapGetters('settings', [
      'itemThumb',
    ]),
    ...mapGetters('dragging', [
      'draggingTimelineItem',
      'isCanDrop',
    ]),
    ...mapGetters('player', [
      'playing',
    ]),
    ...mapGetters('coordinates', {
      droppableCoord: 'activeDroppable',
      timelineLayersContainerCoord: 'timelineLayersContainer',
    }),
    isSelected() {
      return this.selectedItems.indexOf(this.item.id) !== -1;
    },
    isDragging() {
      return this.draggingTimelineItem.id === this.item.id;
    },
    isCanMove() {
      return !this.playing;
    },
    width() {
      return this.item.length * this.zoomedPxPerSec;
    },
    position() {
      return this.item.position * this.zoomedPxPerSec;
    },
    /**
         * Max resizing time depend on resizing size, timeline time or available space
         */
    maxResizingTime() {
      const { item } = this;
      const imageDefDuration = this.itemThumb.imageDefaultDuration;
      if (item.file.time || imageDefDuration) {
        const spaceAvailable = this.getSpaceOnTimelineForResizing();
        switch (this.resizeDirection) {
          case 'w': {
            return item.length + Math.min(spaceAvailable, item.startFrom);
          }
          case 'e': {
            let itemCutRight = Number.MAX_SAFE_INTEGER;
            if (item.file.fileType !== fileTypes.SLIDE) {
              itemCutRight = item.file.time - (item.startFrom + item.length);
            }
            return item.length + Math.min(spaceAvailable, itemCutRight);
          }
          default: return (item.file.time || imageDefDuration);
        }
      }
      return imageDefDuration;
    },
    /**
         * Convert maxResizingTime to pixels for dragging element
         */
    maxResizingWidth() {
      if (this.maxResizingTime !== undefined) {
        return this.maxResizingTime * this.zoomedPxPerSec;
      }
      return Number.MAX_SAFE_INTEGER;
    },
    maxResizingHeight() {
      return this.itemThumb.timelineLayerItemHeight;
    },
    style() {
      return {
        width: `${this.width}px`,
        left: `${this.position}px`,
        position: 'absolute',
      };
    },
  },
  methods: {
    ...mapActions('project', [
      'changeItem',
      'splitItemByPx',
    ]),
    ...mapActions('timeline', [
      'setSelectedItems',
      'toggleSelectedItem',
    ]),
    ...mapActions('dragging', [
      'resetDragging',
      'setDraggingTimelineItem',
      'setDraggingPosition',
      'isDraggingTimelineItem',
    ]),
    onClick(e) {
      if (!this.isSplitTool) {
        if (e.ctrlKey || e.shiftKey || e.metaKey) {
          this.toggleSelectedItem(this.item.id);
        } else {
          this.setSelectedItems([this.item.id]);
        }
      } else {
        e.stopPropagation();
        this.splitItemByPx({
          item: this.item,
          splitPositionPx: e.offsetX,
        });
      }
    },
    onDragStart() {
      this.setDraggingTimelineItem(this.item);
      // If item is not selected
      if (!this.isSelected) {
        // Select it and deselect rest
        this.setSelectedItems([this.item.id]);
      }
    },
    onDragging({ position }) {
      const { top: containerTop } = this.timelineLayersContainerCoord;
      const { top: dropTop } = this.droppableCoord;
      Object.assign(position, {
        left: position.left > 0 ? position.left : 0,
        top: dropTop - containerTop,
      });
      this.setDraggingPosition(position);
    },
    onDragStop() {
      this.resetDragging();
    },
    onResizeStart({ axis }) {
      this.resizeDirection = axis;
    },
    onResizing({ size, position }) {
      const newLength = size.width / this.zoomedPxPerSec;
      const newPosition = position.left / this.zoomedPxPerSec;
      const newStartFrom = (newPosition - this.item.position) + this.item.startFrom;

      if (newStartFrom < 0 || newPosition < 0 || newLength < 0) return;

      this.changeItem({
        id: this.item.id,
        uuid: this.item.uuid,
        position: newPosition,
        length: newLength,
        startFrom: newStartFrom,
      });
    },
    onResizeStop() {
      this.resizeDirection = null;
    },
    /**
         * Get next item on resizing depend on resizing size
         * @returns {*}
         */
    getNextItemOnResizingSide() {
      if (!this.resizeDirection) return null;
      let nextItem = null;
      this.items.forEach((item) => {
        if (
          this.item.id !== item.id
                    && parseInt(this.item.layerId, 10) === parseInt(item.layerId, 10)
        ) {
          switch (this.resizeDirection) {
            case 'w':
              if (
                item.position < this.item.position
                                && (!nextItem || item.position > nextItem.position)
              ) {
                nextItem = item;
              }
              break;
            case 'e':
              if (
                item.position > this.item.position
                                && (!nextItem || item.position < nextItem.position)
              ) {
                nextItem = item;
              }
              break;
            default: break;
          }
        }
      });
      return nextItem;
    },
    /**
         * Get available space on timeline to next item or left edge
         * on resizing depend on resizing side
         * @returns {*}
         */
    getSpaceOnTimelineForResizing() {
      if (!this.resizeDirection) return null;
      const nextItem = this.getNextItemOnResizingSide();
      switch (this.resizeDirection) {
        case 'w':
          return this.item.position - (
            nextItem ? (nextItem.position + nextItem.length) : 0
          );
        case 'e':
          if (nextItem) {
            return nextItem.position - (
              this.item.position + this.item.length
            );
          }
          return Number.MAX_SAFE_INTEGER;
        default: return Number.MAX_SAFE_INTEGER;
      }
    },
  },
};
</script>
