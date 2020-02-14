<template>
  <div
    :style="styles"
    class="ItemThumbImage me-image-item-thumb"
  />
</template>

<script>
import { mapGetters } from 'vuex';
import { ITEM_THUMB } from '../../config/settings';

export default {
  name: 'ItemThumbImage',
  props: {
    file: {
      type: Object,
      default: () => ({}),
    },
    length: {
      type: Number,
      default: 0,
      validator(val) {
        return val >= 0;
      },
    },
  },
  computed: {
    ...mapGetters('settings', ['itemThumb']),
    ...mapGetters('timeline', ['zoomedPxPerSec']),
    width() {
      return ((this.length || this.itemThumb.imageDefaultDuration)
                * this.zoomedPxPerSec) - (ITEM_THUMB.boxBorder * 2);
    },
    styles() {
      return {
        backgroundImage: `url(${this.file.filePath})`,
        width: `${this.width}px`,
      };
    },
  },
};
</script>
