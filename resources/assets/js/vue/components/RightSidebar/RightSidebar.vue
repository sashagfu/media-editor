<template>
  <div
    class="RightSidebar r-sb"
    @click.stop=""
    @mouseleave="closeRightSidebar"
  >
    <transition
      name="component-translate"
      mode="out-in"
    >
      <RightSidebarNarrow
        v-if="isNarrow && showRightSideBar"
        :threads="filteredThreads"
        @toggle-components="toggleComponents"
      />
      <RightSidebarFull
        v-if="!isNarrow && showRightSideBar"
        :threads="filteredThreads"
        :search-text="searchText"
        @input-search-text="updateSearch"
        @toggle-components="toggleComponents"
      />
    </transition>
    <ChatWidget
      v-if="showChat"
      :key="`th-${threadId}`"
      :is-narrow="isNarrow || !showRightSideBar"
      :thread="liveThread()"
      @close-chat-window="closeChatWindow"
    />
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import { has } from 'lodash';

import FETCH_THREADS from 'Gql/messages/queries/fetchThreads.graphql';
import SUBSCRIPTION_THREAD_UPDATED from 'Gql/messages/subscriptions/threadUpdated.graphql';

import ChatWidget from 'Pages/ChatWidget/ChatWidget';
import RightSidebarFull from './RightSidebarFull';
import RightSidebarNarrow from './RightSidebarNarrow';

export default {
  name: 'RightSidebar',
  components: {
    ChatWidget,

    RightSidebarFull,
    RightSidebarNarrow,
  },
  data: () => ({
    showRightSideBar: false,
    isNarrow: true,
    searchText: '',
    showChat: false,
    threadId: null,
    fixRightSidebar: false,
  }),
  computed: {
    ...mapGetters('general', ['activeUser']),
    filteredThreads() {
      return this.fetchThreads.filter((thread) => {
        let searchText = true;
        return thread.users.some((item) => {
          if (this.searchText) {
            const indexDisplayName = item.displayName.toLowerCase()
              .indexOf(this.searchText.toLowerCase());
            const indexEmail = item.email.toLowerCase()
              .indexOf(this.searchText.toLowerCase());
            const indexUsername = item.username.toLowerCase()
              .indexOf(this.searchText.toLowerCase());
            searchText = (indexDisplayName !== -1) || (indexEmail !== -1) || (indexUsername !== -1);
          }
          return searchText;
        });
      });
    },
  },
  created() {
    this.$bus.$on('toggle-right-sidebar', (fix = false) => {
      this.fixRightSidebar = fix;
      this.showRightSideBar = !this.showRightSideBar;
    });
    this.$bus.$on('chat-open', (id) => {
      this.showChat = false;
      this.threadId = id;
      this.showChat = true;
    });
  },
  methods: {
    closeChatWindow() {
      this.showChat = false;
    },
    updateSearch(text) {
      this.searchText = text;
    },
    toggleComponents() {
      this.isNarrow = !this.isNarrow;
    },
    collapseMenu() {
      this.isNarrow = true;
    },
    liveThread() {
      const idx = this.fetchThreads.findIndex(ft => ft.id === this.threadId);
      if (idx !== -1) {
        return this.fetchThreads[idx];
      }
      return null;
    },
    closeRightSidebar() {
      if (this.fixRightSidebar) return;
      this.$bus.$emit('toggle-right-sidebar');
    },
  },
  apollo: {
    fetchThreads: {
      query: FETCH_THREADS,
      subscribeToMore: [
        {
          document: SUBSCRIPTION_THREAD_UPDATED,
          variables() {
            return {
              userId: this.activeUser.id,
            };
          },
          updateQuery: (previousResult, { subscriptionData }) => {
            if (!has(previousResult, 'fetchThreads')) {
              return { fetchThreads: [] };
            }
            if (!has(subscriptionData, 'data')) {
              return previousResult;
            }
            const { fetchThreads } = previousResult;
            const { threadUpdated } = subscriptionData.data;
            console.log('SUBSCRIPTION_THREAD_UPDATED threadUpdated ', threadUpdated);
            const idx = fetchThreads.findIndex(ft => ft.id === threadUpdated.id);
            if (idx !== -1) {
              const threads = fetchThreads.concat();
              threads[idx] = threadUpdated;
              return { fetchThreads: [...threads] };
            }
            return {
              fetchThreads: [
                ...fetchThreads,
                threadUpdated,
              ],
            };
          },
        },
      ],
      result({ data: { fetchThreads } }) {
        const activeThread = fetchThreads.find(th => th.active === true);

        // open chat widget if there is unclosed(active) thread
        if (activeThread) {
          this.$bus.$emit('chat-open', activeThread.id);
        }
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

  .r-sb
    display flex
    flex-direction column
    height 100%
    justify-content space-between
    position fixed
    right 0
    top 0
    z-index 3

  .component-translate-enter-active
    transition transform .5s ease-out

  .component-translate-leave-active
    transition transform .3s ease-in

  .component-translate-enter,
  .component-translate-leave-to
    transform translateX(100%)
</style>
