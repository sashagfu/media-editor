<template>
  <div class="CommentsOfPost comments">
    <font-awesome-icon
      v-if="$apollo.queries.fetchComments.loading"
      icon="spinner"
      spin
      class="fa-icon"
    />
    <Comment
      v-for="comment in fetchComments"
      v-else
      :key="comment.id"
      :post="post"
      :user="user"
      :comment="comment"
    />
  </div>
</template>

<script>
import FETCH_COMMENTS from 'Gql/comments/queries/fetchComments.graphql';
import Comment from './Comment';

export default {
  name: 'CommentsOfPost',
  components: {
    Comment,
  },
  props: {
    post: {
      type: Object,
      default: () => ({}),
    },
    user: {
      type: Object,
      default: () => ({}),
    },
  },
  apollo: {
    fetchComments: {
      query: FETCH_COMMENTS,
      variables() {
        return {
          id: this.post.id,
        };
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

  .fa-icon {
    color     : $text-light;
    font-size : 1rem;
    margin    : 12px;
  }
</style>
