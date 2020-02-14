<template>
  <div
    ref="previewProject"
    class="PreviewProject"
  >
    <canvas
      ref="canvas"
      :width="canvasSize.width"
      :height="canvasSize.height"
    />
  </div>
</template>

<script>
import { fabric } from 'fabric';
import { mapGetters, mapActions } from 'vuex';
import * as fileTypes from '../../config/file-types';
import PreviewMediaElement from './PreviewMediaElement';
import PreviewTextElement from './PreviewTextElement';
import { CANVAS_SIZE, PX_PER_SEC, ITEM_THUMB } from '../../config/settings';

export default {
  name: 'PreviewProject',
  data() {
    return {
      elements: [],
      projectUpdated: false,
      lastCanvasDrawed: null,
      canvasSize: CANVAS_SIZE,
      allElements: [],
    };
  },
  computed: {
    ...mapGetters('timeline', [
      'itemsUnderCursor',
      'cursorPosition',
      'cursorMovedManual',
    ]),
    ...mapGetters('preview/project', [
      'canPlay',
    ]),
    ...mapGetters('player', [
      'playing',
    ]),
    ...mapGetters('project', {
      projectItems: 'items',
      layers: 'layers',
    }),
    canvas() {
      return this.$refs.canvas;
    },
    ctx() {
      return this.canvas.getContext('2d');
    },
    fabricCanvas() {
      return new fabric.Canvas(this.canvas, { centeredRotation: false });
    },
    playableElements() {
      return this.elements.filter(el => el.playable);
    },
    isRunning() {
      return this.playing && this.canPlay;
    },

  },
  watch: {
    cursorPosition() {
      if (this.cursorMovedManual) {
        this.play();
      }
    },
    canPlay() {
      this.play();
    },
    playing() {
      this.play();
    },
    projectItems() {
      this.projectUpdated = true;
      this.createAllMediaElements();
      this.play();
    },
    layers() {
      this.setVolumeForAllPlayableElements();
    },
  },
  mounted() {
    this.createAllMediaElements();
    this.loadMediaElements();
    this.setElementCoordinates({
      elem: this.$refs.previewProject,
      name: 'previewProjectContainer',
    });
  },
  methods: {
    ...mapActions('preview/project', [
      'setCanPlay',
    ]),
    ...mapActions('timeline', [
      'setCursorPosition',
    ]),
    ...mapActions('coordinates', [
      'setElementCoordinates',
    ]),
    ...mapActions('player', [
      'togglePlaying',
      'setCurrentTime',
    ]),
    createAllMediaElements() {
      this.allElements = this.projectItems.map((item) => {
        const el = this.getNewPreviewElement(item);

        return el;
      });
    },
    /**
     * Manage media elements, check if need create or remove
     */
    sortElementsByTimeline(el) {
      return el.sort((a, b) => {
        const elA = this.getItemUnderCursorById(a.id);
        const elB = this.getItemUnderCursorById(b.id);
        return elB.layerId - elA.layerId;
      });
    },
    loadMediaElements() {
      // If No any elements, do nothing
      if (!(this.elements.length || this.itemsUnderCursor.length)) return;
      // Create array of created PreviewMediaElements
      const newElements = this.itemsUnderCursor.map(item => this.getPreviewElement(item));
      this.setCursorPosition({ cursorPosition: this.cursorPosition });

      // If there are created element, remove which are not under cursor
      if (this.elements.length) {
        this.clearElements(newElements);
      }
      // Save array with new and filtered old ME to context
      this.elements = newElements;
      // Is need to wait for load ME
      this.setWaiting();
    },
    /**
     * Prepare PreviewMediaElement by item under cursor
     * @param item
     * @returns {T}
     */
    getPreviewElement(item) {
      // try to get element from created element
      const el = this.getElementFromCreatedById(item.id);

      if (el.playable && (this.cursorMovedManual || this.projectUpdated)) {
        this.setCurrentTimeForPlayableElement(el);
      }
      this.setVolumeForPlayableElement(el);
      // Return prepared element
      return el;
    },
    /**
     * Get item Under Cursor by id
     * @param id
     */
    getItemUnderCursorById(id) {
      return this.itemsUnderCursor.find(item => item.id === id);
    },
    /**
     * Get element by id from array of created elements
     * @param itemId
     * @returns {T}
     */
    getElementFromCreatedById(itemId) {
      return this.allElements.find(el => el.id === itemId);
    },
    /**
     * Create and return new PreviewMediaElement
     * @param item
     * @returns {PreviewMediaElement}
     */
    getNewPreviewElement(item) {
      if (item.file.fileType === fileTypes.SLIDE) {
        return new PreviewTextElement({
          file: item.file,
          id: item.id,
          setCanPlay: this.setIsCanPlay,
          item,
          canvas: this.fabricCanvas,
        });
      }
      return new PreviewMediaElement({
        file: item.file,
        id: item.id,
        setCanPlay: this.setIsCanPlay,
        item,
        canvas: this.fabricCanvas,
      });
    },
    /**
     * Get current time for item base on cursor position
     * @param itemId
     * @returns {number}
     */
    getCurrentTimeForItem(itemId) {
      const item = this.getItemUnderCursorById(itemId);
      return ((this.cursorPosition - item.position) + item.startFrom) / 1000;
    },
    getVolumeForTimelineLayer(layerId) {
      const layer = this.layers.find(lay => lay.id === layerId);
      if (layer) {
        return layer.volume;
      }
      return 0;
    },
    /**
     * Check if all element ready to play and set to store result
     *
     * @param val
     * @private
     */
    setIsCanPlay(val = true) {
      let canPlay = val;
      if (canPlay) {
        canPlay = !this.elements.some(el => !el.ready);
      }
      // Need to redraw canvas after media elements waiting
      this.lastCanvasDrawed = canPlay ? this.lastCanvasDrawed : null;
      this.setCanPlay({ canPlay });
    },
    setCurrentTimeForPlayableElement(el) {
      Object.assign(el, { currentTime: this.getCurrentTimeForItem(el.id) });
    },
    sortPoints(volumeControl) {
      volumeControl.sort((a, b) => {
        if (a.length > b.length) {
          return 1;
        }
        return -1;
      });
      return volumeControl;
    },
    getVolume(b, a, b1) {
      // a/b = a1/b1
      // a/b = x/b1 -> x = a * b1 / b
      return (a * b1) / b;
    },
    getVolumeForItem(el) {
      const { height } = ITEM_THUMB;
      const pxPerSec = PX_PER_SEC;
      let volumeControl = el.item.volumeControl.concat();
      volumeControl = this.sortPoints(volumeControl);
      const { position, startFrom } = this.getItemUnderCursorById(el.id);
      const currentPos = ((this.cursorPosition - position) + startFrom);
      const idx = volumeControl.findIndex((item, index) => {
        if (index + 1 === volumeControl.length) {
          return true;
        }
        if (item.length < currentPos && volumeControl[index + 1].length >= currentPos) {
          return true;
        }
        return false;
      });
      // If end of audio item
      if (idx === volumeControl.length - 1) {
        return volumeControl[idx].level;
      }
      // If volume volume increases
      if (volumeControl[idx + 1].level > volumeControl[idx].level) {
        const dV = this.getVolume(
          (volumeControl[idx + 1].length - volumeControl[idx].length) * pxPerSec,
          (volumeControl[idx + 1].level - volumeControl[idx].level) * height,
          (currentPos - volumeControl[idx].length) * pxPerSec,
        );
        return (dV / height) + volumeControl[idx].level;
        // If volume volume decreases
      } else if (volumeControl[idx].level > volumeControl[idx + 1].level) {
        const dX = volumeControl[idx + 1].length - volumeControl[idx].length;
        const dV = this.getVolume(
          dX * pxPerSec,
          (volumeControl[idx].level - volumeControl[idx + 1].level) * height,
          (dX - (currentPos - volumeControl[idx].length)) * pxPerSec,
        );
        return (dV / height) + volumeControl[idx + 1].level;
      }
      // volume remains at the same level
      return volumeControl[idx].level;
    },
    /**
     * Set volume for playable PreviewMediaElement
     * Volume parameter get from timeline layers settings
     * and compute from volumeControl
     *
     * @param el
     */
    setVolumeForPlayableElement(el) {
      const { layerId, unlinked, file } = this.getItemUnderCursorById(el.id);
      const volume = this.getVolumeForItem(el) * this.getVolumeForTimelineLayer(layerId);
      Object.assign(el, {
        volume: (unlinked && file.fileType === fileTypes.VIDEO)
          ? 0
          : (volume || this.getVolumeForTimelineLayer(layerId)),
      });
    },
    setVolumeForAllPlayableElements() {
      this.playableElements.forEach(el => this.setVolumeForPlayableElement(el));
    },
    /**
     * Wait for other PreviewMediaElement if needed
     * set pause for all PME if some PME still loading
     */
    setWaiting() {
      const { playableElements, canPlay, playing } = this;
      // If preview playing and canPlay
      if (playing && canPlay) {
        // set playing for each me
        playableElements.forEach((el) => {
          el.play();
        });
      } else {
        // Otherwise set pause
        playableElements.forEach((el) => {
          el.pause();
        });
      }
    },
    /**
     * Clear elements which doesn't under cursor
     * @param newElements
     */
    clearElements(newElements) {
      this.elements.forEach((el) => {
        // If this el not in new el array
        if (newElements.indexOf(el) === -1) {
          // pass to remove
          el.hide();
          this.fabricCanvas.renderAll();
        }
      });
    },
    /**
     * Draw canvas
     */
    drawOnCanvas() {
      const { cursorPosition, canPlay } = this;
      const elements = this.sortElementsByTimeline(this.elements);
      // If cursor didn't move from last canvas drawing,
      // or project didn't update
      // do nothing
      // if (this.lastCanvasDrawed === cursorPosition && !this.projectUpdated) return;
      // if (this._canvasDrawnOnTime === cursorPosition && !this._projectWasUpdated) return;

      // Clear canvas
      // this._setBlankToCanvas();
      // If  there is at least one ME
      if (elements.length) {
        // If canPlay
        if (canPlay) {
          // For each ME
          this.lastCanvasDrawed = cursorPosition;
          elements.forEach((el) => {
            if (!el.drawable) return;
            // Item object can be changed because of immutable state
            // me.setItem(this._getItemUnderCursorById(me.id));
            // Set new transform to element
            el.setElementChanges();
          });
          this.fabricCanvas.renderAll();
        }
      } else {
        // Save cursor position when canvas draw to avoid redraw the same picture
        this.lastCanvasDrawed = cursorPosition;
      }

      this.projectUpdated = false;
    },
    /**
     * Set timeline cursor by first playable element currentTime
     */
    moveCursorByPlayableElement() {
      // If there no any ME then do nothing
      // (cursor moving by PreviewController)
      if (!this.playableElements.length) return;

      const { cursorPosition } = this; // { cursorPosition, cursorMovedManual }
      const el = this.playableElements[0];
      const item = this.getItemUnderCursorById(el.id);

      const newCursorPosition = (item.position + (el.currentTime * 1000)) - item.startFrom;

      // If newCursor position move cursor to new position
      if (newCursorPosition > cursorPosition) {
        // const {setCursorPosition, setScrollHor, scrollHorToCursorCenter} = this.props;
        this.setCursorPosition({ cursorPosition: newCursorPosition });
        // Scroll to make cursor center of window if possible
        //    setScrollHor(scrollHorToCursorCenter);
        // Otherwise move cursor only if it will moved manual
      }
      // else if (cursorMovedManual) {
      //     this.setCursorPosition({ cursorPosition });
      // }
    },
    /**
     * Set timeline cursor position by passed time from last RAF call
     */
    moveCursorByTimestamp(timestamp) {
      const { lastEmulateCursorMovingTime, cursorPosition } = this;
      if (lastEmulateCursorMovingTime) {
        this.setCursorPosition({
          cursorPosition: cursorPosition + (timestamp - lastEmulateCursorMovingTime),
        });
      }
      this.lastEmulateCursorMovingTime = timestamp;
    },
    /**
     * Move cursor
     * @param timestamp - requestAnimationFrame argument
     */
    moveCursor(timestamp) {
      // If there is at least one playableElement under cursor
      if (this.playableElements.length) {
        // Move cursor by playable element
        this.moveCursorByPlayableElement();
        // Reset last time
        this.lastEmulateCursorMovingTime = null;

        // If there no items after cursor
      } else if (!this.isLastItem()) {
        // console.log(this.isLastItem());
        // Move cursor by timestamp
        this.moveCursorByTimestamp(timestamp);
      } else {
        this.togglePlaying();
        // Otherwise stop playing
        this.setCursorPosition({ cursorPosition: 1000 });
        this.togglePlaying();
      }
    },
    play(timestamp) {
      this.loadMediaElements();
      this.drawOnCanvas();

      if (this.isRunning) {
        this.moveCursor(timestamp);
        // recursion
        if (!(this.rafId && !timestamp)) {
          this.rafId = window.requestAnimationFrame(this.play);
        }
      } else {
        // Reset helping RAF variables
        this.rafId = null;
        this.lastEmulateCursorMovingTime = null;
      }
    },
    isLastItem() {
      const itemsAfterCursor = this.projectItems
        .filter(item => item.position > this.cursorPosition);
      return !itemsAfterCursor.length;
    },
  },
};
</script>

