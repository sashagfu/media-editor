<template>
  <div
    :class="[
      'NotificationMessageBox',
      'nb',
      {'nb--not-read': !isRead},
    ]"
    @click="markAsRead"
  >
    <div class="left">
      <div
        class="nb__avatar-box"
        @click.stop="goToProfile"
      >
        <Avatar
          v-if="user.avatar.includes('/profile/default/avatar')"
          :size="36"
          :username="user.displayName"
          custom-class="nb__avatar"
        />
        <div
          v-else
          :style="{'background-image': `url(${user.avatar})`}"
          class="nb__avatar"
        />
      </div>
      <div class="nb__title-box">
        <div class="nb__title-line">
          <span
            class="nb__title"
            @click.stop="goToProfile"
          >
            {{ user.displayName }}
          </span>
          <span class="nb__sub-title">
            {{ notificationMessage }}
          </span>
        </div>
        <div class="nb__message">
          {{ message.body }}
        </div>
        <div class="nb__data">
          {{ edited ? 'edited:' : 'created:' }} {{ createdAt }}
        </div>
      </div>
    </div>
    <div class="fl-right">
      <div class="nb__icon">
        <font-awesome-icon
          v-if="isRead"
          :icon="['far', 'envelope-open']"
          class="fa-icon"
        />
        <font-awesome-icon
          v-else
          :icon="['far', 'envelope']"
          class="fa-icon"
        />
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment';

import FETCH_NOTIFICATIONS from 'Gql/notifications/queries/fetchNotifications.graphql';
import MARK_NOTIFICATIONS_READ from 'Gql/notifications/mutations/markNotificationsRead.graphql';

import Avatar from 'Pages/ProfilePage/Avatar';

export default {
  name: 'NotificationMessageBox',
  components: {
    Avatar,
  },
  props: {
    notification: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
    };
  },
  computed: {
    user() {
      return this.notification.data.user;
    },
    notificationMessage() {
      return this.notification.message.substring(this.user.displayName.length);
    },
    message() {
      return this.notification.data.message;
    },
    isRead() {
      // readAt: "2019-03-14T20:39:37+00:00"
      if (!this.notification.readAt) {
        return false;
      }
      return moment().isAfter(this.notification.readAt);
    },
    dateTime() {
      if (this.edited) {
        return moment(this.message.updatedAt).format('DD/MM/YYYY @ hh:mma');
      }
      return moment(this.message.createdAt).format('DD/MM/YYYY @ hh:mma');
    },
    edited() {
      return !moment(this.message.createdAt).isSame(this.message.updatedAt);
    },
  },
  methods: {
    goToProfile() {
      window.location = `/profile/${this.user.username}`;
    },
    chatOpen() {
      this.$bus.$emit('chat-open', this.message.threadId);
    },
    async markAsRead() {
      this.chatOpen();
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
            store.writeQuery({ query: FETCH_NOTIFICATIONS, data });
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
    &__data
      fnt($primary, 12px, $weight-light, left)

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

</style>
