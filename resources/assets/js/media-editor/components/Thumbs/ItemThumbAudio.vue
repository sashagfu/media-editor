<template>
  <div class="ItemThumbAudio me-media-item">
    <el-tooltip
      :manual="true"
      :value="isVisibleControlPanel"
      :visible-arrow="false"
      placement="left-start"
      popper-class="me-media-item__controls"
    >
      <div
        slot="content"
        class="me-media-item__controls-slot"
      >
        <div
          :class="[
            'me-media-item__btn',
            'me-media-item__btn--hoverable',
            {'me-media-item__btn--active': showVolumeControl}
          ]"
          @click="toggleShowVolumeControl"
        >
          VC
        </div>
        <div class="me-media-item__btn me-media-item__btn--disable">
          EQ
        </div>
      </div>
      <div class="me-media-item__container">
        <item-controller-audio-volume
          :show-volume-control="showVolumeControl && isVisibleControlPanel"
          v-bind="$props"
        />
      </div>
    </el-tooltip>
    <canvas
      ref="canvas"
      class="me-media-item__waveform"
    />
  </div>
</template>

<script>
import axios from 'axios';
import { mapGetters } from 'vuex';
import { ITEM_THUMB } from '../../config/settings';
import ItemControllerAudioVolume from '../Controllers/ItemControllerAudioVolume';

export default {
  name: 'ItemThumbAudio',
  components: {
    ItemControllerAudioVolume,
  },
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
  },
  data() {
    return {
      waveform: null,
      showVolumeControl: false,
    };
  },
  computed: {
    ...mapGetters('settings', ['itemThumb']),
    ...mapGetters('timeline', [
      'zoomedPxPerSec',
      'selectedItems',
    ]),
    ...mapGetters('dragging', [
      'isDraggingTimelineItem',
    ]),
    ...mapGetters('project', [
      'items',
      'layers',
    ]),
    canvas() {
      return this.$refs.canvas;
    },
    ctx() {
      return this.canvas.getContext('2d');
    },
    width() {
      return (((this.length || this.file.time) * this.zoomedPxPerSec))
                - (ITEM_THUMB.boxBorder * 2);
    },
    freqTime() {
      return this.waveform.left.length / this.file.time;
    },
    freqPx() {
      return this.waveform.left.length / (this.file.time * this.zoomedPxPerSec);
    },
    videoItemIsOpen() {
      if (this.file.fileType === 'video') {
        const idx = this.items.findIndex(item => item.id === this.id);
        if (idx !== -1) {
          const lix = this.layers.findIndex(layer => layer.id === this.items[idx].layerId);
          if (lix !== -1) {
            return this.layers[lix].height === 2;
          }
        }
        return false;
      }
      return true;
    },
    isVisibleControlPanel() {
      return (this.selectedItems.findIndex(si => si === this.id) !== -1)
        && !this.isDraggingTimelineItem
        && this.videoItemIsOpen;
    },
  },
  watch: {
    waveform() {
      this.drawing();
    },
    zoomedPxPerSec() {
      this.drawing();
    },
    startFrom() {
      this.drawing();
    },
    length() {
      this.drawing();
    },
  },
  mounted() {
    this.getWaveformData();
    this.setCanvasDimensions();
  },
  methods: {
    toggleShowVolumeControl() {
      this.showVolumeControl = !this.showVolumeControl;
    },
    getWaveformData() {
      if (!this.file.waveformData || this.waveform) return;
      axios.get(this.file.waveformData)
        .then(({ data }) => {
          this.waveform = data;
        });
    },
    setCanvasDimensions() {
      this.canvas.height = this.itemThumb.height;
      this.canvas.width = this.width;
    },
    /**
         * Get array with waveform data sliced by item length
         *
         * @returns {{left, right}}
         * @private
         */
    getSlicedWaveformData() {
      const {
        waveform,
        startFrom,
        length,
        freqTime,
      } = this;
      const { time } = this.file;

      const begin = Math.floor(freqTime * startFrom);
      const end = Math.floor(begin + (freqTime * (length || time)));

      return {
        left: waveform.left.slice(begin, end),
        right: waveform.right.slice(begin, end),
      };
    },
    getNormalizeFreq(freq) {
      return freq < 1 ? 1 : freq;
    },
    setCanvasStyle() {
      const { ctx, itemThumb } = this;
      ctx.strokeStyle = itemThumb.strokeColor;
      ctx.fillStyle = itemThumb.fillColor;
      ctx.lineWidth = itemThumb.lineWidth;
    },
    resetCanvas() {
      this.setCanvasDimensions();
      this.setCanvasStyle();
    },
    /**
         * Get average value from array between 2 indexes
         *
         * @param array
         * @param start - start index
         * @param end - end index
         * @returns {*}
         */
    getAverageValue(array, start, end) {
      let localEnd = end;
      if (start === end) return array[start];
      if (localEnd > (array.length - 1)) {
        localEnd = array.length - 1;
      }

      let sum = 0;
      for (let i = start + 1; i <= localEnd; i += 1) {
        sum += array[i];
      }
      return sum / (localEnd - start);
    },
    drawing() {
      if (!this.waveform) return;

      const { ctx, freqPx } = this;
      const data = this.getSlicedWaveformData();
      const { length: dataLength } = this.waveform.left;
      const freq = this.getNormalizeFreq(freqPx);
      const middle = this.itemThumb.height / 2;

      this.resetCanvas();

      ctx.beginPath();

      // left channel, top
      let x = 0;
      let prevIndex = 0;
      let i = 0;
      for (i; i < dataLength; i += freq) {
        const curIndex = Math.floor(i);
        const val = this.getAverageValue(data.left, prevIndex, curIndex);
        ctx.lineTo(x, middle - (middle * val));
        prevIndex = curIndex;
        x += 1;
      }
      for (i -= freq; i >= 0; i -= freq) {
        const curIndex = Math.floor(i);
        const val = this.getAverageValue(data.right, curIndex, prevIndex);
        ctx.lineTo(x, middle + (middle * val));
        prevIndex = curIndex;
        x -= 1;
      }

      ctx.lineTo(0, middle);

      ctx.fill();
      ctx.stroke();
      ctx.closePath();
    },
  },
};
</script>

<style lang="stylus" scoped>
  @import '../../../../sass/front/components/bulma-theme';
  .me-media-item {
    position: relative;
    &__waveform {
      background-color: $grey-lighter;
      display: block;
      user-select: none;
    }
  }
</style>

<style lang="stylus">
  @import '../../../../sass/front/components/bulma-theme';
  .me-media-item {
    &__container {
      cover-all();
    }
    &__controls {
      background-color: $white !important;
      border-radius: $radius !important;
      border: 1px solid $grey-dark !important;
      padding: 2px !important;
    }
    &__controls-slot {
      fl-left();
    }
    &__btn {
      fl-center();
      fnt($text-invert, 8px, $weight-semibold, center);
      background-color: $grey-dark;
      border-radius: $radius-small;
      cursor: pointer;
      height: 12px;
      transition: all .3s;
      width: 12px;
      &--hoverable {
        &:hover {
          opacity: .5;
        }
      }
      &--disable {
        cursor: default;
        background-color: $grey-light;
      }
      &--active {
        background-color: $danger;
      }
    }
    &__btn + &__btn {
      margin-left: 2px;
    }
  }
</style>
