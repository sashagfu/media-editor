<template>
  <div class="CommentAComment comment-a-project">
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
      @submit.prevent="addReply(comment)"
    >
      <el-input
        v-model="reply.text"
        :autosize="{ minRows: 1 }"
        type="textarea"
        resize="none"
        placeholder="Please enter to comment..."
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
          v-for="(error, key) in errors.commentText"
          :key="key"
          class="comment-a-project-error comment-a-project-error--bottom"
          @click="skipError('comment_text', key)"
        >
          {{ error }}
        </div>
      </template>
    </form>
  </div>
</template>
<script>
import Avatar from 'Pages/ProfilePage/Avatar';
import CREATE_COMMENT from 'Gql/comments/mutations/createComment.graphql';
import FETCH_COMMENTS from 'Gql/comments/queries/fetchComments.graphql';
import Comment from 'Models/Comment';

export default {
  name: 'CommentAComment',
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
  data() {
    return {
      reply: new Comment(),
      commentLoading: false,
      errors: [],
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
    skipError(key, index) {
      this.errors[key].splice(index, 1);
    },
    addReply() {
      this.reply.projectId = this.comment.projectId;
      this.reply.parentId = this.comment.id;
      this.commentLoading = true;
      this.$apollo.mutate({
        mutation: CREATE_COMMENT,
        variables: {
          comment: this.reply,
        },
        update: (store, { data: { createComment } }) => {
          const newComment = Object.assign({}, createComment);
          const fetchComments = {
            query: FETCH_COMMENTS,
            variables: {
              id: this.comment.projectId,
            },
          };
          const data = store.readQuery(fetchComments);
          const key = data.fetchComments.findIndex(c => c.id === this.comment.id);
          if (key !== -1) {
            data.fetchComments[key].replies.push(newComment);
            data.fetchComments[key].repliesLength += 1;
            store.writeQuery({ ...fetchComments, data });
          }
        },
      })
        .then(() => {
          this.reply.text = '';
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
    color     : $text-light;
    font-size : 1rem;
    &:hover {
      color : $text-lighter;
    }
    &--invert {
      color : $white;
      &:hover {
        color : $white-bis;
      }
    }
    &--pointer {
      cursor : pointer;
    }
    &__box {
      padding : 0 4px 0;
    }
  }

  .comment-a-project-error {
    fnt($warning-invert, .75rem, $weight-normal, left);
    background-color : $warning;
    border           : 1px solid $warning;
    border-radius    : 3px;
    box-shadow       : 0 2px 2px 0 rgba($black-bis, .16), 0 0 0 1px rgba($black-bis, .08);
    cursor           : pointer;
    left             : 20px;
    margin-bottom    : -6px;
    padding          : 4px 12px;
    position         : absolute;
    transition       : box-shadow .3s;
    width            : 80%;
    z-index          : 2;
    &--bottom {
      bottom : -18px;
      left   : 20px;
    }
    &:hover {
      box-shadow : 0 3px 8px 0 rgba($black-bis, 0.2), 0 0 0 1px rgba($black-bis, 0.08);
    }
  }

  .comment-a-project {
    display : flex;
    padding : 18px 24px;
    &__avatar {
      fl-center();
      background    : center center/cover no-repeat $grey-light;
      border-radius : 50%;
      flex          : 0 0 auto;
      height        : 26px;
      margin        : 2px 16px 0 0;
      width         : 26px;
    }
    &__form {
      align-items      : flex-end;
      background-color : $white;
      border           : 1px solid $border;
      border-radius    : 3px;
      display          : flex;
      position         : relative;
      width            : 100%;
    }
    &__input {
      margin-left : 1px;
    }
    &__button {
      fl-center();
      background-color : $grey-lighter;
      border           : none;
      border-radius    : 2px;
      cursor           : pointer;
      flex             : 0 0 auto;
      height           : 26px;
      margin           : 1px;
      outline          : none;
      transition       : all .3s;
      width            : 40px;
      &:active {
        background-color : $blue-hover;
      }
      &--is-active {
        background-color : $info;
      }
    }
  }

</style>

<style lang="stylus">
  @import '../../../../sass/front/components/bulma-theme';

  .comment-a-project__form {
    .el-textarea__inner {
      fnt($text-light, 12px, $weight-normal, left);
      border : none;
      &::-webkit-input-placeholder {
        color       : $grey-lighter;
        font-weight : 300;
      }
      &::-moz-placeholder {
        color       : $grey-lighter;
        font-weight : 300;
      }
      &:-moz-placeholder {
        color       : $grey-lighter;
        font-weight : 300;
      }
      &:-ms-input-placeholder {
        color       : $grey-lighter;
        font-weight : 300;
      }
    }
  }
</style>
