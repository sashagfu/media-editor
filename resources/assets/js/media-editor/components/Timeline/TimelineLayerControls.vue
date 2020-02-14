<template>
  <div
    :class="{'me-tl-layer-controls--height--high': height === 2}"
    class="TimelineLayerControls me-tl-layer-controls"
  >
    <div class="me-tl-layer-controls__controls">
      <div
        class="me-tl-layer-controls__tools"
        @click="handleMouseClick"
      >
        <font-awesome-icon
          v-if="height === 2"
          :icon="['far', 'caret-square-up']"
          class="fa-icon fa-icon--hoverable fa-icon--pointer"
        />
        <font-awesome-icon
          v-else
          :icon="['far', 'caret-square-down']"
          class="fa-icon fa-icon--hoverable fa-icon--pointer"
        />
      </div>
    </div>
    <div class="me-tl-layer-controls__volume me-tl-layer-control-volume">
      <div
        class="me-tl-layer-control-volume__icon"
        @click="toggleVolume"
      >
        <font-awesome-icon
          v-if="volume > 0.51"
          :icon="['fas', 'volume-up']"
          class="fa-icon fa-icon--pointer"
        />
        <font-awesome-icon
          v-if="(volume < 0.5) && (volume !== 0)"
          :icon="['fas', 'volume-down']"
          class="fa-icon fa-icon--pointer"
        />
        <font-awesome-icon
          v-if="volume === 0"
          :icon="['fas', 'volume-off']"
          class="fa-icon fa-icon--pointer"
        />
      </div>
      <div class="me-tl-layer-control-volume__slider">
        <el-slider
          :min="0"
          :max="1"
          :step="0.01"
          :format-tooltip="formatTooltip"
          :value="volume"
          class="me-slider-el"
          @input="onLayerVolumeChange"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex';

export default {
  name: 'TimelineLayerControls',
  props: {
    id: {
      type: [Number, String],
      default: 0,
    },
    volume: {
      type: Number,
      default: 0.8,
    },
    height: {
      type: Number,
      default: 1,
    },
  },
  data() {
    return {
      unmuteVolume: 0.8,
    };
  },
  created() {
    this.unmuteVolume = this.volume;
  },
  methods: {
    ...mapActions('project', [
      'setLayerVolume',
      'toggleHeightLayer',
    ]),
    onLayerVolumeChange(val) {
      if (this.volume !== val) {
        this.unmuteVolume = val;
        this.setLayerVolume({
          layerId: this.id,
          volume: val,
        });
      }
    },
    formatTooltip(val) {
      return Math.round(val * 100);
    },
    handleMouseClick() {
      const size = this.height === 1 ? 2 : 1;
      this.toggleHeightLayer({ id: this.id, height: size });
    },
    toggleVolume() {
      if (this.volume) {
        this.setLayerVolume({
          layerId: this.id,
          volume: 0,
        });
      } else {
        this.setLayerVolume({
          layerId: this.id,
          volume: this.unmuteVolume,
        });
      }
    },
  },
};
</script>

<style lang="stylus" scoped>
  @import '../../../../sass/front/components/bulma-theme';

  .fa-icon {
    color: $text-light;
    &--hoverable {
      &:hover {
        color: $text-lighter;
      }
    }
    &--invert {
      color: $white;
      &:hover {
        color: $white-bis;
      }
    }
    &--pointer {
      cursor: pointer;
    }
    &__box {
      padding: 0 4px;
    }
  }
</style>

