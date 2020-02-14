<template>
  <div class="NotificationDropdownMenu nmb">
    <div class="menu-diamond"/>
    <div class="nmb__headline">
      <div class="nmb__headline-title">
        {{ trans('notifications.name') }}
      </div>
      <div class="fl-right">
        <font-awesome-icon
          v-if="deleteLoading"
          :icon="['fas', 'spinner']"
          spin
          class="fa-icon fa-icon--smaller fa-icon--clear"
        />
        <div
          class="nmb__headline-title nmb__headline-title--menu nmb__headline--clear "
          @click="deleteNotifications"
        >
          {{ trans('notifications.clear') }}
        </div>
        <font-awesome-icon
          v-if="readLoading"
          :icon="['fas', 'spinner']"
          spin
          class="fa-icon fa-icon--smaller"
        />
        <div
          class="nmb__headline-title nmb__headline-title--menu"
          @click="readAllNotifications()"
        >
          {{ trans('notifications.mark_all_read') }}
        </div>
      </div>
    </div>
    <div
      v-if="loading"
      class="nmb__content-box">
      <font-awesome-icon
        :icon="['fas', 'spinner']"
        spin
        class="fa-icon"
      />
    </div>
    <div
      v-else-if="!notifications.length"
      class="nmb__content-box"
    >
      <div class="nmb__empty">
        {{ trans('notifications.empty') }}
      </div>
    </div>
    <div
      v-else
      class="nmb__main-box"
    >
      <!--notifications-->
      <div
        :class="[
          'nmb__main',
          {'nmb__main--without-footer': !showFooter}
        ]"
      >
        <!-- NotificationBox -->
        <component
          v-for="notification in notificationsSliced"
          :key="notification.id"
          :is="notificationsComponent(notification)"
          :notification="notification"
        />
      </div>
      <div
        v-if="showFooter"
        class="nmb__footer"
        @click="scrollNotifications"
      >
        <div class="nmb__view-all">
          {{ trans('common.load_more') }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import * as constants from 'Helpers/constants';

import FETCH_NOTIFICATIONS from 'Gql/notifications/queries/fetchNotifications.graphql';
import MARK_NOTIFICATIONS_READ from 'Gql/notifications/mutations/markNotificationsRead.graphql';
import DELETE_NOTIFICATIONS from 'Gql/notifications/mutations/deleteNotifications.graphql';

import NotificationBox from './NotificationBox';
import NotificationMessageBox from './NotificationMessageBox';
import NotificationStarBox from './NotificationStarBox';
import NotificationProjectBox from './NotificationProjectBox';
import NotificationFilesBox from './NotificationFilesBox';
import NotificationTransactionBox from './NotificationTransactionBox';

export default {
  name: 'NotificationDropdownMenu',
  components: {
    NotificationBox,
    NotificationMessageBox,
    NotificationStarBox,
    NotificationProjectBox,
    NotificationFilesBox,
    NotificationTransactionBox,
  },
  props: {
    loading: {
      type: Boolean,
      default: false,
    },
    notifications: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      readLoading: false,
      deleteLoading: false,
      slice: 6,
    };
  },
  computed: {
    notificationsSliced() {
      return this.notifications.slice(0, this.slice);
    },
    showFooter() {
      return this.notifications.length > this.slice;
    },
    ids() {
      return this.notifications.map(n => n.id);
    },
  },
  methods: {
    notificationsComponent(notificationsItem) {
      if (notificationsItem.data.typeName === constants.NOTIF_TYPE_MESSAGE) {
        return 'NotificationMessageBox';
      }
      if (notificationsItem.data.typeName === constants.NOTIF_TYPE_STAR) {
        return 'NotificationStarBox';
      }
      if (notificationsItem.data.typeName === constants.NOTIF_TYPE_PROJECT) {
        return 'NotificationProjectBox';
      }
      if (notificationsItem.data.typeName === constants.NOTIF_TYPE_FILES) {
        return 'NotificationFilesBox';
      }
      if (notificationsItem.data.typeName === constants.NOTIF_TYPE_TRANSACTION) {
        return 'NotificationTransactionBox';
      }
      return 'NotificationBox';
    },
    async readAllNotifications() {
      if (this.readLoading || !this.notifications.length) return;
      try {
        this.readLoading = true;
        await this.$apollo.mutate({
          mutation: MARK_NOTIFICATIONS_READ,
          variables: {
            ids: this.ids,
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
      } finally {
        this.readLoading = false;
      }
    },
    async deleteNotifications() {
      if (this.deleteLoading || !this.notifications.length) return;
      this.deleteLoading = true;
      await this.$apollo.mutate({
        mutation: DELETE_NOTIFICATIONS,
        update: (store) => {
          const data = store.readQuery({ query: FETCH_NOTIFICATIONS });
          data.fetchNotifications = [];
          store.writeQuery({
            query: FETCH_NOTIFICATIONS,
            data,
          });
        },
      });
      this.deleteLoading = false;
    },
    scrollNotifications() {
      this.slice += 3;
    },
  },
};

</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

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

    &--clear
      color $red

  .menu-diamond
    deg45();
    size(8, 8)
    background-color $white
    border-left 1px solid $border
    border-top 1px solid $border
    left 185px
    position absolute
    top -5px
    width 8px
    z-index 1
    +desktop()
      left 254px

  .nmb
    background-color $white
    border-radius $radius
    border 1px solid $border
    position relative
    width 360px

    &__headline
      fl-between()
      border-bottom 1px solid $border
      height 36px
      padding 0 24px
      width 100%

    &__headline-title
      fnt($text, 9px, $weight-normal, left);
      line-height 2
      text-transform uppercase
      width 100%

      &--menu
        fnt($text, 9px, $weight-semibold, right);
        cursor pointer
        padding-left 4px
        transition all .3s
        white-space nowrap

        &:hover {
          color $text-light
        }

    &__headline
      &--clear
        color $red
        margin-right 5px;

        &:hover
          color $red-hover

    &__content-box
      fl-center()
      height 80px
      width 100%

    &__empty
      fnt($text-lighter, 18px, $weight-light, center)

    &__item
      max-height 400px
      overflow-y auto

      &:not(:last-child)
        border-bottom 1px solid $border

    &__loader
      font-size 16px
      margin-left 5px

    &__main-box
      display flex
      flex-direction column

    &__main
      display flex
      flex-direction column
      max-height 408px
      overflow-y auto
      width 100%

      &--without-footer
        max-height 440px

    &__footer
      fl-center()
      background-color $info
      border-radius 0 0 $radius $radius
      cursor pointer
      height 32px
      text-transform capitalize
      width 100%

    &__view-all
      fnt($primary-invert, 12px, $weight-semibold, center)

</style>
