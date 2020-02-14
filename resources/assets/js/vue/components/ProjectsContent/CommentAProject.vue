<template>
  <div class="CommentAProject">
    <Border/>
    <div class="comment-a-project">
      <div
        v-if="!user.avatar.includes('/profile/default/avatar')"
        :style="{'background-image': `url(${user.avatar})`}"
        class="comment-a-project__avatar"
      />
      <Avatar
        v-if="user.avatar.includes('/profile/default/avatar')"
        :username="user.display_name"
        :size="26"
        custom-class="comment-a-project__avatar"
      />
      <form
        class="comment-a-project__form"
        @submit.prevent=""
      >
        <el-input
          v-model="comment.text"
          :autosize="{ minRows: 1 }"
          type="textarea"
          resize="none"
          placeholder="Please comment"
          class="comment-a-project__input"
          @focus="onFocus"
          @blur="ifBlur"
        />
        <button
          :class="[
            'comment-a-project__button',
            {'comment-a-project__button--is-active': commentLoading},
            {'comment-a-project__button--is-active': isActive}
          ]"
          @click="addComment"
        >
          <font-awesome-icon
            v-if="commentLoading"
            icon="spinner"
            spin
            fixed-width
            class="fa-icon fa-icon--invert"
          />
          <font-awesome-icon
            v-else
            :icon="['fas', 'paper-plane']"
            class="fa-icon fa-icon--invert"
          />
        </button>
        <template v-if="errors.commentText">
          <div
            v-for="(error, index) in errors.commentText"
            :key="index"
            class="comment-a-project-error comment-a-project-error--bottom"
            @click="skipError({
              key: 'commentText',
              index,
            })"
          >
            {{ error }}
          </div>
        </template>
      </form>
    </div>
  </div>
</template>

<script>
import Comment from 'Models/Comment';
import Avatar from 'Pages/ProfilePage/Avatar';
import Border from 'Pages/ProjectsContent/Border';

import CREATE_COMMENT from 'Gql/comments/mutations/createComment.graphql';
import FETCH_COMMENTS from 'Gql/comments/queries/fetchComments.graphql';
import FETCH_PROJECT from 'Gql/projects/queries/fetchProject.graphql';

export default {
  name: 'CommentAProject',
  components: {
    Border,
    Avatar,
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
  data() {
    return {
      comment: new Comment(),
      errors: [],
      commentLoading: false,
      isActive: false,
    };
  },
  methods: {
    onFocus() {
      this.isActive = true;
    },
    ifBlur() {
      this.isActive = false;
    },
    skipError() {
    },

    updateProjects(store, comment) {
      const fetchComments = {
        query: FETCH_COMMENTS,
        variables: {
          id: this.project.id,
        },
      };
      const data = store.readQuery(fetchComments);
      data.fetchComments.push(comment);
      store.writeQuery({ ...fetchComments, data });

      const fetchProject = {
        query: FETCH_PROJECT,
        variables: {
          id: this.project.id,
        },
      };
      const dataProjects = store.readQuery(fetchProject);
      dataProjects.fetchProject.comments.push(comment);
      dataProjects.fetchProject.commentsCount += 1;
      store.writeQuery({ ...fetchProject, data: dataProjects });
    },
    addComment() {
      this.comment.projectId = this.project.id;
      this.commentLoading = true;
      this.$apollo.mutate({
        mutation: CREATE_COMMENT,
        variables: {
          comment: this.comment,
        },
        update: (store, { data: { createComment: comment } }) => {
          this.updateProjects(store, comment);
        },
      })
        .then(() => {
          this.comment.text = '';
          this.commentLoading = false;
        })
        .catch(() => {
          this.commentLoading = false;
        });
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
    &:hover {
      color $text-lighter
    }
    &--invert {
      color $white
      &:hover {
        color $white-bis
      }
    }
    &--pointer {
      cursor pointer
    }
    &__box {
      padding 0 4px 0
    }
  }

  .comment-a-project-error {
    fnt($warning-invert, .75rem, $weight-normal, left)
    background-color $warning
    border 1px solid $warning
    border-radius 3px;
    box-shadow 0 2px 2px 0 rgba($black-bis, .16), 0 0 0 1px rgba($black-bis, .08)
    cursor pointer
    left 20px
    margin-bottom -6px
    padding 4px 12px
    position absolute
    transition box-shadow .3s
    width 80%
    z-index 2
    &--bottom {
      bottom -18px
      left 20px
    }
    &:hover {
      box-shadow 0 3px 8px 0 rgba($black-bis, 0.2), 0 0 0 1px rgba($black-bis, 0.08)
    }
  }

  .comment-a-project {
    display flex
    padding 18px 24px
    &__avatar {
      fl-center()
      background center center/cover no-repeat $grey-light
      border-radius 50%
      flex 0 0 auto
      height 26px
      margin 2px 16px 0 0
      width 26px
    }
    &__form {
      align-items flex-end
      border-radius 3px
      border 1px solid $border
      display flex
      position relative
      width 100%
    }
    &__input {
      margin-left 1px
    }

    &__button {
      fl-center()
      background-color $grey-lighter
      border none
      border-radius 2px
      cursor pointer
      flex 0 0 auto
      outline none
      transition all .3s
      width 40px
      height 26px
      margin 1px
      &:active {
        background-color $blue-hover
      }
      &--is-active {
        background-color $info
      }
    }
  }

</style>

<style lang="stylus">
  @import '../../../../sass/front/components/bulma-theme';

  .comment-a-project__form {
    .el-textarea__inner {
      fnt($text-light, 12px, $weight-normal, left)
      border : none;
      &::-webkit-input-placeholder {
        color $grey-lighter
        font-weight 300
      }
      &::-moz-placeholder {
        color $grey-lighter
        font-weight 300
      }
      &:-moz-placeholder {
        color $grey-lighter
        font-weight 300
      }
      &:-ms-input-placeholder {
        color $grey-lighter
        font-weight 300
      }
    }
  }
</style>

