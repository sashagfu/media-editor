<template>
  <div class="DeleteCommentPopup delete-comment-popup">
    <div
      :class="{'is-active': deleteModalActive}"
      class="modal"
    >
      <div class="modal-background"/>
      <div class="modal-content">
        <div class="delete-post box">
          <div class="delete-post__main">
            <p>
              {{ trans('comments.delete_comment_warn') }}
            </p>
          </div>
          <div class="delete-post__footer">
            <button
              class="delete-post__button delete-post__button--cancel"
              @click="closePopUp"
            >
              {{ trans('common.cancel') }}
            </button>
            <button
              :class="{'delete-post__button--danger': deleteLoading}"
              class="delete-post__button delete-post__button--delete"
              @click="deleteComment"
            >
              {{ trans('comments.delete_comment') }}
              <div
                v-if="deleteLoading"
                class="fa-icon__box"
              >
                <font-awesome-icon
                  :class="{'fa-icon--danger': deleteLoading}"
                  icon="spinner"
                  spin
                  class="fa-icon fa-icon--invert"
                />
              </div>
            </button>
          </div>
          <!-- <p v-if="error">
              {{ error }}
          </p> -->
          <button
            class="modal-close"
            @click="closePopUp()"
          />
        </div>
      </div>
    </div>
  </div>
</template>
<script>
/* @flow */
import { mapGetters, mapActions } from 'vuex';
import HeadLine from '../../HeadLine/HeadLine';

export default {
  components: {
    HeadLine,
  },
  props: {
    deleteModalActive: {
      type: Boolean,
      required: true,
    },
  },
  computed: {
    ...mapGetters('feed', [
      'activeComment',
      'deleteLoading',
      'error',
    ]),
  },
  methods: {
    ...mapActions('feed', [
      'setToggleCommentDeleteModal',
      'deleteComment',
    ]),
    closePopUp() {
      this.setToggleCommentDeleteModal(false);
    },
  },
};

</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .fa-icon {
    color      : $text-light;
    transition : all .3s;
    font-size  : 1rem;
    &--invert {
      color : $text-invert;
    }
    &--danger {
      color : $danger;
    }
    &__box {
      padding : 0 4px;
    }
  }

  .delete-post {
    display        : flex;
    flex-direction : column;
    height         : 200px;
    &__main {
      fl-center();
      flex            : 1 1 auto;
    }
    &__footer {
      flex            : 0 0 auto;
      display         : flex;
      justify-content : flex-end;
      padding         : 12px 24px;
    }
    &__button {
      fnt($text-invert, 12px, $weight-normal, center);
      fl-center();
      cursor          : pointer;
      outline         : none;
      //height: 34px;
      padding         : 8px 12px;
      border-radius   : $radius;
      transition      : all .2s;
      &--cancel {
        background-color : $grey;
        border           : 1px solid $grey;
        &:hover {
          background-color : $white;
          color            : $grey;
        }
      }
      &--delete {
        background-color : $danger;
        border           : 1px solid $danger;
        &:hover {
          background-color : $white;
          color            : $danger;
        }
      }
      &--danger {
        background-color : $white;
        color            : $danger;
      }
    }
    &__button + &__button {
      margin-left : 12px;
    }
  }

  .modal-content {
    overflow : visible;
    width    : 470px;
  }

  .modal-close {
    position : absolute;
    right    : -48px;
    top      : -48px;
  }
</style>
