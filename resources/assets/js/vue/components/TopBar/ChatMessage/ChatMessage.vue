<template>
  <div
    class="ChatMessage chat"
    @click="toggleRightSidebar"
  >
    <div class="chat__box">
      <div class="fa-icon__box">
        <font-awesome-icon
          :icon="['far', 'comments']"
          class="fa-icon"
        />
      </div>
      <div
        v-if="unreadMessagesCount"
        class="chat__tag"
      >
        {{ unreadMessagesCount }}
      </div>
    </div>
    <!-- <div class="chat__dropdown-menu">
      <ChatMessageDropdownMenu
        :loading="$apollo.queries.fetchThreads.loading"
        :threads="fetchThreads"
      />
    </div> -->
  </div>
</template>

<script>
import FETCH_THREADS from 'Gql/messages/queries/fetchThreads.graphql';

import ChatMessageDropdownMenu from './ChatMessageDropdownMenu';

export default {
  name: 'ChatMessage',
  components: {
    ChatMessageDropdownMenu,
  },
  data() {
    return {
      loadingThreads: false,
      fetchThreads: [],
    };
  },
  computed: {
    unreadMessagesCount() {
      let unreadMessagesCount = 0;
      this.fetchThreads.forEach((th) => {
        unreadMessagesCount += th.unreadMessagesCount;
      });
      return unreadMessagesCount;
    },
  },
  methods: {
    toggleRightSidebar() {
      this.$bus.$emit('toggle-right-sidebar', true);
    },
  },
  apollo: {
    fetchThreads: {
      query: FETCH_THREADS,
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
    fnt($text-light, 1.5rem, $weight-light, center)
    transition all .3s
    &__box {
      fl-center()
      size(24, 24)
      flex 0 0 auto
    }
  }

  .chat {
    align-items flex-start
    cursor pointer
    display flex
    flex-direction column
    position relative
    &__tag {
      fnt($text-invert, 10px, 700, center)
      fl-center()
      circle(19)
      background-color $primary
      position absolute
      right 12px
      top 12px
    }
    &__item {
      position relative
    }
    &__dropdown-menu {
      align-items flex-start
      box-shadow 0px 0px 20px 0px rgba($grey-darker, .1)
      display none
      flex-direction: column
      left -172px
      position absolute
      top 56px
      width 380px
      z-index 1
    }
    &:hover .fa-box__icon {
      color $text
    }
    &:hover &__dropdown-menu {
      display flex
    }
    &__box {
      fl-center()
      size(72, 62)
      position relative
      transition all .3s
    }
  }

</style>
