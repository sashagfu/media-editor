<template>
  <div
    :style="styles"
    class="ItemThumbSlide item-thumb-slide"
  >
    <div class="item-thumb-slide__title">
      {{ file.name }}
    </div>
    <font-awesome-icon
      :icon="['fas', 'font']"
      class="fa-icon"
    />
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import { ITEM_THUMB } from '../../config/settings';

export default {
  name: 'ItemThumbSlide',
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
        width: `${this.width}px`,
      };
    },
  },
};
</script>

<style lang="stylus" scoped>
  @import '../../../../sass/front/components/bulma-theme';

  .item-thumb-slide {
    fl-center();
    height: 50px;
    position: relative;
    width: 100%;
    &__title {
      fnt($text, 8px, $weight-light, left);
      txt-ellipsis();
      left: 4px;
      position: absolute;
      top: 0;
    }
  }

  .fa-icon {
    color: $blue;
    font-size: 1rem;
  }
</style>
