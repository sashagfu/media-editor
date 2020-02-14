<template>
  <div
    class="ViewMoreComments view-more-comments view-more-comments--last"
    @click="loadComments(post)"
  >
    <div class="view-more-comments__text">
      {{ trans('comments.view_more') }}
    </div>
  </div>
</template>

<script>
/* @flow */
export default {
  components: {},
  props: {
    post: {
      type: Object,
      default: () => ({}),
    },
  },
  methods: {
    loadComments(post) {
      this.$set(post, 'commentLoading', true);
      this.$http.post(post.commentsNextUrl, {
        _token: window.Laravel.csrfToken,
        post_id: post.id,
      })
        .then((response) => {
          this.$set(post, 'commentLoading', false);
          this.post.comments = this.post.comments.concat(response.data.data);
          this.$set(post, 'commentsNextUrl', response.data.next_page_url);
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
