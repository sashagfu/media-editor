<template>
  <div class="Notification notif">
    <div class="notif__box">
      <div class="fa-icon__box">
        <font-awesome-icon
          :icon="['far', 'bell']"
          class="fa-icon"
        />
      </div>
      <div
        v-if="unreadNotificationsCount"
        class="notif__tag"
      >
        {{ unreadNotificationsCount }}
      </div>
    </div>
    <div class="notif__dropdown-menu">
      <NotificationDropdownMenu
        :loading="$apollo.queries.fetchNotifications.loading"
        :notifications="fetchNotifications"
      />
    </div>
  </div>
</template>

<script>
import moment from 'moment';

import { mapGetters, mapActions } from 'vuex';
import { has } from 'lodash';
import * as constants from 'Helpers/constants';

import FETCH_NOTIFICATIONS from 'Gql/notifications/queries/fetchNotifications.graphql';

import SUBSCRIPTION_NOTIFICATIONS_CREATED
  from 'Gql/notifications/subscriptions/notificationCreated.graphql';

import NotificationDropdownMenu from './NotificationDropdownMenu';

export default {
  name: 'Notification',
  components: {
    NotificationDropdownMenu,
  },
  data() {
    return {
      nextPageUrl: null,
      fetchNotifications: [],
    };
  },
  computed: {
    ...mapGetters('general', ['activeUser']),
    unreadNotificationsCount() {
      let count = 0;
      this.fetchNotifications.forEach((fn) => {
        if (!fn.readAt) {
          const isAfter = moment(fn.createdAt)
            .isAfter(localStorage.getItem('uploadingFiles', 'YYYY-MM-DD HH:mm:ss'));
          count += 1;
          if (fn.data.typeName === constants.NOTIF_TYPE_FILES && isAfter) {
            this.setProcessing(false);
            localStorage.removeItem('uploadingFiles');
            this.closeDialog();
          }
        } else if (!moment()
          .isAfter(fn.readAt)) {
          count += 1;
        }
      });
      return count;
    },
  },
  methods: {
    ...mapActions('upload', [
      'setProcessing',
      'closeDialog',
    ]),
  },
  apollo: {
    fetchNotifications: {
      query: FETCH_NOTIFICATIONS,
      subscribeToMore: [
        {
          document: SUBSCRIPTION_NOTIFICATIONS_CREATED,
          variables() {
            return {
              userId: this.activeUser.id,
            };
          },
          updateQuery: (previousResult, { subscriptionData }) => {
            if (!has(previousResult, 'fetchNotifications')) {
              return { fetchNotifications: [] };
            }
            if (!has(subscriptionData, 'data')) {
              return previousResult;
            }
            const { fetchNotifications } = previousResult;
            const { notificationCreated } = subscriptionData.data;

            const idx = fetchNotifications.findIndex(fn => fn.id === notificationCreated.id);
            if (idx !== -1) {
              return previousResult;
            }
            const audio = new Audio('/sounds/notification_sound.mp3');
            audio.play();
            return {
              fetchNotifications: [
                notificationCreated,
                ...fetchNotifications,
              ],
            };
          },
        },
      ],
      skip() {
        return !this.activeUser.id;
      },
    },
  },
};

</script>

<style
        lang="stylus"
        scoped
>
    @import '../../../../../sass/front/components/bulma-theme';

    .notif {
        display flex
        flex-direction column
        position relative

        &:hover &__dropdown-menu {
            display flex
        }

        &__box {
            fl-center()
            cursor pointer
            height 72px
            position relative
            transition all .3s
            width 62px
        }

        &__dropdown-menu {
            align-items flex-start
            box-shadow 0px 0px 20px 0px rgba($grey-darker, .1)
            display none
            flex-direction column
            left -158px
            position absolute
            top 56px
            z-index 1
            +desktop() {
                left: -228px;
            }
        }

        &__tag {
            fnt($text-invert, 10px, 700, center)
            fl-center()
            circle(19)
            background-color $info
            position absolute
            right 12px
            top 12px
        }
    }

    .fa-icon {
        color $text-light
        font-size 1.5rem

        &__box {
            fl-center()
            size(24, 24)
            flex 0 0 auto
        }
    }

</style>
