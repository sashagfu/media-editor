<template>
  <div
    :style="{'left': lineLeft}"
    class="TimelineMaxLengthLine tl-maxlng"
    @click.stop="handleClick"
  >
    <el-tooltip
      :manual="true"
      :value="isVisibleTooltip"
      :visible-arrow="false"
      class="item"
      effect="dark"
      placement="left-start"
      popper-class="tl-maxlng__tooltip"
    >
      <div slot="content">
        You can create videos
        <br>
        {{ `up to ${maxDuration} minutes long` }}
      </div>
      <div class="tl-maxlng__line"/>
    </el-tooltip>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  name: 'TimelineMaxLengthLine',
  data() {
    return {
      isVisibleTooltip: false,
    };
  },
  computed: {
    ...mapGetters('settings', [
      'maxDuration',
    ]),
    ...mapGetters('timeline', [
      'zoomedPxPerSec',
    ]),
    lineLeft() {
      return `${this.maxDuration * 60000 * this.zoomedPxPerSec}px`;
    },
  },
  mounted() {
    window.addEventListener('click', this.tooltipClose, false);
  },
  methods: {
    handleClick() {
      this.isVisibleTooltip = !this.isVisibleTooltip;
    },
    tooltipClose() {
      this.isVisibleTooltip = false;
    },
  },
};
</script>

<style lang="stylus" scoped>
  @import '../../../../sass/front/components/bulma-theme';

  .tl-maxlng {
    cursor: pointer;
    height: 100%;
    position: absolute;
    top: 0;
    &__line {
      border: 2px solid $danger;
      height: 100%;
      width: 2px;
    }
    &__tooltip {
      background-color: $info !important;
    }
  }

</style>

<style lang="stylus" >
  @import '../../../../sass/front/components/bulma-theme';

  .tl-maxlng {
    &__tooltip {
      background-color: $info !important;
      border-radius: $radius 0 $radius $radius !important;
      position: relative;
      &::after {
        border-right: 8px solid transparent;
        border-top: 8px solid $info;
        content: '';
        position: absolute;
        right: -8px;
        top: 0;
      }
    }
  }

</style>
