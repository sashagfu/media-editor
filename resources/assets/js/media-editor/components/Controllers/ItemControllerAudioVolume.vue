<template>
  <div
    class="ItemControllerAudioVolume ctrl-audio"
  >
    <div
      v-if="showVolumeControl"
      class="ctrl-audio__canvas"
      @mousedown.stop=""
    >
      <canvas
        ref="canvas"
        :width="width"
        :height="height"
      />
    </div>
    <div
      v-else
      class="ctrl-audio__canvas"
    >
      <canvas
        ref="canvas"
        :width="width"
        :height="height"
      />
    </div>
    <div
      v-if="!showVolumeControl"
      :style="{'height': `${height}px`}"
      :class="[
        'ctrl-audio__cursor',
        {'ctrl-audio__cursor--split': isSplitTool}
      ]"
    />
  </div>
</template>

<script>
import { has, isNull, debounce } from 'lodash';
import uuid from 'uuid';
import { mapGetters, mapActions } from 'vuex';
import { fabric } from 'fabric';
import { ITEM_THUMB } from '../../config/settings';

export default {
  name: 'ItemControllerAudioVolume',
  props: {
    file: {
      type: Object,
      default: () => ({}),
    },
    length: {
      type: Number,
      validator: val => val >= 0,
      default: 0,
    },
    startFrom: {
      type: Number,
      validator: val => val >= 0,
      default: 0,
    },
    id: {
      type: [Number, String],
      default: 0,
    },
    uuid: {
      type: String,
      default: '',
    },
    showVolumeControl: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      height: ITEM_THUMB.height,
      volumeControl: [{
        id: '0',
        length: 0,
        level: 1,
      }, {
        id: '1',
        length: 0,
        level: 1,
      }],
    };
  },
  computed: {
    ...mapGetters('settings', [
      'itemThumb',
    ]),
    ...mapGetters('timeline', [
      'zoomedPxPerSec',
      'isSplitTool',
    ]),
    ...mapGetters('project', [
      'items',
    ]),
    item() {
      const index = this.items.findIndex(i => i.id === this.id);
      return this.items[index];
    },
    width() {
      return (((this.length || this.file.time) * this.zoomedPxPerSec))
                - (ITEM_THUMB.boxBorder * 2);
    },
    canvas() {
      return this.$refs.canvas;
    },
    fabricCanvas() {
      const fabricCanvas = new fabric.Canvas(this.canvas, { selection: false });
      fabricCanvas.on('object:moving', this.movingPoint);
      fabricCanvas.on('mouse:down', this.addPoint);
      fabricCanvas.on('mouse:dblclick', this.deletePoint);
      return fabricCanvas;
    },
  },
  watch: {
    showVolumeControl() {
      this.fabricCanvas.clear();
      this.generateLines();
    },
    width(newWidth) {
      this.volumeControl[this.getLastIndex()].length = this.getTime(newWidth);
      this.saveToStore();
      this.fabricCanvas.clear();
      this.canvas.width = newWidth;
      this.fabricCanvas.setWidth(newWidth);
      this.generateLines();
    },
    startFrom(newStart) {
      this.volumeControl[0].length = newStart;
    },
  },
  mounted() {
    if (has(this.item, 'volumeControl')) {
      this.volumeControl = this.item.volumeControl.concat();
    }
    this.volumeControl[this.getLastIndex()].length = this.getTime(this.width);
    // Removed for performance issue

    // let idx = -1;
    // if (this.items.every(itm => has(itm, 'uuid'))) {
    //   idx = this.items.findIndex(itm => itm.uuid === this.uuid);
    // } else {
    //   idx = this.items.findIndex(itm => itm.id === this.id);
    // }
    // if (idx !== -1) {
    //   const volumeControl = this.volumeControl.concat();
    // const item = Object.assign({}, this.items[idx], { volumeControl });
    // this.changeItem(item);
    // }
    this.generateLines();
  },
  methods: {
    ...mapActions('project', [
      'changeItem',
    ]),
    movingPoint(e) {
      if (!this.showVolumeControl) {
        return;
      }
      const p = e.target;
      p.setCoords();
      // limitation
      if (p.getBoundingRect().top < 0 || p.getBoundingRect().left < 0) {
        p.top = Math.max(p.top, p.top - p.getBoundingRect().top);
        p.left = Math.max(p.left, p.left - p.getBoundingRect().left);
      }
      if (p.getBoundingRect().top + p.getBoundingRect().height > p.canvas.height
                || p.getBoundingRect().left + p.getBoundingRect().width > p.canvas.width) {
        p.top = Math.min(p.top, ((p.canvas.height - p.getBoundingRect().height)
                    + p.top) - p.getBoundingRect().top);
        p.left = Math.min(p.left, ((p.canvas.width - p.getBoundingRect().width)
                    + p.left) - p.getBoundingRect().left);
      }
      // moving lines
      if (p.line1) {
        p.line1.set({
          x2: p.left + 5,
          y2: p.top + 5,
        });
        p.line1.setCoords();
      }
      if (p.line2) {
        p.line2.set({
          x1: p.left + 5,
          y1: p.top + 5,
        });
        p.line2.setCoords();
      }

      if (p.type === 'circle') {
        // set time in volume array
        const index = this.volumeControl.findIndex(item => item.id === p.id);
        this.volumeControl[index].length = this.getTime(p.left);
        this.volumeControl[index].level = this.getVolume(p.top);
        this.saveToStore();
        // replacement of lines if they overlap
        if (index !== 0 && this.volumeControl[index].id !== '1') {
          if (this.volumeControl[index - 1].length > this.volumeControl[index].length) {
            const points = this.fabricCanvas.getObjects('circle');
            const index1 = points.findIndex(i =>
              i.id === this.volumeControl[index - 1].id);
            const index2 = points.findIndex(i =>
              i.id === this.volumeControl[index].id);
            const { line1, line2 } = points[index1];
            points[index1].line1 = points[index2].line1;
            points[index1].line1.set({
              x2: points[index1].left + 5,
              y2: points[index1].top + 5,
            });
            points[index1].line2 = points[index2].line2;
            points[index1].line2.set({
              x1: points[index1].left + 5,
              y1: points[index1].top + 5,
            });
            points[index2].line1 = line1;
            points[index2].line2 = line2;
            this.fabricCanvas.bringToFront(points[index1]);
            this.fabricCanvas.bringToFront(points[index2]);
            this.sortPoints();
            this.saveToStore();
          }
          if (this.volumeControl[index + 1].length < this.volumeControl[index].length) {
            const points = this.fabricCanvas.getObjects('circle');
            const index1 = points.findIndex(i =>
              i.id === this.volumeControl[index + 1].id);
            const index2 = points.findIndex(i =>
              i.id === this.volumeControl[index].id);
            const { line1, line2 } = points[index1];
            points[index1].line1 = points[index2].line1;
            points[index1].line1.set({
              x2: points[index1].left + 5,
              y2: points[index1].top + 5,
            });
            points[index1].line2 = points[index2].line2;
            points[index1].line2.set({
              x1: points[index1].left + 5,
              y1: points[index1].top + 5,
            });
            points[index2].line1 = line1;
            points[index2].line2 = line2;
            this.fabricCanvas.bringToFront(points[index1]);
            this.fabricCanvas.bringToFront(points[index2]);
            this.sortPoints();
            this.saveToStore();
          }
        }
      }
      this.fabricCanvas.renderAll();
    },
    sortPoints() {
      this.volumeControl.sort((a, b) => {
        if (a.length > b.length) {
          return 1;
        }
        return -1;
      });
    },
    addPoint(event) {
      if (!this.showVolumeControl) {
        return;
      }
      const p = event.target;
      const { e } = event;
      if (!isNull(p) && p.type === 'line') {
        this.volumeControl.push({
          id: uuid(),
          length: this.getTime(e.layerX - 5),
          level: this.getVolume(e.layerY - 5),
        });
        this.sortPoints();
        this.saveToStore();
        this.fabricCanvas.clear();
        this.generateLines();
      }
    },
    deletePoint(event) {
      if (!this.showVolumeControl) {
        return;
      }
      const p = event.target;
      if (!isNull(p) && p.type === 'circle' && p.id !== '0' && p.id !== '1') {
        this.volumeControl = this.volumeControl.filter(i => i.id !== p.id);
      }
      this.saveToStore();
      this.fabricCanvas.clear();
      this.generateLines();
    },
    makeLine(coords) {
      return new fabric.Line(coords, {
        fill: this.showVolumeControl ? '#ff5d38' : '#51b847',
        stroke: this.showVolumeControl ? '#ff5d38' : '#51b847',
        strokeWidth: this.showVolumeControl ? 2 : 1,
        selectable: false,
        originX: 'center',
        originY: 'center',
        type: 'line',
      });
    },
    makeCircle(left, top, originX, line1, line2, lockMovementX, id) {
      const c = new fabric.Circle({
        left: this.getLeft(left),
        top: this.getHeight(top),
        strokeWidth: 1,
        radius: 4,
        fill: '#fff',
        stroke: '#666',
        originX,
        originY: 'top',
        lockMovementX,
        hasControls: false,
        hasBorders: false,
        line1,
        line2,
        type: 'circle',
        id,
      });
      return c;
    },
    getVolume(top) {
      const vol = (this.height - top) / this.height;
      return vol;
    },
    getHeight(prc) {
      return this.height - ((this.height * prc));
    },
    getLeft(time) {
      return (time - this.startFrom) * this.zoomedPxPerSec;
    },
    getTime(left) {
      return (left / this.zoomedPxPerSec) + this.startFrom;
    },
    getLastIndex() {
      return this.volumeControl.length - 1;
    },
    generateLines() {
      let line = null;
      let circle = null;
      this.volumeControl.reduce((prev, cur, index) => {
        if (!isNull(prev)) {
          if (prev.point.length > cur.length && index !== this.getLastIndex()) {
            return ({
              line: null,
              point: prev.point,
            });
          }
          if (prev.point.length + (10 / this.zoomedPxPerSec)
                        < this.volumeControl[this.getLastIndex()].length) {
            // add line
            line = this.makeLine([
              this.getLeft(prev.point.length) + 5,
              this.getHeight(prev.point.level) + 5,
              index === this.getLastIndex()
                ? this.getLeft(cur.length) - 5
                : this.getLeft(cur.length) + 5,
              this.getHeight(cur.level) + 5,
            ]);
            this.fabricCanvas.add(line);
            // add circle
            if (this.showVolumeControl) {
              if (index === 1) {
                circle = this.makeCircle(prev.point.length, prev.point.level, 'left', prev.line, line, true, prev.point.id);
              } else {
                circle = this.makeCircle(prev.point.length, prev.point.level, 'left', prev.line, line, false, prev.point.id);
              }
              this.fabricCanvas.add(circle);
            }
          }
          // change last line coordinate (x2 y2) if coordinates out of canvas
          if (prev.point.length + (10 / this.zoomedPxPerSec)
                        > this.volumeControl[this.getLastIndex()].length) {
            line.set({
              x2: this.getLeft(cur.length) - 5,
              y2: this.getHeight(cur.level) + 5,
            });
          }
          // add last circle
          if (index === this.getLastIndex() && this.showVolumeControl) {
            circle = this.makeCircle(cur.length, cur.level, 'right', line, null, true, cur.id);
            this.fabricCanvas.add(circle);
          }
        }
        return {
          line,
          point: cur,
        };
      }, null);
    },
    saveToStore: debounce(function saveTo() {
      let idx = -1;
      if (this.items.every(itm => has(itm, 'uuid'))) {
        idx = this.items.findIndex(itm => itm.uuid === this.uuid);
      } else {
        idx = this.items.findIndex(itm => itm.id === this.id);
      }

      if (idx !== -1) {
        const item = Object.assign({}, this.items[idx], {
          volumeControl: this.volumeControl.concat(),
        });
        this.changeItem(item);
      }
    }, 600),
  },
};
</script>

<style lang="stylus" scoped>
  @import '../../../../sass/front/components/bulma-theme';

  .ctrl-audio {
    &__canvas {
      cover-all();
    }
    &__cursor {
      cover-all();
      cursor: move;
      &--split {
        cursor: col-resize;
      }
    }
  }
</style>
