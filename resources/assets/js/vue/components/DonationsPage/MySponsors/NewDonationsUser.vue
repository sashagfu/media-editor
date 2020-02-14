<template>
  <div
    :class="[
      'NewDonationsUser',
      'nd-user',
      {'nd-user--accepted': isAccepted},
      {'nd-user--declined': isDeclined},
      {'nd-user--expired': isExpired || isExpiredNow}
    ]"
  >
    <div
      class="nd-user__left"
      @click="goToUser"
    >
      <Avatar
        v-if="newDonation.user.avatar.includes('/profile/default/avatar')"
        :username="newDonation.user.displayName"
        :size="36"
        custom-class="nd-user__avatar"
      />
      <div
        v-else
        :style="{'background-image': `url(${newDonation.user.avatar})`}"
        class="nd-user__avatar"
      />
      <div class="nd-user__title-box">
        <div class="nd-user__title">
          {{ newDonation.user.displayName }}
        </div>
        <div
          :class="[
            'nd-user__sub-title',
            {'nd-user__sub-title--declined': isDeclined || isExpired}
          ]"
        >
          {{ trans('donate.on_al_from') }}: {{ formatDate(newDonation.user.createdAt) }}
        </div>
      </div>
      <div class="nd-user__btn-box">
        <FollowButton
          :following="newDonation.user"
          :active-user="activeUser"
        />
        <MessageButton
          :user="newDonation.user"
          :message="trans('donate.say_thank_you')"
          class="nd-user__btn"
        />
      </div>
    </div>
    <div class="nd-user__center">
      <div class="nd-user__donate-box">
        <div class="nd-user__plus">
          {{ isPending || isAccepted ? '+' : '' }}
        </div>
        <div
          :class="[
            'nd-user__donate-amount',
            {'nd-user__donate-amount--declined': isDeclined},
            {'nd-user__donate-amount--expired': isExpired}
          ]"
        >
          $ {{ Number.parseInt(newDonation.amount, 10) }}
        </div>
        <div class="nd-user__donate-date">
          {{ formatDateTime(newDonation.createdAt) }}
        </div>
      </div>
    </div>
    <div
      :class="[
        'nd-user__right',
        {'nd-user__right--expired': isExpired},
        {'nd-user__right--pending': isPending},
      ]"
    >
      <div
        v-if="isExpired || isExpiredNow"
        class="nd-user__status-box"
      >
        <div class="nd-user__status-title-box">
          <div class="nd-user__status-title nd-user__status-title--expired">
            {{ trans('donate.expired') }}
          </div>
          <div
            v-if="isExpired"
            class="nd-user__status-date nd-user__status-date--expired">
            {{ trans('donate.at') }}: {{ formatDateTime(newDonation.updatedAt) }}
          </div>
          <!--v-if="isExpiredNow"-->
          <div
            v-else
            class="nd-user__status-date nd-user__status-date--expired">
            {{ trans('donate.time_left') }}: {{ timeLeft }}
          </div>
        </div>
      </div>
      <div
        v-if="isAccepted"
        class="nd-user__status-box"
      >
        <div class="nd-user__status-title-box">
          <div class="nd-user__status-title nd-user__status-title--accepted">
            {{ trans('donate.accepted') }}
          </div>
          <div class="nd-user__status-date nd-user__status-date--accepted">
            {{ trans('donate.at') }}: {{ formatDateTime(newDonation.updatedAt) }}
          </div>
        </div>
        <div class="nd-user__right-box">
          <div class="nd-user__switch-label nd-user__switch-label--accepted">
            {{ trans('donate.always_accept') }}:
          </div>
          <el-switch
            v-model="isAutoAccept"
            @change="toggleAlwaysAccept"
          />
        </div>
      </div>
      <div
        v-if="isDeclined"
        class="nd-user__status-box"
      >
        <div class="nd-user__status-title-box">
          <div class="nd-user__status-title nd-user__status-title--declined">
            {{ trans('donate.declined') }}
          </div>
          <div class="nd-user__status-date nd-user__status-date--declined">
            {{ trans('donate.at') }}: {{ formatDateTime(newDonation.updatedAt) }}
          </div>
        </div>

        <div class="nd-user__right-box">
          <div class="nd-user__switch-label nd-user__switch-label--declined">
            {{ trans('donate.always_decline') }}:
          </div>
          <el-switch
            v-model="isAutoDecline"
            active-color="#ff4d5e"
            @change="toggleAlwaysDecline"
          />
        </div>
      </div>
      <div
        v-if="isPending"
        class="nd-user__status-box"
      >
        <div class="nd-user__status-title-box">
          <div class="nd-user__status-title nd-user__status-title--pending">
            {{ trans('donate.pending') }}
          </div>
          <div class="nd-user__status-date nd-user__status-date--pending">
            {{ trans('donate.time_left') }}: {{ timeLeft }}
          </div>
        </div>
        <div class="nd-user__right-box">
          <el-button
            class="nd-user__status-btn"
            type="primary"
            size="mini"
            @click="donationMutate(4)"
          >
            <font-awesome-icon
              v-if="acceptLoading"
              :icon="['fas', 'spinner']"
              spin
              class="fa-icon fa-icon--spinner"
            />
            <template v-else>
              {{ trans('donate.accept_donation') }}
            </template>
          </el-button>
          <el-button
            class="nd-user__status-btn"
            type="danger"
            size="mini"
            @click="donationMutate(3)"
          >
            <font-awesome-icon
              v-if="declineLoading"
              :icon="['fas', 'spinner']"
              spin
              class="fa-icon fa-icon--spinner"
            />
            <template v-else>
              {{ trans('donate.decline_donation') }}
            </template>
          </el-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment';
import { mapGetters, mapActions } from 'vuex';
import * as constants from 'Helpers/constants';

import FETCH_NEW_DONATIONS from 'Gql/donations/queries/fetchIncomingDonations.graphql';
import ACCEPT_DONATIONS from 'Gql/donations/mutations/acceptDonation.graphql';
import DECLINE_DONATIONS from 'Gql/donations/mutations/declineDonation.graphql';
import TOGGLE_AUTO_ACCEPT_DONATIONS from 'Gql/donations/mutations/toggleAutoAcceptDonation.graphql';
import TOGGLE_AUTO_DECLINE_DONATIONS from 'Gql/donations/mutations/toggleAutoDeclineDonation.graphql';
import FETCH_USER from 'Gql/users/queries/fetchUser.graphql';

import Avatar from 'Pages/ProfilePage/Avatar';
import FollowButton from 'Pages/Common/FollowButton';
import MessageButton from 'Pages/Common/MessageButton';

export default {
  name: 'NewDonationsUser',
  components: {
    Avatar,
    FollowButton,
    MessageButton,
  },
  props: {
    newDonation: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      constants,
      acceptLoading: false,
      declineLoading: false,
      isAutoAccept: false,
      isAutoDecline: false,
      timer: null,
      secondsLeft: 0,
    };
  },
  computed: {
    ...mapGetters('general', ['activeUser']),
    isPending() {
      return (this.newDonation.status
        === constants.DONATIONS_STATUS_PENDING) && this.secondsLeft > 0;
    },
    isExpiredNow() {
      return (this.newDonation.status
        === constants.DONATIONS_STATUS_PENDING) && (this.secondsLeft <= 0);
    },
    isAccepted() {
      return this.newDonation.status
        === constants.DONATIONS_STATUS_ACCEPTED;
    },
    isDeclined() {
      return this.newDonation.status
        === constants.DONATIONS_STATUS_DECLINED;
    },
    isExpired() {
      return (this.newDonation.status
        === constants.DONATIONS_STATUS_EXPIRED);
    },
    timeLeft() {
      if (this.secondsLeft > 60 || this.secondsLeft < -60) {
        return moment.duration(this.secondsLeft, 'seconds').humanize(true);
      }
      if (this.secondsLeft < 0) {
        return `${(moment.duration(this.secondsLeft, 'seconds') / 1000) * -1} sec. ago`;
      }
      return `in ${moment.duration(this.secondsLeft, 'seconds') / 1000} sec.`;
    },
  },
  watch: {
    activeUser: {
      handler() {
        if (!this.activeUser.autoAccepts.length) {
          this.isAutoAccept = false;
        } else {
          this.isAutoAccept = this.activeUser.autoAccepts
            .some(user => user.id === this.newDonation.user.id);
        }
        if (!this.activeUser.autoDeclines.length) {
          this.isAutoDecline = false;
        } else {
          this.isAutoDecline = this.activeUser.autoDeclines
            .some(user => user.id === this.newDonation.user.id);
        }
      },
      immediate: true,
    },
  },
  created() {
    const created = moment(this.newDonation.createdAt);
    this.secondsLeft = (constants.DONATIONS_WAITING_TIME * 3600) - moment().diff(created, 'second');
    if (this.secondsLeft > -3600) {
      this.setTimer();
    }
  },
  destroyed() {
    clearTimeout(this.timer);
  },
  methods: {
    ...mapActions('general', ['setActiveUser']),
    goToUser() {
      this.$router.push({
        name: 'ProfilePage',
        params: {
          username: this.newDonation.user.username,
        },
      });
    },
    setTimer() {
      if (this.secondsLeft > 14400) {
        this.timer = setTimeout(() => {
          this.secondsLeft -= 3600;
          this.setTimer();
        }, (3600 * 1000));
      } else if (this.secondsLeft > 119) {
        this.timer = setTimeout(() => {
          this.secondsLeft -= 60;
          this.setTimer();
        }, (60 * 1000));
      } else if (this.secondsLeft >= -60) {
        this.timer = setTimeout(() => {
          this.secondsLeft -= 1;
          this.setTimer();
        }, 1000);
      } else if (this.secondsLeft >= -3600) {
        this.timer = setTimeout(() => {
          this.secondsLeft -= 60;
          this.setTimer();
        }, (60 * 1000));
      }
    },
    formatDate(date) {
      return moment(date).format('MM/DD/YYYY');
    },
    formatDateTime(date) {
      return moment(date).format('MM/DD/YYYY @ hh:mma');
    },
    async toggleAlwaysAccept() {
      try {
        await this.$apollo.mutate({
          mutation: TOGGLE_AUTO_ACCEPT_DONATIONS,
          variables: {
            userId: this.newDonation.user.id,
          },
          update: (store) => {
            const fetchUser = { query: FETCH_USER };
            const data = store.readQuery(fetchUser);
            const key = data.fetchUser.autoAccepts
              .findIndex(user => user.id === this.newDonation.user.id);
            if (key === -1) {
              data.fetchUser.autoAccepts.push(this.newDonation.user);
            } else {
              data.fetchUser.autoAccepts = data.fetchUser.autoAccepts
                .filter(user => user.id !== this.newDonation.user.id);
            }
            store.writeQuery({ ...fetchUser, data });
          },
        });
        this.setActiveUser();
      } catch (e) {
        console.error(e);
      }
    },
    async toggleAlwaysDecline() {
      try {
        await this.$apollo.mutate({
          mutation: TOGGLE_AUTO_DECLINE_DONATIONS,
          variables: {
            userId: this.newDonation.user.id,
          },
          update: (store) => {
            const fetchUser = { query: FETCH_USER };
            const data = store.readQuery(fetchUser);
            const key = data.fetchUser.autoDeclines
              .findIndex(user => user.id === this.newDonation.user.id);
            if (key === -1) {
              data.fetchUser.autoDeclines.push(this.newDonation.user);
            } else {
              data.fetchUser.autoDeclines = data.fetchUser.autoDeclines
                .filter(user => user.id !== this.newDonation.user.id);
            }
            store.writeQuery({ ...fetchUser, data });
            this.setActiveUser();
          },
        });
      } catch (e) {
        console.error(e);
      }
    },
    async donationMutate(st) {
      // st -> status
      // 3 -> decline
      // 4 -> accept
      try {
        if (st === 3) {
          this.declineLoading = true;
        } else {
          this.acceptLoading = true;
        }
        await this.$apollo.mutate({
          mutation: st === 3 ? DECLINE_DONATIONS : ACCEPT_DONATIONS,
          variables: {
            id: this.newDonation.id,
          },
          update: (store) => {
            const fetchNewDonations = {
              query: FETCH_NEW_DONATIONS,
              variables: {
                userId: this.activeUser.id,
                // status: 2,
              },
            };
            const data = store.readQuery(fetchNewDonations);
            const idx = data.fetchIncomingDonations.findIndex(d => d.id === this.newDonation.id);
            if (idx !== -1) {
              data.fetchIncomingDonations[idx] = Object.assign(
                data.fetchIncomingDonations[idx],
                { status: st, updatedAt: moment().format() },
              );
              store.writeQuery({ ...fetchNewDonations, data });
            }
          },
        });
      } catch (e) {
        console.error(e);
      } finally {
        this.acceptLoading = false;
        this.declineLoading = false;
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

  .fa-icon {
    fnt($text-light, 14px, $weight-light, left);
    transition : all .3s;
    &--user {
      color: $grey;
    }
    &--follow {
      color: $grey;
      font-size: 11px;
    }
    &--envelope {
      color: $blue;
      font-size: 11px;
    }
    &--spinner {
      color     : $text-invert;
      font-size : 16px;
      margin    : -2px;
    }
  }

  .nd-user {
    fl-between();
    background: $white;
    height: 56px;
    padding: 0 26px;
    width: 100%;
    &--accepted {
      background: rgba($primary, 0.05);
    }
    &--declined {
      background: rgba($danger, 0.05);
    }
    &--expired {
      background: rgba($grey, 0.05);
    }
    &:not(:last-child) {
      border-bottom: 1px solid $border;
    }
    &__left {
      fl-left();
      width: 40%;
    }
    &__center {
      fl-center();
      width: 20%;
    }
    &__right {
      fl-between();
      width: 30%;
      &--expired {
        fl-left();
      }
      &--pending {
        fl-right();
      }
    }
    &__avatar {
      fl-center();
      background: center center/cover no-repeat $grey-light;
      border-radius: 50%;
      cursor: pointer;
      flex: 0 0 auto;
      height: 36px;
      width: 36px;
    }
    &__title-box {
      cursor: pointer;
      display: flex;
      flex-direction: column;
      padding-left: 16px;
      width: 240px;
    }
    &__title {
      fnt($text, 14px, $weight-normal, left);
      line-height: 1;
    }
    &__sub-title {
      fnt($primary, 12px, $weight-light, left);
      line-height: 1;
      &--declined {
        color: $text-light;
      }
    }

    &__btn-box {
      fl-left();
      padding: 0 16px;
    }

    &__donate-box {
      fl-left();
    }
    &__donate-title {
      fnt($text-lighter, 14px, $weight-normal, left);
      line-height: 1;
    }
    &__plus {
      fnt($primary, 18px, $weight-normal, right);
      line-height: 1;
      width: 12px;
    }
    &__donate-amount {
      fnt($primary, 18px, $weight-normal, left);
      padding-left: 8px;
      line-height: 1;
      width: 56px;
      &--declined,
      &--expired {
        color: $text-light;
      }
    }
    &__donate-date {
      fnt($text-lighter, 14px, $weight-normal, left);
      padding-left: 8px;
      line-height: 1;
    }
    &__status-box {
      fl-between();
      width: 100%;
      &--accepted {
        fl-right();
      }
    }
    &__status-btn {
      width: 72px;
    }
    &__status-title-box {
      fl-left();
    }
    &__right-box {
      fl-right();
    }
    &__status-date {
      fnt($text-light, 12px, $weight-light, left);
      line-height: 1;
      &--accepted {
        color: rgba($primary, 0.6);
      }
      &--declined {
        color: rgba($danger, 0.6);
      }
    }
    &__status-title {
      fnt($text-lighter, 18px, $weight-semibold, left);
      text-transform: uppercase;
      line-height: 1;
      padding-right: 8px;
      &--pending {
        color: rgba($info, 0.6);
      }
      &--accepted {
        color: rgba($primary, 0.6);
      }
      &--declined {
        color: rgba($danger, 0.6);
      }
    }
    &__switch-label {
      fnt($text-lighter, 12px, $weight-semibold, right);
      padding-right: 8px;
      &--accepted {
        color: rgba($primary, 0.6);
      }
      &--declined {
        color: rgba($danger, 0.6);
      }
    }
    &__btn {
      margin-left: 8px;
    }
    &__btn-count {
      fnt($grey-light, 12px, $weight-normal, center);
    }
  }
</style>
