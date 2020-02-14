<template>
  <div
    ref="rightSidebar"
    :class="[
      'RightSidebarFull',
      'r-sbf',
      {'r-sbf--transitional': transitional}
    ]"
  >
    <div class="r-sbf__threads">
      <div
        v-for="thread in threads"
        :key="thread.id"
        class="r-sbf__thread-box"
      >
        <ThreadItemFull
          v-if="!thread.hidden || searchText !== ''"
          :thread="thread"
          @chat-open="chatOpen(thread)"
        />
      </div>
    </div>
    <div class="r-sbf__footer">
      <CreateChat :full="true"/>
      <div class="r-sbf__menu">
        <div class="r-sbf__search-inp">
          <div class="r-sbf__inp-box">
            <input
              :value="searchText"
              :placeholder="trans('common.quick_search')"
              type="text"
              name="quick-search"
              class="r-sbf__inp"
              @input="debounceInput"
              @keydown.esc="resetSearchText"
            >
          </div>
          <div
            class="r-sbf__inp-btn"
            @click="resetSearchText"
          >
            <font-awesome-icon
              v-if="searchText"
              :icon="['fas', 'times']"
              class="fa-icon fa-icon--search"
            />
            <font-awesome-icon
              v-else
              :icon="['fas', 'search']"
              class="fa-icon fa-icon--search"
            />
          </div>
        </div>
        <div
          class="r-sbf__close-btn"
          @click="toggleComponents"
        >
          <font-awesome-icon
            :icon="['fas', 'times']"
            class="fa-icon fa-icon"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { debounce } from 'lodash';

import OPEN_THREAD from 'Gql/messages/mutations/openThread.graphql';
import CLOSE_THREAD from 'Gql/messages/mutations/closeThread.graphql';
import FETCH_THREADS from 'Gql/messages/queries/fetchThreads.graphql';

import CreateChat from 'Pages/ChatWidget/CreateChat';
import ThreadItemFull from './ThreadItemFull';

export default {
  name: 'RightSidebarFull',
  components: {
    CreateChat,
    ThreadItemFull,
  },
  props: {
    threads: {
      type: Array,
      default: () => [],
    },
    searchText: {
      type: String,
      default: '',
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
            const idx = data.fetchThreads.findIndex(th => th.id === this.thread.id);
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
    resetSearchText() {
      this.$emit('input-search-text', '');
    },
    debounceInput: debounce(function updateSearch(e) {
      this.$emit('input-search-text', e.target.value);
    }, 600),
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
    &--search {
      fnt($grey-light, 14px, $weight-normal, center)
    }
  }

  .r-sbf {
    background-color $white
    border-left 1px solid $border
    display flex
    flex-direction column
    flex-grow 1
    justify-content space-between
    padding-top 72px
    position relative
    width 205px
    &--transitional {
      transition all .5s
    }

    &__threads {
      align-items flex-start
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
      cursor pointer
      flex 0 0 auto
      padding 0 8px
      width 100%
    }
    /* &__footer {
      fl-between()
      background-color: $primary
      bottom 0
      cursor pointer
      flex 0 0 auto
      height 56px
      position absolute
      width 100%
    } */
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
    &__close-btn {
      fl-center()
      height 100%
      padding 0 8px
    }
    &__search-inp {
      fl-left()

    }
    &__inp-box {
      background-color $white
      border-radius $radius 0 0 $radius
      border 1px solid $border
      border-right none
      height 28px
      margin-left 8px
      padding 0 14px
    }
    &__inp {
      fnt($text-light, 12px, $weight-normal, left);
      size(26, 104)
      border none
      outline none

      &::-webkit-input-placeholder {
        color $grey-light
        font-weight 300
      }
      &::-moz-placeholder {
        color $grey-light
        font-weight 300
      }
      &:-moz-placeholder {
        color $grey-light
        font-weight 300
      }
      &:-ms-input-placeholder {
        color $grey-light
        font-weight 300
      }
    }
    &__inp-btn {
      fl-center()
      size(28, 28)
      background-color $white
      border-radius 0 $radius $radius 0
      border 1px solid $border
      border-left none
    }
  }

</style>
