<template>
  <div
    class="Timeline"
    @click="resetPreviewGalleryItem"
  >
    <div class="me-tl">
      <!--ref={ref => this._timelineContainer = ref}-->
      <div class="me-tl__header">
        <timeline-header-controls/>
        <timeline-ruler/>
      </div>
      <div class="me-tl__body">
        <div class="me-tl__body-controls">
          <timeline-layer-controls
            v-for="layer in layers"
            :key="layer.id"
            v-bind="layer"
          />
        </div>
        <div
          class="me-tl__layers"
          @mouseover="setIsMouseOverLayers(true)"
          @mouseout="setIsMouseOverLayers(false)"
        >
          <timeline-layers-container/>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
// TODO check this error & what is this debounce function for
// import _ from 'lodash';
import { mapGetters, mapActions } from 'vuex';

import TimelineHeaderControls from './TimelineHeaderControls';
import TimelineLayersContainer from './TimelineLayersContainer';
import TimelineLayerControls from './TimelineLayerControls';
import TimelineRuler from './TimelineRuler';
import {
  addMultiEventListener,
  removeMultiEventListener,
} from '../../utils/helpers';

export default {
  name: 'Timeline',
  components: {
    TimelineHeaderControls,
    TimelineLayersContainer,
    TimelineLayerControls,
    TimelineRuler,
  },
  computed: {
    ...mapGetters('timeline', [
      'selectedItems',
      'isSplitTool',
    ]),
    ...mapGetters('project', [
      'layers',
    ]),
  },
  watch: {
    isSplitTool(isSplitTool) {
      if (isSplitTool) {
        addMultiEventListener(['click'], window, this.toggleSplitTool);
      } else {
        removeMultiEventListener(['click'], window, this.toggleSplitTool);
      }
    },
  },
  mounted() {
    this.setDomListeners();
  },
  beforeDestroy() {
    this.removeDomListeners();
  },
  methods: {
    ...mapActions('timeline', [
      'setSelectedItems',
      'toggleSplitTool',
    ]),
    ...mapActions('preview', {
      resetPreviewGalleryItem: 'resetGalleryItemId',
    }),
    ...mapActions('project', [
      'removeItems',
    ]),
    ...mapActions('project/undoRedo', [
      'undo',
      'redo',
    ]),
    ...mapActions('dragging', [
      'setIsMouseOverTimelineLayersContainer',
    ]),
    onWindowKeyUp(e) {
      switch (e.code) {
        case 'Backspace':
        case 'Delete': {
          // Delete items
          if (!this.show.settings) {
            this.deleteSelectedItems();
          }
          break;
        }
        case 'KeyZ':
          if (e.ctrlKey) {
            if (e.shiftKey) {
              this.redo();
            } else {
              this.undo();
            }
          }
          break;
        default: break;
      }
    },
    deleteSelectedItems() {
      if (this.selectedItems.length !== 0) {
        this.removeItems(this.selectedItems);
        this.setSelectedItems([]);
      }
    },
    setDomListeners() {
      addMultiEventListener(['keyup'], window, this.onWindowKeyUp);
    },
    removeDomListeners() {
      removeMultiEventListener(['keyup'], window, this.onWindowKeyUp);
    },
    // TODO check this error & what is this debounce function for
    // setIsMouseOverLayers: _.debounce((isOver) => {
    //     this.setIsMouseOverTimelineLayersContainer(isOver);
    // }, 0),
    setIsMouseOverLayers(isOver) {
      this.setIsMouseOverTimelineLayersContainer(isOver);
    },
  },
};
</script>

