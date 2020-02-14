<template>
  <div class="PlayerControls">
    <div class="me-player__controls">
      <div class="me-player__slider">
        <el-slider
          v-show="!isHideControls && duration"
          :show-tooltip="false"
          :max="duration"
          :value="currentTime"
          class="me-slider-el"
          @input="onSliderChanged"
        />
      </div>
      <div>
        <duration
          v-show="duration"
          :s="currentTime"
          class="me-player-controls__time-left"
          show-frame
        />
      </div>
      <a
        v-show="!isHideControls"
        class="me-player-controls__button"
        @click.prevent="togglePlaying"
      >
        <font-awesome-icon
          v-if="playing"
          :icon="['fas', 'pause']"
          class="fa-icon fa-icon--lg"
        />
        <font-awesome-icon
          v-else
          :icon="['fas', 'play']"
          :class="{
            'fa-icon--disable': !items.length,
            'fa-icon--hoverable': items.length
          }"
          class="fa-icon fa-icon--lg"
        />
      </a>
      <div>
        <duration
          v-show="duration"
          :s="duration"
          class="me-player-controls__time-right"
          show-frame
        />
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import Duration from '../common/Duration';
import * as fileTypes from '../../config/file-types';

export default {
  name: 'PreviewPlayerControls',
  components: {
    Duration,
  },
  computed: {
    ...mapGetters('player', [
      'playing',
      'currentTime',
      'duration',
    ]),
    ...mapGetters('preview', [
      'galleryItem',
    ]),
    ...mapGetters('project', [
      'items',
    ]),
    isHideControls() {
      return this.galleryItem.fileType === fileTypes.IMAGE
                || this.galleryItem.fileType === fileTypes.SLIDE;
    },
  },
  methods: {
    ...mapActions('player', [
      'togglePlaying',
      'setCurrentTime',
    ]),
    onSliderChanged(value) {
      if (value !== this.currentTime) {
        this.setCurrentTime(value);
      }
    },
  },
};
</script>

<style lang="stylus" scoped>
  @import '../../../../sass/front/components/bulma-theme';

  .fa-icon {
    color: $text-light;
    font-size: 1rem;
    transition: all .3s;
    &--hoverable {
      &:hover {
          color: $text-lighter;
      }
    }
    &--lg {
      font-size: 1.5rem;
    }
    &--invert {
      color: $white;
      &:hover {
        color: $white-bis;
      }
    }
    &--disable {
      opacity: 0.5;
      cursor: not-allowed;
    }
    &--pointer {
      cursor: pointer;
    }
    &__box {
      padding: 0 4px 0;
    }
  }
</style>
