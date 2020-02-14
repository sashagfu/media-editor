<template>
  <div
    :class="[
      'ProjectStatisticsProjectBox',
      'pspb',
    ]"
  >
    <div class="left">
      <div
        class="pspb__avatar-box"
        @click.stop="goToProject"
      >
        <div
          :style="{
            'backgroundImage': `url(${asset.thumbPath})`,
            'background-color': randomBackgroundColor
          }"
          class="pspb__cover"
        />
      </div>
      <div class="pspb__title-box">
        <div class="pspb__title-line">
          <span
            class="pspb__title"
            @click.stop="goToProject"
          >
            {{ project.title }}
          </span>
        </div>
        <div
          class="pspb__user"
          @click.stop="goToUser"
        >
          by: {{ project.author.displayName }}
        </div>
        <div class="pspb__duration">
          {{ duration }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment';
import * as constants from 'Helpers/constants';

export default {
  name: 'ProjectStatisticsPageProjectBox',
  props: {
    project: {
      type: Object,
      default: () => {},
    },
  },
  data() {
    return {
      backgroundColors: [
        '#F44336', '#FF4081', '#9C27B0', '#673AB7',
        '#3F51B5', '#2196F3', '#03A9F4', '#00BCD4', '#009688',
        '#4CAF50', '#8BC34A', '#CDDC39', '#FFC107',
        '#FF9800', '#FF5722', '#795548', '#9E9E9E', '#607D8B',
      ],
    };
  },
  computed: {
    randomBackgroundColor() {
      return this.backgroundColors[Math.floor(Math.random() * this.backgroundColors.length)];
    },
    dateTime() {
      return moment(this.project.updatedAt).format('MM/DD/YYYY @ hh:mma');
    },
    asset() {
      if (this.project.assets.length) {
        return this.project.assets.find(a => a.type === constants.FULL);
      }
      return { thumbPath: '' };
    },
    duration() {
      if (moment.duration(this.asset.time).asHours() > 1) {
        return moment.utc(moment.duration(this.asset.time).asMilliseconds()).format('HH:mm:ss');
      }
      return moment.utc(moment.duration(this.asset.time).asMilliseconds()).format('mm:ss');
    },
  },
  methods: {
    goToProject() {
      window.location = `/projects/${this.project.uuid}`;
    },
    goToUser() {
      window.location = `/profile/${this.project.author.username}`;
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .pspb
    fl-between()
    cursor pointer
    flex: 0 0 auto;
    padding 12px 16px
    width: 40%;

    &--not-read
      background-color rgba($info, 0.05)
    &__avatar-box
      cursor pointer
    &__avatar
      fl-center()
      circle(36)
      background center center/cover no-repeat $grey-lighter
    &__title-box
      display flex
      flex-direction column
      padding-left 8px
    &__title-line
      fl-left()
      align-items flex-end
    &__title
      fnt($text, 14px, $weight-semibold, left)
      cursor pointer
      line-height 1
    &__sub-title
      fnt($text-light, 12px, $weight-light, left)
      line-height 1
      padding-left 4px
    &__message
      fnt($text-light, 12px, $weight-light, left)
      txt-ellipsis()
      align-items flex-end
      display flex
      height 16px
      width 232px
    &__user
      fnt($primary, 12px, $weight-light, left)
    &__cover
      fl-center()
      background center center/cover no-repeat $info
      border-radius 3px
      cursor pointer
      height 40px
      width 40px
    &__duration
      fnt($grey-light, 12px, $weight-light, left)

  .fa-icon
    color $text-lighter
    font-size 1.5rem
    &--smaller
      color $text
      font-size 12px
    &__box
      fl-center()
      size(24, 24)
      flex 0 0 auto
    &--play
      color  $white
      font-size 16px
      transition all .2s
      &:hover {
        opacity 0.8
      }
      &--action {
        fnt($text-light, 14px, $weight-light, left)
      }
      &--asset {
        fnt($text-lighter, 10px, $weight-light, left)
      }
</style>
