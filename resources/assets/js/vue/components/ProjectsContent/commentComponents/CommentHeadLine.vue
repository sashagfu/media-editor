<template>
  <div class="CommentHeadLine header">
    <div class="header__left">
      <div
        v-if="!comment.author.avatar.includes('/profile/default/avatar')"
        :style="{'background-image': `url(${ comment.author.avatar })`}"
        class="header__avatar level-item"
      />
      <Avatar
        v-if="comment.author.avatar.includes('/profile/default/avatar')"
        :username="comment.author.displayName"
        :size="26"
        custom-class="header__avatar"
      />
      <div class="header__title">
        <div
          class="header__text"
          @click="onProfileClick"
        >
          <span
            class="header__text header__text--strong"
          >
            {{ comment.author.displayName }}
          </span>
          {{ trans('comments.wrote_a') }} {{ trans('comments.comment') }}
        </div>
        <div class="header__date">
          {{ comment.createdAtDiff }}
        </div>
      </div>
    </div>
    <div class="header__right">
      <div v-if="isMine">
        <div class="header__setting-box level-item">
          <div class="header__settings">
            <font-awesome-icon
              :icon="['fas', 'ellipsis-h']"
              class="fa-icon fa-icon--pointer"
            />
          </div>
          <div class="header__dropdown">
            <div
              class="header__menu-item"
              @click="deleteComment(comment)"
            >
              {{ trans('comments.delete_comment') }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Avatar from 'Pages/ProfilePage/Avatar';

import DELETE_COMMENT from 'Gql/comments/mutations/deleteComment.graphql';
import FETCH_COMMENTS from 'Gql/comments/queries/fetchComments.graphql';
import FETCH_PROJECT from 'Gql/projects/queries/fetchProject.graphql';

export default {
  name: 'CommentHeadLine',
  components: {
    Avatar,
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
    isMine() {
      return String(this.comment.author.id).toLowerCase() === String(this.user.id).toLowerCase();
    },
  },
  methods: {
    onProfileClick() {
      window.location = `/profile/${this.comment.author.username}`;
    },
    deleteComment({ id }) {
      this.$apollo.mutate({
        mutation: DELETE_COMMENT,
        variables: {
          id,
        },
        update: (store, { data: { deleteComment } }) => {
          const fetchComments = {
            query: FETCH_COMMENTS,
            variables: {
              id: deleteComment.projectId,
            },
          };
          const data = store.readQuery(fetchComments);
          if (deleteComment.parentId) {
            const key = data.fetchComments.findIndex(c => c.id === String(deleteComment.parentId));
            if (key !== -1) {
              data.fetchComments[key].replies = data.fetchComments[key].replies
                .filter(r => r.id !== deleteComment.id);
              data.fetchComments[key].repliesLength -= 1;
              store.writeQuery({ ...fetchComments, data });
            }
          } else {
            data.fetchComments = data.fetchComments.filter(c => c.id !== deleteComment.id);
            store.writeQuery({ ...fetchComments, data });
          }
          if (!deleteComment.parentId) {
            const fetchProject = {
              query: FETCH_PROJECT,
              variables: {
                id: deleteComment.projectId,
              },
            };
            const dataProject = store.readQuery(fetchProject);
            dataProject.fetchProject.comments = dataProject.fetchProject.comments
              .filter(c => c.id !== deleteComment.id);
            store.writeQuery({ ...fetchProject, data: dataProject });
          }
        },
      })
        .then(() => {
          this.$notify({
            title: 'Success',
            message: 'The Comment has been deleted',
            type: 'success',
          });
        })
        .catch(() => {
          this.$notify.error({
            title: 'Error',
            message: 'Comment has`t been deleted',
          });
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
    &:hover
      color $text-lighter
    &--invert
      color $white
      &:hover
        color $white-bis
    &--pointer
      cursor pointer
    &__box
      padding 0 4px 0

  .header
    fl-between()
    padding 12px 24px 8px
    &__left
      fl-left()
    &__right
      fl-right()
    &__avatar
      fl-center()
      background center center/cover no-repeat $grey-light
      border-radius 50%
      flex 0 0 auto
      height 26px
      margin-right 16px
      width 26px
    &__title
      display flex
      flex-direction column
      align-items flex-start
    &__text
      fnt($text-light, 12px, $weight-semibold, left)
      cursor pointer
      &--strong
        color $text
        font-weight $weight-semibold + 100
      &--em
        color $primary
    &__date
      fnt($primary, 11px, $weight-normal, left)
    &__settings
      size(40, 40)
      cursor pointer
    &__setting-box
      position relative
      width 40px
    &__setting-box:hover &__dropdown
      display flex
    &__dropdown
      box-shadow 0 2px 2px 0 rgba($black-bis, .16), 0 0 0 1px rgba($black-bis, .08)
      background-color $white
      display none
      border-radius $radius
      flex-direction column
      min-width 100px
      padding 6px 12px
      position absolute
      right 12px
      top 20px
      z-index 5
      &:hover
        box-shadow 0 3px 8px 0 rgba($black-bis, 0.2), 0 0 0 1px rgba($black-bis, 0.08)
    &__menu-item
      fnt($text-light, 12px, $weight-normal, left)
      cursor pointer
      white-space nowrap
      &:hover
        color $primary
        font-weight $weight-semibold

</style>
