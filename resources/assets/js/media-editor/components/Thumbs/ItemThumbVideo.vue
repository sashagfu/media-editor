<template>
  <div class="ItemThumbVideo me-media-item-thumbs">
    <div
      :style="styles"
      :class="[
        'me-media-item-thumbs__thumbs',
        {'me-media-item-thumbs--no-sprite': !file.spritePath}
      ]"
    />
    <div
      v-if="!unlinked"
      class="me-media-item-thumbs__waveform"
    >
      <item-thumb-audio
        v-bind="$props"
      />
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import ItemThumbAudio from './ItemThumbAudio';

export default {
  name: 'ItemThumbVideo',
  components: {
    ItemThumbAudio,
  },
  props: {
    file: {
      type: Object,
      default: () => ({}),
    },
    length: {
      type: [Number, String],
      default: 0,
    },
    startFrom: {
      type: Number,
      default: 0,
    },
    unlinked: {
      type: Boolean,
      default: false,
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
  computed: {
    ...mapGetters('settings', ['itemThumb']),
    ...mapGetters('timeline', ['zoomedPxPerSec']),
    fullWidth() {
      return this.file.time * this.zoomedPxPerSec;
    },
    lengthWidth() {
      return this.length ? this.length * this.zoomedPxPerSec : this.fullWidth;
    },
    fullFrameNumber() {
      return Math.ceil(this.fullWidth / this.itemThumb.frameWidth);
    },
    startFromFrameNumber() {
      return (this.startFrom * this.zoomedPxPerSec) / this.itemThumb.frameWidth;
    },
    lengthFrameNumber() {
      return this.length
        ? Math.ceil(this.lengthWidth / this.itemThumb.frameWidth)
        : this.fullFrameNumber;
    },
    eachFrameNumber() {
      return (this.file.frames - 1) / this.fullFrameNumber;
    },
    // Just repeat the sprite image number times as backgroundImage
    bgImage() {
      let bgImage = '';
      for (let i = 0; i < this.lengthFrameNumber; i += 1) {
        if (i !== 0) {
          bgImage += ',';
        }
        bgImage += `url(${this.file.spritePath || this.file.thumbPath})`;
      }
      return bgImage;
    },
    // Get positions for backgroundImage
    position() {
      const {
        eachFrameNumber,
        lengthFrameNumber,
        startFromFrameNumber,
        itemThumb,
      } = this;

      // All positions store here
      let position = '';
      // which frame number from sprite needed
      let frameFromSprite = startFromFrameNumber * eachFrameNumber;
      // y param for current frame
      let y = itemThumb.spriteFrameHeight * Math.round(frameFromSprite);
      // x param for current frame
      let x = 0;

      for (let i = 0; i < lengthFrameNumber; i += 1) {
        // If it not the first frame add comma before
        if (i !== 0) position += ',';
        // Add current values
        position += `${x}px -${y}px`;
        // Change values for next frame
        // x axis - just shift for frame width
        x += itemThumb.frameWidth;
        // Calculate float number for next frame in sprite
        frameFromSprite += eachFrameNumber;
        // y axis - shift to needed frame on sprite calculate on frame height
        y = itemThumb.spriteFrameHeight * Math.floor(frameFromSprite);
      }
      return position;
    },
    styles() {
      return {
        width: `${this.lengthWidth}px`,
        backgroundImage: this.bgImage,
        backgroundPosition: this.position,
      };
    },
  },
};
</script>

<style lang="stylus" scoped>
  .me-media-item-thumbs + .me-media-item-waveform {
    padding-top: 3px;
    height: 0;
  }
  .me-media-item-thumbs--no-sprite {
    background-size: contain;
    background-repeat: repeat-x;
  }
</style>
