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
    <template v-if="repliesIsVisibility">
      <CommentsReply
        v-for="(reply, key) in comment.replies"
        :key="`rep-${key}`"
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
import CommentAComment from 'Pages/ProjectsContent/CommentAComment';
import CommentFooter from 'Pages/ProjectsContent/commentComponents/CommentFooter';
import CommentHeadLine from 'Pages/ProjectsContent/commentComponents/CommentHeadLine';
import CommentMain from 'Pages/ProjectsContent/commentComponents/CommentMain';
import CommentsReply from 'Pages/ProjectsContent/CommentsReply';

export default {
  name: 'Comment',
  components: {
    CommentAComment,
    CommentFooter,
    CommentHeadLine,
    CommentMain,
    CommentsReply,
  },
  props: {
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
    background-color $white
  }
  .fa-icon {
    color $text-light
    transition all .3s
    font-size 1rem
  }
</style>
