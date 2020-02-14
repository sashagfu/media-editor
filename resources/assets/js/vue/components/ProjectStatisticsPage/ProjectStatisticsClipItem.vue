<template>
  <div class="item">
    <ProjectStatisticsPageProjectBox
      :project="project"
    />
    <div class="item__timing">
      {{ formatTime(credit.details.from) }} - {{ formatTime(credit.details.to) }}
    </div>
    <div
      v-if="credit.details.type !== 'FULL'"
      :class="{
        'item__audio': isAudio,
        'item__video': isVideo
      }"
    >
      {{ credit.details.type }}
    </div>
    <div
      v-else
      class="item__video"
    >
      <span class="item__video">VIDEO</span>
      <span class="item__divider">/</span>
      <span class="item__audio">AUDIO</span>
    </div>
    <el-progress
      :text-inside="true"
      :stroke-width="20"
      :percentage="Math.round(credit.details.percentages)"
      color="#51b847"
    />
  </div>
</template>

<script>
import * as constants from 'Helpers/constants';
import moment from 'moment';

import ProjectStatisticsPageProjectBox from './ProjectStatisticsPageProjectBox';

export default {
  name: 'ProjectStatisticsClipItem',
  components: {
    ProjectStatisticsPageProjectBox,
  },
  props: {
    credit: {
      type: Object,
      default: () => {},
    },
    project: {
      type: Object,
      default: () => {},
    },
  },
  computed: {
    isAudio() {
      return this.credit.details.type === constants.AUDIO;
    },
    isVideo() {
      return this.credit.details.type === constants.VIDEO;
    },
  },
  methods: {
    formatTime(time) {
      return moment.utc(moment.duration(time).asMilliseconds()).format('HH:mm:ss');
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .item
    padding 5px 0
    display flex
    justify-content space-between
    width 100%
    text-align left
    border-bottom 1px solid $border
    align-items center
    padding-right 20px

    &__audio
      fnt($blue, 16px, $weight-semibold, left)
      text-transform uppercase
      flex-grow 1
    &__video
      fnt($primary, 16px, $weight-semibold, left)
      text-transform uppercase
      flex-grow 1
    &__timing
      fnt($grey-light, 14px, $weight-normal, left)
      padding 12px 16px
    &__divider
      fnt($grey-light, 16px, $weight-semibold, left)

  .item:last-child
    border none
</style>
