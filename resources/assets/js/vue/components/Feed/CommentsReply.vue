<template>
  <div class="CommentsReply">
    <div class="comments-replay">
      <div class="comments-replay__left-column">
        <div class="comments-replay__ring"/>
      </div>
      <div class="comments-replay__right-column">
        <CommentHeadLine
          :comment="reply"
          :user="user"
        />
        <CommentMain :text="reply.text"/>
        <CommentFooter
          :comment="reply"
          :user="user"
        />
      </div>
    </div>
  </div>
</template>

<script>
/* @flow */
import CommentHeadLine from './commentComponents/CommentHeadLine';
import CommentMain from './commentComponents/CommentMain';
import CommentFooter from './commentComponents/CommentFooter';
import Border from './commentComponents/Border';

export default {
  components: {
    CommentHeadLine,
    CommentMain,
    CommentFooter,
    Border,
  },
  props: {
    parent: {
      type: Object,
      default: () => ({}),
    },
    reply: {
      type: Object,
      default: () => ({}),
    },
    user: {
      type: Object,
      default: () => ({}),
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .comments-replay {
    background-color : $background;
    display          : flex;
    &__left-column {
      border-right : 1px solid $border;
      position     : relative;
      width        : 40px;
    }
    &__ring {
      background-color : $background;
      border           : 2px solid $border;
      border-radius    : 100%;
      flex             : 0 0 auto;
      height           : 9px;
      position         : absolute;
      right            : -5px;
      top              : 28px;
      width            : 9px;
    }
    &__right-column {
      width : 100%;
    }
  }
</style>
