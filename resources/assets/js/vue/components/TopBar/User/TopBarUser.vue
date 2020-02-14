<template>
  <div class="TopBarUser user">
    <div class="user__box">
      <div
        v-if="activeUser.avatar"
        class="user__ava-box"
        @click="goToProfile"
      >
        <Avatar
          v-if="activeUser.avatar.includes('/profile/default/avatar')"
          :username="activeUser.displayName ? activeUser.displayName : activeUser.display_name"
          :background-color="'#A9ACC1'"
          :custom-class="'user__ava'"
        />
        <div
          v-else
          :style="{backgroundImage: `url(${activeUser.avatar})`}"
          class="user__ava"
        />
      </div>
      <div class="user__title-box">
        <div class="user__title">
          {{ activeUser.displayName ? activeUser.displayName : activeUser.display_name }}
        </div>
        <div class="user__sub-title">
          @{{ activeUser.username }}
        </div>
      </div>
    </div>
    <div class="user__dropdown-menu">
      <UserDropdownMenu
        @open-settings="openSettings"
        @withdraw-funds="openWithdrawFunds"
        @change-password="openChngPsw"
        @log-out="openLogOut"
        @open-top-up="openTopUp"
      />
    </div>
    <UserProfileSettings
      :settings-is-open="settingsIsOpen"
      @close-settings="closeSettings"
    />
    <UserLogOut
      :log-out-is-open="logOutIsOpen"
      @close-log-out="closeLogOut"
    />
    <UserChangePassword
      :chng-psw-is-open="chngPswIsOpen"
      @close-chng-psw="closeChngPsw"
    />
    <WithdrawFundsDialog
      :withdraw-funds-is-open="withdrawFundsIsOpen"
      @close-withdraw-funds="closeWithdrawFunds"
    />
    <TopUpDialog
      :is-dialog-open="topUpDialogOpen"
      :user="activeUser"
      @close-dialog="closeTopUp"
    />

  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import * as constants from 'Helpers/constants';
import { has } from 'lodash';

import FETCH_ME from 'Gql/users/queries/fetchMe.graphql';
import SUBSCRIPTION_USER_UPDATED from 'Gql/users/subscriptions/userUpdated.graphql';

import Avatar from 'Pages/ProfilePage/Avatar';
import WithdrawFundsDialog from 'Pages/DonationsPage/WithdrawFundsDialog';
import TopUpDialog from 'Pages/DonationsPage/TopUpDialog';

import UserDropdownMenu from './UserDropdownMenu';
import UserProfileSettings from './UserProfileSettings';
import UserLogOut from './UserLogOut';
import UserChangePassword from './UserChangePassword';

export default {
  name: 'TopBarUser',
  components: {
    Avatar,
    UserDropdownMenu,
    UserProfileSettings,
    UserLogOut,
    UserChangePassword,
    WithdrawFundsDialog,
    TopUpDialog,
  },
  data() {
    return {
      settingsIsOpen: false,
      logOutIsOpen: false,
      chngPswIsOpen: false,
      withdrawFundsIsOpen: false,
      topUpDialogOpen: false,
    };
  },
  computed: {
    ...mapGetters('general', [
      'activeUser',
    ]),
  },
  watch: {
    fetchMe(user) {
      this.setActiveUser(user);
    },
  },
  created() {
    const hash = document.location.hash.slice(1)
      .toUpperCase();
    if (hash === constants.VERIFY_STATUS_OK || hash === constants.VERIFY_STATUS_FAIL) {
      this.withdrawFundsIsOpen = true;
    }
  },
  methods: {
    ...mapActions('general', [
      'setActiveUser',
    ]),
    openChngPsw() {
      this.chngPswIsOpen = true;
    },
    closeChngPsw() {
      this.chngPswIsOpen = false;
    },
    openWithdrawFunds() {
      this.withdrawFundsIsOpen = true;
    },
    closeWithdrawFunds() {
      this.withdrawFundsIsOpen = false;
    },
    openTopUp() {
      this.topUpDialogOpen = true;
    },
    closeTopUp() {
      this.topUpDialogOpen = false;
    },
    openLogOut() {
      this.logOutIsOpen = true;
    },
    closeLogOut() {
      this.logOutIsOpen = false;
    },
    goToProfile() {
      this.$router.push({
        name: 'ProfilePage',
        params: { username: this.activeUser.username },
      });
    },
    openSettings() {
      this.settingsIsOpen = true;
    },
    closeSettings() {
      this.settingsIsOpen = false;
    },
  },
  apollo: {
    fetchMe: {
      query: FETCH_ME,
      subscribeToMore: [
        {
          document: SUBSCRIPTION_USER_UPDATED,
          variables() {
            return {
              userId: this.activeUser.id,
            };
          },
          updateQuery: (previousResult, { subscriptionData }) => {
            if (!has(subscriptionData, 'data')) {
              return previousResult;
            }

            const { fetchMe } = previousResult;
            const { userUpdated } = subscriptionData.data;

            Object.assign(fetchMe, userUpdated);

            return fetchMe;
          },
        },
      ],
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .user {
    align-items: flex-start;
    display: flex;
    flex-direction: column;
    height: 72px;
    position: relative;

    &__box {
      display: flex;
      height: 72px;
      position: relative;
    }

    &__ava-box {
      fl-center();
      cursor: pointer;
      height: 100%;
      width: 56px;
    }

    &__ava {
      fl-center();
      background: center center / cover no-repeat $grey-light;
      border-radius: 100%;
      flex: 0 0 auto;
      height: 32px;
      width: 32px;
    }

    &__title-box {
      align-items: flex-start;
      display: flex;
      flex-direction: column;
      height: 100%;
      justify-content: center;
      width: 100px;
      +desktop() {
        display: none;
      }
    }

    &__title {
      fnt($text, 12px, $weight-bold, left);
    }

    &__sub-title {
      fnt($grey, 9px, $weight-normal, left);
    }

    &__dropdown-menu {
      align-items: flex-start;
      box-shadow: $shadow;
      display: none;
      flex-direction: column;
      right: -16px;
      position: absolute;
      top: 56px;
      width: 232px;
      z-index: 1;
    }

    &:hover &__dropdown-menu {
      display: flex;
    }

    &:hover &__arrow {
      transform: rotate(180deg);

    }
  }


</style>
