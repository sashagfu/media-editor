<template>
  <div class="MultiUserAvatar m-avatar">
    <div class="m-avatar__box">
      <template v-for="userItem in multiUsers">
        <Avatar
          v-if="userItem.avatar.includes('/profile/default/avatar')"
          :key="`usr-${userItem.id}`"
          :size="20"
          :username="userItem.displayName"
          custom-class="m-avatar__avatar"
        />
        <div
          v-else
          :style="{'background-image': `url(${userItem.avatar})`}"
          :key="`usr-${userItem.id}`"
          class="m-avatar__avatar"
        />
      </template>
      <div
        v-if="countOfUsers > 2"
        class="m-avatar__avatar m-avatar__avatar--count"
      >
        {{ decorateCountOfUser }}
      </div>
    </div>
    <div class="m-avatar__dropdown">
      <div class="ddown__head">
        <div class="left">
          <div class="ddown__title">
            {{ trans('chat.participants') }}
          </div>
        </div>
        <div class="fl-right">
          <div
            class="ddown__btn"
            @click="deleteParticipants"
          >
            <font-awesome-icon
              v-if="loading"
              :icon="['fas', 'spinner']"
              spin
              class="fa-icon fa-icon--dark"
            />
            <font-awesome-icon
              v-else
              :icon="['far', 'trash-alt']"
              class="fa-icon fa-icon--dark"
            />
          </div>
        </div>
      </div>
      <SearchBar
        v-model="searchText"
      />
      <div class="ddown__main">
        <ParticipantItem
          v-for="(user, idx) in usersCanDelete"
          :key="`usr-${user.id}-${idx}`"
          :user="user"
          :selected-friends="selectedUsers"
          @toggle-user="toggleUser"
        />
      </div>
    </div>
  </div>
</template>

<script>

import DELETE_PARTICIPANTS from 'Gql/messages/mutations/deleteParticipants.graphql';

import ParticipantItem from './ParticipantItem';
import SearchBar from './SearchBar';

export default {
  name: 'MultiUserAvatar',
  components: {
    SearchBar,
    ParticipantItem,
  },
  props: {
    users: {
      type: Array,
      default: () => [],
    },
    threadId: {
      type: [String, Number],
      required: true,
    },
    activeUser: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      selectedUsers: [],
      loading: false,
      searchText: '',
    };
  },
  computed: {
    multiUsers() {
      if (this.users.length > 3) {
        return this.users.slice(0, 3);
      }
      return this.users;
    },
    usersCanDelete() {
      return this.users.filter((u) => {
        let searchText = true;
        if (this.searchText) {
          const indexDisplayName = u.displayName.toLowerCase()
            .indexOf(this.searchText.toLowerCase());
          const indexEmail = u.email.toLowerCase()
            .indexOf(this.searchText.toLowerCase());
          const indexUsername = u.username.toLowerCase()
            .indexOf(this.searchText.toLowerCase());
          searchText = (indexDisplayName !== -1) || (indexEmail !== -1) || (indexUsername !== -1);
        }
        if (u.uuid !== this.activeUser.uuid && searchText) {
          return true;
        }
        return false;
      });
    },
    countOfUsers() {
      const users = this.users.filter(u => u.uuid !== this.activeUser.uuid);
      return users.length;
    },
    decorateCountOfUser() {
      const count = this.countOfUsers - 2;
      if (count < 1) {
        return '';
      }
      if (count >= 1 && count < 10) {
        return `+${count}`;
      }
      return '+9';
    },
  },
  methods: {
    async deleteParticipants() {
      if (!this.selectedUsers.length) {
        return;
      }
      try {
        this.loading = true;
        await this.$apollo.mutate({
          mutation: DELETE_PARTICIPANTS,
          variables: {
            threadId: this.threadId,
            ids: this.selectedUsers,
          },
        });
        this.selectedUsers = [];
      } catch (err) {
        console.error(err);
      } finally {
        this.loading = false;
      }
    },
    toggleUser(id) {
      const idx = this.selectedUsers.findIndex(u => u === id);
      if (idx !== -1) {
        this.selectedUsers = this.selectedUsers.filter(u => u !== id);
      } else {
        this.selectedUsers.push(id);
      }
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .fa-icon {
    fnt($white-shadow, 14px, $weight-normal, left)
    cursor pointer
    transition all .3s
    &--spinner {
      color $text
      font-size 16px
      margin -2px
    }
    &--dark {
      color $text-lighter
    }
    &--invert {
      fnt($text-invert, 12px, $weight-normal, center)
    }
  }

  .m-avatar {
    position relative

    &__avatar {
      fl-center()
      circle(18)
      background center center/cover no-repeat $primary
      margin 0 2px 2px 0

      &--count {
        fnt($text-invert, 8px, $weight-normal, center)
      }
    }
    &__box {
      size(56, 40)
      display flex
      flex-wrap wrap
      justify-content center
      margin 0 -2px -2px 0
      padding 8px 0
    }
    &__dropdown {
      size(180, 220)
      background-color $white
      border-radius $radius
      border 1px solid $border
      bottom 48px
      display none
      flex-direction column
      left -34px
      position absolute
      z-index 10
      &:before {
        border-color $border transparent;
        border-style solid;
        border-width 7px 7px 0;
        bottom -7px;
        content: '';
        display: block;
        position: absolute;
        right 20px;
        width 0;
      }
      &:after {
        border-color: $white transparent;
        border-style: solid;
        border-width: 6px 6px 0;
        bottom: -6px;
        content: "";
        display: block;
        position: absolute;
        right: 21px;
        width: 0;
      }
    }
    &:hover &__dropdown {
      display flex
    }
  }

  .ddown {
    &__head {
      fl-between()
      border-bottom 1px solid $border
      padding 8px 16px
      width 100%
    }
    &__title {
      fnt($text, 12px, $weight-semibold, left)
    }
    &__main {
      display flex
      flex-direction column
      overflow-y auto
    }
    &__loading {
      fl-center()
      height 180px
    }
    &__btn {
      fl-center()
      size(22, 32)
      border-radius $radius
      border 1px solid $grey-light
      cursor pointer
      transition all .3s
      &:hover {
        opacity .7
      }
    }
  }

  .left {
    fl-left()
  }
  .right {
    fl-right()
  }

</style>
