<template>
  <div class="CommentFooter comment-footer">
    <div class="comment-likes__icon">
      <font-awesome-icon
        v-if="comment.id === clickedComment"
        icon="spinner"
        spin
        class="fa-icon"
      />
      <font-awesome-icon
        v-else
        :icon="['far', 'heart']"
        :class="{'fa-icon--liked': userReaction}"
        class="fa-icon fa-icon--pointer"
        @click="likeComment(comment)"
      />
    </div>
    <div
      v-if="comment.likes.length"
      class="comment-likes__number"
    >
      {{ comment.likes.length }}
    </div>
    <div
      v-if="comment.parentId === null"
      class="comment-likes__reply"
      @click="toggleRepliesVisibility(comment)"
    >
      <div class="fa-icon__box">
        <font-awesome-icon
          :icon="['fas', 'reply']"
          class="fa-icon"
        />
      </div>
      {{ trans('comments.reply') }}
      <span v-if="comment.repliesLength">
        ({{ comment.repliesLength }})
      </span>
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex';

import COMMENT_LIKE from 'Gql/comments/mutations/handleCommentLike.graphql';
import FETCH_COMMENTS from 'Gql/comments/queries/fetchComments.graphql';

export default {
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
  data() {
    return {
      clickedComment: null,
    };
  },
  computed: {
    userReaction() {
      return this.comment.likes.some(u => String(u.id) === String(this.user.id));
    },
  },
  methods: {
    ...mapActions('feed', [
      'toggleRepliesVisibility',
    ]),
    likeComment(comment) {
      this.clickedComment = comment.id;
      this.$apollo.mutate({
        mutation: COMMENT_LIKE,
        variables: {
          id: comment.id,
        },
        update: (store, { data: { handleCommentLike: user } }) => {
          const fetchComments = {
            query: FETCH_COMMENTS,
            variables: {
              id: comment.projectId,
            },
          };
          const data = store.readQuery(fetchComments);
          if (comment.parentId) {
            const key = data.fetchComments.findIndex(c => c.id === String(comment.parentId));
            const reply = data.fetchComments[key].replies;
            const rKey = reply.findIndex(c => c.id === comment.id);
            if (this.userReaction) {
              reply[rKey].likes = reply[rKey].likes
                .filter(u => u.id !== user.id);
            } else {
              reply[rKey].likes.push(user);
            }
          } else {
            const key = data.fetchComments.findIndex(c => c.id === comment.id);
            if (this.userReaction) {
              data.fetchComments[key].likes = data.fetchComments[key].likes
                .filter(u => u.id !== user.id);
            } else {
              data.fetchComments[key].likes.push(user);
            }
          }
          store.writeQuery({ ...fetchComments, data });
        },
      })
        .then(() => {
          this.clickedComment = null;
        })
        .catch(() => {
          this.clickedComment = null;
        });
    },
  },
};

</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .fa-icon
    color $text-light
    transition all .3s
    font-size 1rem
    &:hover
      color $text-lighter
    &--invert
      color $white
      &:hover
        color $white-bis
    &--light
      color $text-lighter
    &--pointer
      cursor pointer
    &--liked
      color $secondary
      &:hover
        color $coral-hover
    &--starred
      color $primary
      &:hover
        color $lime-hover
    &__box
      padding 0 4px 0

  .comment-footer
    fl-left()
    padding 0 26px 8px
    width 100%

  .comment-likes
    &__icon
      fl-center()
      cursor pointer
      flex 0 0 auto
      height 24px
      transition all .1s
      width 24px
    &__number
      fnt($text-light, 12px, $weight-semibold, left)
      padding 0 8px 0 4px
    &__reply
      fnt($text-light, 12px, $weight-normal, left)
      fl-left()
      cursor pointer
      transition all .3s
      &:hover
        color $text-lighter
        font-weight $weight-semibold
        & .fa-icon
          color $text-lighter

</style>
