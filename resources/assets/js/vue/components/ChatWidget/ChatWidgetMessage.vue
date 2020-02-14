<template>
  <div
    :class="[
      'ChatWidgetMessage',
      'cwm',
      {'cwm--first': firstPhrase},
      {'cwm--last': lastPhrase},
    ]"
    @click="toggleShowInfo"
  >
    <div class="cwm__side-box">
      <template v-if="!isMine && firstPhrase">
        <Avatar
          v-if="message.user.avatar.includes('/profile/default/avatar')"
          :size="36"
          :username="message.user.displayName"
          custom-class="cwm__avatar"
        />
        <div
          v-else
          :style="{'background-image': `url(${message.user.avatar})`}"
          class="cwm__avatar"
        />
      </template>
      <template
        v-else-if="isMine"
      >
        <font-awesome-icon
          v-if="message.id === '-1'"
          :icon="['fas', 'spinner']"
          spin
          class="fa-icon fa-icon--invert"
        />
        <ActionBox
          v-else
          :actions="messageActions"
          :message="message"
          :loading="loadingId === message.id"
          :class="[
            'cwm__action-box',
            {'cwm__action-box--loading': loadingId === message.id}
          ]"
        />
      </template>
    </div>
    <div class="cwm__main-box">
      <div
        :style="{'background-color': !isMine ? background : ''}"
        :class="[
          'cwm__message-box',
          {'cwm__message-box--is-mine': isMine},
          {'cwm__message-box--first': firstPhrase},
          {'cwm__message-box--last': lastPhrase},
        ]"
      >
        <div class="cwm__message-text">
          {{ message.body }}
        </div>
        <SingleProjectPageVideoPlayer
          v-if="message.project"
          :height="200"
          :project="message.project"
        />
        <div
          v-if="message.project"
          class="cwm__asset-title-box"
        >
          <div
            :class="[
              'cwm__asset-title',
              {'cwm__asset-title--is-mine': isMine},
            ]"
            @click="goToProject"
          >
            {{ message.project.title }}
          </div>
          <div
            :class="[
              'cwm__durations',
              {'cwm__durations--is-mine': isMine},
            ]"
          >
            2:32
          </div>
        </div>
      </div>
      <div
        v-if="isShowingInfo"
        :class="[
          'cwm__time',
          {'cwm__time--is-mine': isMine}
        ]"
      >
        {{ edited ? 'edited:' : 'created:' }} {{ dateTime }}
      </div>
    </div>
    <div class="cwm__side-box">
      <template v-if="isMine && firstPhrase">
        <Avatar
          v-if="message.user.avatar.includes('/profile/default/avatar')"
          :size="36"
          :username="message.user.displayName"
          custom-class="cwm__avatar"
        />
        <div
          v-else
          :style="{'background-image': `url(${message.user.avatar})`}"
          class="cwm__avatar"
        />
      </template>
      <!-- <template
        v-else-if="isMine"
      >
        <ActionBox
          :actions="messageActions"
          :message="message"
          :loading="loadingId === message.id"
          :class="[
            'cwm__action-box',
            {'cwm__action-box--loading': loadingId === message.id}
          ]"
        />
      </template> -->
    </div>
  </div>
</template>

<script>
import { has } from 'lodash';
import moment from 'moment';

import Avatar from 'Pages/ProfilePage/Avatar';
import ActionBox from './ActionBox';
import SingleProjectPageVideoPlayer from '../SingleProjectPage/SingleProjectPageVideoPlayer';

export default {
  name: 'ChatWidgetMessage',
  components: {
    SingleProjectPageVideoPlayer,
    Avatar,
    ActionBox,
  },
  props: {
    message: {
      type: Object,
      default: () => ({}),
    },
    activeUser: {
      type: Object,
      default: () => ({}),
    },
    messages: {
      type: Array,
      default: () => [],
    },
    idx: {
      type: Number,
      default: -1,
    },
    loadingId: {
      type: [String, Number],
      default: '',
    },
  },
  data() {
    return {
      messageActions: ['edit-message', 'delete-message'],
      backgroundColors: [
        '#F44336', '#FF4081', '#9C27B0', '#673AB7',
        '#3F51B5', '#2196F3', '#03A9F4', '#00BCD4', '#009688',
        '#4CAF50', '#8BC34A', '#CDDC39', '#FFC107',
        '#FF9800', '#FF5722', '#795548', '#9E9E9E', '#607D8B',
      ],
      isShowingInfo: false,
    };
  },
  computed: {
    backgroundImage() {
      // TODO it's fake image must be replace
      return 'https://picsum.photos/320/880/?image=1080';
    },
    background() {
      return `${this.randomBackgroundColor(this.message.user.username.length, this.backgroundColors)}34`;
    },
    isMine() {
      if (has(this.message.user, 'uuid')) {
        return this.message.user.uuid === this.activeUser.uuid;
      }
      return this.message.user.id === this.activeUser.id;
    },
    lastPhrase() {
      if (this.idx === this.messages.length - 1) {
        return true;
      }
      if (has(this.message.user, 'uuid') && this.message.user.uuid === this.messages[this.idx + 1].user.uuid) {
        return false;
      } else if (this.message.user.id === this.messages[this.idx + 1].user.id) {
        return false;
      }
      return true;
    },
    firstPhrase() {
      if (this.idx === 0) {
        return true;
      }
      if (has(this.message.user, 'uuid') && this.message.user.uuid === this.messages[this.idx - 1].user.uuid) {
        return false;
      } else if (this.message.user.id === this.messages[this.idx - 1].user.id) {
        return false;
      }
      return true;
    },
    dateTime() {
      if (this.edited) {
        return moment(this.message.updatedAt)
          .format('MM/DD/YYYY @ hh:mma');
      }
      return moment(this.message.createdAt)
        .format('MM/DD/YYYY @ hh:mma');
    },
    edited() {
      return !moment(this.message.createdAt)
        .isSame(this.message.updatedAt);
    },
  },
  methods: {
    toggleShowInfo() {
      this.isShowingInfo = !this.isShowingInfo;
    },
    randomBackgroundColor(seed, colors) {
      return colors[seed % (colors.length)];
    },
    goToProject() {
      this.$router.push({
        name: 'SingleProjectPage',
        params: {
          projectUuid: this.message.shareData.shareUuid,
        },
      });
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .fa-icon
    fnt($text-light, 12px, $weight-light, left)
    transition all .3s
    cursor pointer

    &:hover
      clolor $primary

    &--play
      font-size 16px
      color $white

  /* Chat Widget Message */
  .cwm
    align-items flex-start
    display flex
    flex 0 0 auto
    justify-content space-between
    padding 1px 0
    width 100%
    cursor pointer

    &--first
      padding-top 4px

    &--last
      padding-bottom 4px

    &__side-box
      align-items flex-start
      display flex
      flex 0 0 auto
      height 22px
      margin-bottom -12px
      width 36px
      justify-content flex-end
      align-items center

    &__avatar
      fl-center()
      circle(36)
      background center center / cover no-repeat

    &__main-box
      display flex
      flex-direction column
      flex 1 1 auto
      padding 0 8px

    &__message-box
      align-items flex-start
      display flex
      flex-direction column
      padding 4px 8px

      &--is-mine
        background-color $grey-lighter
        border none
        justify-content flex-end

      &--last
        border-bottom-left-radius $radius-large
        border-bottom-right-radius $radius-large

      &--first
        border-top-left-radius $radius-large
        border-top-right-radius $radius-large

    &__message-text
      fnt($text, 12px, $weight-light, left)

      &--is-mine
        fnt($text-invert, 12px, $weight-normal, right)

    &__asset
      fl-center()
      background center center / cover no-repeat $white
      border-radius $radius
      height 120px
      margin 4px 0
      position relative
      width 100%

    &__play-btn
      fl-center()
      circle(40)
      border 2px solid $white
      cursor pointer
      flex 0 0 auto
      opacity 0
      padding-left 4px
      transition all .3s

    &__asset:hover &__play-btn {
      opacity 1
    }

    &__asset-title-box
      fl-between()
      width 100%
      margin-top -10px
      cursor pointer

    &__asset-title
      fnt($text, 12px, $weight-bold, left)

      &--is-mine
        color $text

    &__durations
      fnt($text, 10px, $weight-normal, right)

      &--is-mine
        color $text

    &__action-box
      opacity 0
      transition all .3s

      &--loading
        opacity 1

    &:hover &__action-box
      opacity 1

    &__time
      fnt($text-lighter, 10px, $weight-light, left)
      padding 0 8px

      &--is-mine
        text-align right

</style>
