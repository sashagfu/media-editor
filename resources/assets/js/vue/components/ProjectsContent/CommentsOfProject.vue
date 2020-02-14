<template>
  <div class="CommentsOfProject comments">
    <div
      v-if="$apollo.queries.fetchComments.loading"
      class="comments__loading"
    >
      <font-awesome-icon
        icon="spinner"
        spin
        class="fa-icon"
      />
    </div>
    <Comment
      v-for="(comment, key) in fetchComments"
      v-else
      :key="`com-${key}`"
      :user="user"
      :comment="comment"
    />
  </div>
</template>

<script>
import Comment from 'Pages/ProjectsContent/Comment';

import FETCH_COMMENTS from 'Gql/comments/queries/fetchComments.graphql';

export default {
  name: 'CommentsOfProject',
  components: {
    Comment,
  },
  props: {
    project: {
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
          id: this.project.id,
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
    color $text-light
    font-size 1rem
    margin 12px
  }
  .comments {
    &__loading {
      fl-center()
      width 100%
      height 80px
    }
  }

</style>
