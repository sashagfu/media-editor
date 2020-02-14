<template>
  <div
    :class="[
      'NotificationStarBox',
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
        <div
          class="nb__title"
          @click.stop="goToProject"
        >
          {{ message }}
        </div>
        <div class="nb__data">
          created: {{ createdAt }}
        </div>
      </div>
    </div>
    <div class="fl-right">
      <div class="nb__icon">
        <font-awesome-icon
          :icon="['far', 'star']"
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
  name: 'NotificationStarBox',
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
      if (this.notification.data.follower !== null) {
        return this.notification.data.follower;
      }
      return this.notification.data.user;
    },
    message() {
      return this.notification.message;
    },
    project() {
      return this.notification.data.star.starable;
    },
    isRead() {
      // readAt: "2019-03-14T20:39:37+00:00"
      if (!this.notification.readAt) {
        return false;
      }
      return moment().isAfter(this.notification.readAt);
    },
    createdAt() {
      return moment(this.notification.createdAt)
        .format('DD/MM/YYYY [@] HH:mma');
    },
  },
  methods: {
    goToProfile() {
      window.location = `/profile/${this.user.username}`;
    },
    goToProject() {
      window.location = `/projects/${this.project.uuid}`;
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
    padding 12px 16px
    width 100%
    flex: 0 0 auto;
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
    &__title
      fl-left()
      fnt($text-light, 12px, $weight-light, left)
      cursor pointer
      line-height 1
    &__data
      fnt($primary, 12px, $weight-light, left)
      padding-top 2px

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
