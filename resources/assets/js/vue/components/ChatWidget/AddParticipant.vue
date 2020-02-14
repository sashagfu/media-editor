<template>
  <div class="AddParticipant adp">
    <div
      :title="trans('chat.add_participant')"
      class="adp__btn"
    >
      <font-awesome-icon
        :icon="['fas', 'plus']"
        class="fa-icon"
      />
    </div>
    <div class="adp__dropdown">
      <div class="ddown__head">
        <div class="left">
          <div class="ddown__title">
            {{ trans('chat.add_participant') }}
          </div>
        </div>
        <div class="fl-right">
          <div
            class="ddown__btn"
            @click="handleAdd"
          >
            <font-awesome-icon
              v-if="loading"
              :icon="['fas', 'spinner']"
              spin
              class="fa-icon fa-icon--invert"
            />
            <font-awesome-icon
              v-else-if="selectedFriends.length"
              :icon="['fas', 'check']"
              class="fa-icon fa-icon--invert"
            />
            <font-awesome-icon
              v-else
              :icon="['fas', 'plus']"
              class="fa-icon fa-icon--invert"
            />
          </div>
        </div>
      </div>
      <div
        v-if="$apollo.queries.fetchFollowers.loading || $apollo.queries.fetchFollowing.loading"
        class="ddown__loading">
        <font-awesome-icon
          :icon="['fas', 'spinner']"
          spinn
          class="fa-icon fa-icon--spinner"
        />
      </div>
      <SearchBar
        v-model="searchText"
      />
      <div class="ddown__main">
        <ParticipantItem
          v-for="(user, idx) in filteredFriends"
          :key="`usr-${user.id}-${idx}`"
          :user="user"
          :selected-friends="selectedFriends"
          @toggle-user="toggleUser"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { mergeUnique } from 'Helpers/utils';

import FETCH_FOLLOWERS from 'Gql/users/queries/fetchFollowers.graphql';
import FETCH_FOLLOWING from 'Gql/users/queries/fetchFollowing.graphql';
import FETCH_THREADS from 'Gql/messages/queries/fetchThreads.graphql';

import CREATE_PARTICIPANTS from 'Gql/messages/mutations/createParticipants.graphql';
import CREATE_GROUP_THREAD from 'Gql/messages/mutations/createGroupThread.graphql';

import ParticipantItem from './ParticipantItem';
import SearchBar from './SearchBar';

export default {
  name: 'AddParticipant',
  components: {
    ParticipantItem,
    SearchBar,
  },
  props: {
    activeUser: {
      type: Object,
      default: () => ({}),
    },
    thread: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      fetchFollowers: [],
      fetchFollowing: [],
      selectedFriends: [],
      loading: false,
      searchText: '',
    };
  },
  computed: {
    friends() {
      const followers = this.cloneFollowers(this.fetchFollowers);
      const following = this.cloneFollowers(this.fetchFollowing);
      return mergeUnique(followers, following);
    },
    filteredFriends() {
      return this.friends.filter((u) => {
        const idx = this.thread.users.findIndex(ui => ui.uuid === u.uuid);
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
        if (idx === -1 && searchText) {
          return true;
        }
        return false;
      });
    },
    countOfUsers() {
      const users = this.thread.users.filter(u => u.uuid !== this.activeUser.uuid);
      return users.length;
    },
  },
  methods: {
    handleAdd() {
      if (!this.selectedFriends.length || this.loading) return;
      if (this.countOfUsers < 2) {
        this.createGroupChat();
      } else {
        this.addParticipants();
      }
    },
    resetSearchText() {
      this.searchText = '';
    },
    async createGroupChat() {
      if (!this.selectedFriends.length) {
        return;
      }
      // Add already exists chat member to feature group chat
      const member = this.thread.users.filter(u => u.uuid !== this.activeUser.uuid)[0];
      this.selectedFriends.push(member.id);

      try {
        this.loading = true;
        const { data: { createGroupThread } } = await this.$apollo.mutate({
          mutation: CREATE_GROUP_THREAD,
          variables: {
            ids: this.selectedFriends,
          },
          update: (store, { data: { createGroupThread: groupThread } }) => {
            const data = store.readQuery({ query: FETCH_THREADS });
            const idx = data.fetchThreads.findIndex(ft => ft.id === groupThread.id);
            if (idx !== -1) {
              data.fetchThreads[idx] = groupThread;
            } else {
              data.fetchThreads.push(groupThread);
            }
            store.writeQuery({ query: FETCH_THREADS, data });
          },
        });
        this.selectedFriends = [];
        this.$bus.$emit('chat-open', createGroupThread.id);
      } catch (err) {
        console.error(err);
      } finally {
        this.loading = false;
      }
    },
    async addParticipants() {
      if (!this.selectedFriends.length) {
        return;
      }
      try {
        this.loading = true;
        await this.$apollo.mutate({
          mutation: CREATE_PARTICIPANTS,
          variables: {
            threadId: this.thread.id,
            ids: this.selectedFriends,
          },
        });
        this.selectedFriends = [];
      } catch (err) {
        console.error(err);
      } finally {
        this.loading = false;
      }
    },
    cloneFollowers(obj) {
      return JSON.parse(JSON.stringify(obj, (k, v) => {
        switch (k) {
          case '__typename':
          case 'createdAt':
          case 'updatedAt':
            return undefined;
          default:
            return v;
        }
      }));
    },
    toggleUser(id) {
      const idx = this.selectedFriends.findIndex(u => u === id);
      if (idx !== -1) {
        this.selectedFriends = this.selectedFriends.filter(u => u !== id);
      } else {
        this.selectedFriends.push(id);
      }
    },
  },
  apollo: {
    fetchFollowers: {
      query: FETCH_FOLLOWERS,
      variables() {
        return {
          userId: this.activeUser.id,
        };
      },
    },
    fetchFollowing: {
      query: FETCH_FOLLOWING,
      variables() {
        return {
          userId: this.activeUser.id,
        };
      },
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
    transition all .3s
    cursor pointer
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

  .adp {
    position relative

    &__btn {
      fl-center()
      size(32, 26)
      cursor pointer
    }
    &__dropdown {
      size(180, 220)
      background-color $white
      border-radius $radius
      border 1px solid $border
      bottom 32px
      display none
      flex-direction column
      position absolute
      right -15px
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
      background-color $primary
      border-radius $radius
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
