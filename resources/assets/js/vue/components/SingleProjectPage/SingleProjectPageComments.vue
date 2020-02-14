<template>
  <div class="SingleProjectPageComments box spc">
    <div class="spc__headline">
      <div class="left">
        <div class="spc__headline-title">
          {{ `${trans('comments.comments')} (${project.comments.length})` }}
        </div>
      </div>
      <div class="fl-right" />
    </div>
    <div class="spc__main">
      <div
        v-if="$apollo.queries.fetchComments.loading"
        class="spc__loading"
      >
        <font-awesome-icon
          :icon="['fas', 'spinner']"
          spin
          class="fa-icon"
        />
      </div>
      <div
        v-else-if="!fetchComments.length"
        class="spc__empty"
      >
        {{ trans('comments.any_comments') }}
      </div>
      <Comment
        v-for="(comment, key) in fetchComments"
        v-else
        :key="`com-${key}`"
        :user="user"
        :comment="comment"
      />

    </div>
    <div class="spc__bottom">
      <CommentAProject
        :project="project"
        :user="user"
      />
    </div>
  </div>
</template>

<script>
import Comment from 'Pages/ProjectsContent/Comment';
import CommentAProject from 'Pages/ProjectsContent/CommentAProject';

import FETCH_COMMENTS from 'Gql/comments/queries/fetchComments.graphql';

export default {
  name: 'SingleProjectPageComments',
  components: {
    Comment,
    CommentAProject,
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

  .fa-icon
    fnt($grey-light, 18px, $weight-semibold, center)

  .spc // Single Pages comments
    display flex
    flex-direction column
    &__headline
      fl-between()
      border-bottom 1px solid $border
      height 48px
      padding 0 26px
    &__headline-title
      fnt($text, 14px, $weight-semibold, left)
    &__loading
      fl-center()
      height 80px
    &__empty
      fl-center()
      fnt($grey-lighter, 18px, $weight-semibold, center)
      height 80px

</style>
