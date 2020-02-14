<template>
  <div
    ref="rightSidebar"
    :class="[
      'RightSidebarNarrow',
      'r-sbn',
      {'r-sbn--transitional': transitional}
    ]"
  >
    <div class="r-sbn__threads">
      <div
        v-for="thread in threads"
        :key="thread.id"
        class="r-sbn__thread-box"
        @click="chatOpen(thread)"
      >
        <ThreadItemNarrow
          v-if="!thread.hidden"
          :thread="thread"
        />
      </div>
    </div>
    <div class="r-sbn__footer">
      <CreateChat />
      <div
        class="r-sbn__menu"
        @click="toggleComponents"
      >
        <font-awesome-icon
          :icon="['fas', 'bars']"
          class="fa-icon fa-icon--invert"
        />
      </div>
    </div>
  </div>
</template>

<script>
import OPEN_THREAD from 'Gql/messages/mutations/openThread.graphql';
import CLOSE_THREAD from 'Gql/messages/mutations/closeThread.graphql';
import FETCH_THREADS from 'Gql/messages/queries/fetchThreads.graphql';

import CreateChat from 'Pages/ChatWidget/CreateChat';
import ThreadItemNarrow from './ThreadItemNarrow';

export default {
  name: 'RightSidebarNarrow',
  components: {
    ThreadItemNarrow,
    CreateChat,
  },
  props: {
    threads: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      topHeight: 72,
      transitional: false,
      oldScrollTop: 0,
    };
  },
  mounted() {
    window.addEventListener('scroll', this.getCurrentScroll, false);
  },
  destroyed() {
    window.removeEventListener('scroll', this.getCurrentScroll, false);
  },
  methods: {
    chatOpen(thread) {
      try {
        const activeThread = this.threads.find(th => th.active === true);
        if (activeThread) {
          this.$apollo.mutate({
            mutation: CLOSE_THREAD,
            variables: {
              id: activeThread.id,
            },
          });
        }
        this.$apollo.mutate({
          mutation: OPEN_THREAD,
          variables: {
            id: thread.id,
          },
          update: (store, { data: { openThread } }) => {
            const data = store.readQuery({ query: FETCH_THREADS });
            const idx = data.fetchThreads.findIndex(th => th.id === thread.id);
            console.log(openThread);
            if (idx !== -1) {
              data.fetchThreads[idx] = openThread;
            }
            store.writeQuery({ query: FETCH_THREADS, data });
          },
        });
      } catch (err) {
        console.error(err);
      }
      this.$bus.$emit('chat-open', thread.id);
    },
    toggleComponents() {
      this.$emit('toggle-components');
    },
    getCurrentScroll() {
      const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

      if (scrollTop > this.topHeight) {
        this.transitional = true;
        if (scrollTop >= this.oldScrollTop) {
          this.$refs.rightSidebar.style.paddingTop = '0';
        } else {
          this.$refs.rightSidebar.style.paddingTop = `${this.topHeight}px`;
        }
      } else {
        this.transitional = false;
        if (scrollTop >= this.oldScrollTop) {
          this.$refs.rightSidebar.style.paddingTop = `${this.topHeight - scrollTop}px`;
        } else {
          this.$refs.rightSidebar.style.paddingTop = `${this.topHeight}px`;
        }
      }
      this.oldScrollTop = scrollTop;
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
    fnt($white, 20px, $weight-normal, center)
    transition all .2s
    &--plus {
      color $grey-lighter
    }
  }

  .r-sbn {
    background-color $white
    border-left 1px solid $border
    display flex
    flex-direction: column
    flex-grow 1
    justify-content space-between
    padding-top 72px
    position relative;
    width 72px
    &--transitional {
      transition all .5s
    }

    &__threads {
      align-items center
      display flex
      flex-direction column
      overflow auto
      &:first-child {
        padding-top 12px
      }
      &:last-child {
        padding-bottom 84px
      }
    }
    &__thread-box {
      fl-center()
      cursor pointer
      flex 0 0 auto
    }
    &__footer {
      bottom 0
      position absolute
      width 100%
    }
    &__menu {
      fl-center()
      background-color $primary
      cursor pointer
      flex 0 0 auto
      height 56px
      width 100%
    }
  }

</style>
