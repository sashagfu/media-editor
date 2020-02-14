<template>
  <div class="TimelineZoom me-tl-zoom">
    <div class="fa-icon__box fa-icon__box--r">
      <font-awesome-icon
        :icon="['fas', 'search-minus']"
        class="fa-icon"
      />
    </div>
    <el-slider
      :show-tooltip="false"
      :min="timelineZoomSettings.min"
      :max="timelineZoomSettings.max"
      :step="0.1"
      :value="zoom"
      class="me-slider-el"
      @input="setZoom"
    />
    <div class="fa-icon__box fa-icon__box--l">
      <font-awesome-icon
        :icon="['fas', 'search-plus']"
        class="fa-icon"
      />
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import { TIMELINE_ZOOM as timelineZoomSettings } from '../../config/settings';

export default {
  name: 'TimelineZoom',
  data() {
    return {
      timelineZoomSettings,
    };
  },
  computed: {
    ...mapGetters('timeline', [
      'zoom',
    ]),
  },
  methods: {
    ...mapActions('timeline', [
      'setZoom',
    ]),
  },
};
</script>

<style lang="stylus" scoped>
  @import '../../../../sass/front/components/bulma-theme';

  .fa-icon {
    color: $text-lighter;
    &__box {
      &--l {
        padding-left: 12px;
      }
      &--r {
        padding-right: 12px;
      }
    }
  }

</style>
