<template>
  <div
    :class="[
      'NotificationFilesBox',
      'nb',
      {'nb--not-read': !isRead},
    ]"
    @click="markAsRead"
  >
    <div class="left">
      <div
        class="nb__avatar-box"
        @click.stop="goToMediaEditor"
      >
        <div
          :style="{
            'backgroundImage': `url(${asset.thumbPath})`,
            'background-color': randomBackgroundColor
          }"
          class="nb__cover"
        >
          <font-awesome-icon
            :icon="['fas', 'play']"
            class="fa-icon fa-icon--play"
          />
        </div>
      </div>
      <div
        class="nb__title-box"
        @click.stop="goToMediaEditor"
      >
        <div class="nb__title-line">
          <span class="nb__sub-title">
            {{ notification.message }}
          </span>
        </div>
        <div class="nb__data">
          created: {{ createdAt }}
        </div>
      </div>
    </div>
    <div class="fl-right">
      <div class="nb__icon">
        <font-awesome-icon
          :icon="['fas', 'upload']"
          class="fa-icon"
        />
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment';
import * as constants from 'Helpers/constants';

import FETCH_NOTIFICATIONS from 'Gql/notifications/queries/fetchNotifications.graphql';
import MARK_NOTIFICATIONS_READ from 'Gql/notifications/mutations/markNotificationsRead.graphql';

export default {
  name: 'NotificationProjectBox',
  props: {
    notification: {
      type: Object,
      default: () => ({}),
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
    project() {
      return this.notification.data.project;
    },
    isRead() {
      // readAt: "2019-03-14T20:39:37+00:00"
      if (!this.notification.readAt) {
        return false;
      }
      return moment()
        .isAfter(this.notification.readAt);
    },
    createdAt() {
      return moment(this.notification.createdAt)
        .format('DD/MM/YYYY @ hh:mma');
    },
    asset() {
      if (this.project.assets.length) {
        return this.project.assets.find(a => a.type === constants.FULL);
      }
      return { thumbPath: '' };
    },
  },
  methods: {
    goToMediaEditor() {
      this.markAsRead();
      this.$router.push({
        name: 'EditorPage',
        params: {
          id: this.project.id,
        },
      });
    },
    async markAsRead() {
      if (this.isRead) return;
      try {
        await this.$apollo.mutate({
          mutation: MARK_NOTIFICATIONS_READ,
          variables: {
            ids: [this.notification.id],
          },
          update: (store, { data: { markNotificationsRead } }) => {
            const data = store.readQuery({ query: FETCH_NOTIFICATIONS });
            markNotificationsRead.forEach((mnr) => {
              const idx = data.fetchNotifications.findIndex(fn => fn.id === mnr.id);
              if (idx !== -1) {
                data.fetchNotifications[idx] = mnr;
              }
            });
            store.writeQuery({
              query: FETCH_NOTIFICATIONS,
              data,
            });
          },
        });
      } catch (err) {
        console.error(err);
      }
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .nb
    fl-between()
    cursor pointer
    flex: 0 0 auto;
    padding 12px 16px
    width 100%

    &--not-read
      background-color rgba($info, 0.05)

    &:not(:last-child)
      border-bottom 1px solid $border

    &__avatar-box
      cursor pointer

    &__avatar
      fl-center()
      circle(36)
      background center center / cover no-repeat $grey-lighter

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

    &__message
      fnt($text-light, 12px, $weight-light, left)
      txt-ellipsis()
      align-items flex-end
      display flex
      height 16px
      width 232px

    &__data
      fnt($primary, 12px, $weight-light, left)

    &__cover
      fl-center()
      background center center / cover no-repeat $info
      border-radius 3px
      cursor pointer
      height 40px
      width 40px

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
      color $white
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

  .content-box__cover {
    .fa-icon {

    }
  }
</style>
