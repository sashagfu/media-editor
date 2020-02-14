<template>
  <div class="ShowComments sh-comments">
    <div
      class="sh-comments__icon"
      @click="showComments(project)"
    >
      <font-awesome-icon
        :icon="['far', 'comments']"
        class="fa-icon"
      />
    </div>
    <div
      class="sh-comments__number"
    >
      {{ project.comments.length }}
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex';

export default {
  name: 'ShowComments',
  props: {
    project: {
      type: Object,
      default: () => ({}),
    },
  },
  methods: {
    ...mapActions('feed', [
      // 'setActivePost',
      'toggleCommentVisibility',
      // 'setComment',
    ]),
    showComments(project) {
      this.toggleCommentVisibility(project);
      // this.setActivePost(project);
      // if (project.commentVisibility && !project.comments) {
      //     this.setComment(project);
      // }
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

  .sh-comments
    fl-left()
    height 100%
    &__number
      fnt($text-light, 12px, $weight-semibold, left)
      padding 0 8px
      height 100%
      display flex
      align-items center

</style>
