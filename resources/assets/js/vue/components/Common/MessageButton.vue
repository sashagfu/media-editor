<template>
  <div
    :title="trans('profile_page.send_message')"
    :class="[
      'MessageButton',
      'msg-btn',
      {'msg-btn--round': round}
    ]"
    @click="chatOpen"
  >
    <font-awesome-icon
      v-if="threadLoading"
      :icon="['fas', 'spinner']"
      spin
      class="fa-icon"
    />
    <font-awesome-icon
      v-else
      :icon="['fas', 'envelope']"
      class="fa-icon"
    />
    <div
      v-if="!round"
      class="msg-btn__text"
    >
      {{ showMessage }}
    </div>
  </div>
</template>

<script>
import FETCH_THREADS from 'Gql/messages/queries/fetchThreads.graphql';

import CREATE_THREAD from 'Gql/messages/mutations/createThread.graphql';

export default {
  name: 'MessageButton',
  props: {
    round: {
      type: Boolean,
      default: false,
    },
    user: {
      type: Object,
      default: () => ({}),
    },
    message: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      threadLoading: false,
    };
  },
  computed: {
    showMessage() {
      if (this.message) {
        return this.message;
      }
      return this.trans('profile_page.send_message');
    },
  },
  methods: {
    async chatOpen() {
      try {
        this.threadLoading = true;
        const { data: { createThread } } = await this.$apollo.mutate({
          mutation: CREATE_THREAD,
          variables: {
            targetId: this.user.id,
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
        this.threadLoading = false;
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

  .fa-icon
    fnt($blue, 11px, $weight-light, left)

  .msg-btn
    fl-center()
    border-radius 9px
    border 1px solid $info
    cursor pointer
    height 18px
    transition all .3s
    width 120px
    &--round
      border-radius 50%
      height 26px
      width 26px
    &:hover
      opacity 0.5
    &__text
      fnt($info, 12px, $weight-normal, center)
      padding-left 8px

</style>
