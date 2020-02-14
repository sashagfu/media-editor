<template>
  <div class="Comment comment">
    <CommentHeadLine
      :comment="comment"
      :user="user"
    />
    <CommentMain
      :text="comment.text"
    />
    <CommentFooter
      :comment="comment"
      :user="user"
    />
    <font-awesome-icon
      v-if="comment.repliesLoading"
      icon="spinner"
      spin
      class="fa-icon"
    />
    <ViewPreviousComments
      v-if="comment.repliesNextUrl"
      :comment="comment"
    />
    <template v-if="repliesIsVisibility">
      <CommentsReply
        v-for="reply in comment.replies"
        :key="reply.id"
        :reply="reply"
        :parent="comment"
        :user="user"
      />
    </template>
    <CommentAComment
      v-if="repliesIsVisibility"
      v-bind="$props"
    />
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import CommentHeadLine from './commentComponents/CommentHeadLine';
import CommentMain from './commentComponents/CommentMain';
import CommentFooter from './commentComponents/CommentFooter';
import CommentsReply from './CommentsReply';
import CommentAComment from './CommentAComment';
import ViewPreviousComments from './commentComponents/ViewPreviousComments';

export default {
  components: {
    CommentHeadLine,
    CommentMain,
    CommentFooter,
    CommentsReply,
    CommentAComment,
    ViewPreviousComments,
  },
  props: {
    post: {
      type: Object,
      default: () => ({}),
    },
    comment: {
      type: Object,
      default: () => ({}),
    },
    user: {
      type: Object,
      default: () => ({}),
    },
  },
  computed: {
    ...mapGetters('feed', [
      'repliesVisibility',
    ]),
    repliesIsVisibility() {
      return this.repliesVisibility.find(i => i === this.comment.id);
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .comment {
    background-color : $background;
  }

  .fa-icon {
    color      : $text-light;
    transition : all .3s;
    font-size  : 1rem;
  }
</style>
