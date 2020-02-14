<template>
  <div class="CreateChat cc">
    <div
      v-if="full"
      class="cc__full-box"
    >
      <div
        :title="trans('chat.create_chat')"
        class="cc__btn cc__btn--full"
      >
        <font-awesome-icon
          :icon="['fas', 'plus']"
          class="fa-icon fa-icon--plus"
        />
      </div>
      <div class="cc__title">
        {{ trans('chat.create_chat') }}
      </div>
    </div>
    <div
      v-else
      :title="trans('chat.create_chat')"
      class="cc__btn"
    >
      <font-awesome-icon
        :icon="['fas', 'plus']"
        class="fa-icon fa-icon--plus"
      />
    </div>
    <div
      :class="[
        'cc__dropdown',
        {'cc__dropdown--full': full}
      ]"
    >
      <div class="ddown__head">
        <div class="left">
          <div class="ddown__title">
            {{ trans('chat.choose_participant') }}
          </div>
        </div>
        <div class="fl-right">
          <div
            :title="trans('chat.create_chat')"
            :class="[
              'ddown__btn',
              {'ddown__btn--disabled': !selectedFriends.length}
            ]"
            @click="handleCreate"
          >
            <font-awesome-icon
              v-if="loading"
              :icon="['fas', 'spinner']"
              spin
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
      <div class="ddown__main">
        <ParticipantItem
          v-for="(user, idx) in friends"
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
import { mapGetters } from 'vuex';
import { mergeUnique } from 'Helpers/utils';

import FETCH_FOLLOWERS from 'Gql/users/queries/fetchFollowers.graphql';
import FETCH_FOLLOWING from 'Gql/users/queries/fetchFollowing.graphql';
import FETCH_THREADS from 'Gql/messages/queries/fetchThreads.graphql';

import CREATE_THREAD from 'Gql/messages/mutations/createThread.graphql';
import CREATE_GROUP_THREAD from 'Gql/messages/mutations/createGroupThread.graphql';

import ParticipantItem from './ParticipantItem';

export default {
  name: 'CreateChat',
  components: {
    ParticipantItem,
  },
  props: {
    full: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      fetchFollowers: [],
      fetchFollowing: [],
      selectedFriends: [],
      loading: false,
    };
  },
  computed: {
    ...mapGetters('general', ['activeUser']),
    friends() {
      const followers = this.cloneFollowers(this.fetchFollowers);
      const following = this.cloneFollowers(this.fetchFollowing);
      return mergeUnique(followers, following);
    },
  },
  methods: {
    handleCreate() {
      if (!this.selectedFriends.length) {
        return;
      }
      if (this.selectedFriends.length === 1) {
        this.createChat();
      } else {
        this.createGroupChat();
      }
    },
    async createGroupChat() {
      if (!this.selectedFriends.length) {
        return;
      }
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
    async createChat() {
      try {
        this.loading = true;
        const { data: { createThread } } = await this.$apollo.mutate({
          mutation: CREATE_THREAD,
          variables: {
            targetId: this.selectedFriends[0],
          },
          update: (store, { data: { createThread: cThread } }) => {
            const data = store.readQuery({ query: FETCH_THREADS });
            const idx = data.fetchThreads.findIndex(ft => ft.id === cThread.id);
            if (idx !== -1) {
              data.fetchThreads[idx] = cThread;
            } else {
              data.fetchThreads.push(cThread);
            }
            store.writeQuery({ query: FETCH_THREADS, data });
          },
        });
        this.$bus.$emit('chat-open', createThread.id);
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

  .fa-icon
    fnt($white, 12px, $weight-normal, center)
    transition all .2s
    &--plus
      color $grey-lighter
      font-size 20px

  .cc
    position: relative
    &__full-box
      fl-left()
      width 100%
    &__title
      fnt($text-lighter, 14px, $weight-semibold, left)
      margin-left -8px
    &__btn
      fl-center()
      cursor pointer
      flex 0 0 auto
      height 56px
      width 100%
      &--full
        size(56, 56)

    &__dropdown
      size(196, 240)
      background-color $white
      border-radius $radius
      border 1px solid $border
      bottom 48px
      display none
      flex-direction column
      position absolute
      right 8px
      z-index 10
      &--full
        right 148px
      &:before
        border-color $border transparent;
        border-style solid;
        border-width 7px 7px 0;
        bottom -7px;
        content: '';
        display: block;
        position: absolute;
        right 20px;
        width 0;

      &:after
        border-color: $white transparent;
        border-style: solid;
        border-width: 6px 6px 0;
        bottom: -6px;
        content: "";
        display: block;
        position: absolute;
        right: 21px;
        width: 0;

    &:hover &__dropdown
      display flex

  .ddown
    position: relative
    &__head
      fl-between()
      border-bottom 1px solid $border
      padding 8px 16px
      width 100%
    &__title
      fnt($text, 12px, $weight-semibold, left)

    &__main
      display flex
      flex-direction column
      overflow-y auto

    &__loading
      fl-center()
      height 180px

    &__btn
      fl-center()
      size(22, 32)
      background-color $primary
      border-radius $radius
      cursor pointer
      transition all .3s
      &--disabled
        cursor not-allowed
        opacity .7
      &:hover
        opacity .7

</style>
