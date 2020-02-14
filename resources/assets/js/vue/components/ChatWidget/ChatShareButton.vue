<template>
  <el-button
    plain
    size="small"
    class="chat-share__button"
    @click="shareToThread"
  >
    <font-awesome-icon
      v-if="shareIsPending"
      :icon="['fas', 'spinner']"
      spin
      class="fa-icon"
    />
    <font-awesome-icon
      v-if="!shareIsPending && sent"
      :icon="['fas', 'check']"
      class="fa-icon"
    />
    <span v-if="!shareIsPending && !sent">Send</span>
  </el-button>
</template>

<script>
export default {
  name: 'ChatShareButton',
  props: {
    pendingShares: {
      type: Array,
      default: () => [],
    },
    thread: {
      type: Object,
      default: () => {},
    },
    user: {
      type: Object,
      default: () => {},
    },
  },
  data() {
    return {
      sent: false,
    };
  },
  computed: {
    shareIsPending() {
      let pendingShare = {};
      if (this.thread) {
        pendingShare = this.pendingShares.find(sh => sh.threadId === this.thread.id);
      } else {
        pendingShare = this.pendingShares.find(sh => sh.userId === this.user.id);
      }

      return !!pendingShare;
    },
  },
  methods: {
    shareToThread() {
      if (this.sent || this.shareIsPending) return;
      this.sent = true;
      if (this.thread) {
        this.$emit('share-to-thread', this.thread);
      } else {
        this.$emit('share-to-user', this.user);
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

  .chat-share
    &__button
      width:54px;
</style>
