<template>
  <div
    class="ViewPreviousComments view-more-comments view-more-comments--last"
    @click="loadReplies(comment)"
  >
    <div class="view-more-comments__text">
      {{ trans('comments.view_previous') }}
    </div>
  </div>
</template>

<script>
/* @flow */
export default {
  components: {},
  props: {
    comment: {
      type: Object,
      default: () => ({}),
    },
  },
  methods: {
    loadReplies(comment) {
      this.$set(comment, 'repliesLoading', true);
      this.$http.post(comment.repliesNextUrl, {
        _token: window.Laravel.csrfToken,
        comment_id: comment.id,
      })
        .then((response) => {
          const replies = response.data.data.reverse();
          this.$set(comment, 'repliesLoading', false);
          this.comment.replies = replies.concat(this.comment.replies);
          this.$set(comment, 'repliesNextUrl', response.data.next_page_url);
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

  .view-more-comments {
    align-items      : center;
    background-color : $background-light;
    cursor           : pointer;
    display          : flex;
    height           : 36px;
    justify-content  : center;
    transition       : all .3s;
    width            : 100%;
    &--last {
      //border-bottom: 1px solid $border;
      &:hover {
        //border-bottom: 1px solid $white-ter;
      }
    }
    &:hover {
      background-color : $primary;
    }
    &:hover &__text {
      color : $white-ter;
    }

    &__text {
      color       : $text;
      font-size   : 12px;
      font-weight : $weight-semibold;
    }
  }
</style>
