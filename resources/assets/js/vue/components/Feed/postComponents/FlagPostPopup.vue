<template>
  <div class="FlagPostPopup flag-post-popup">
    <div
      :class="{'is-active': flagModalActive}"
      class="modal"
    >
      <div class="modal-background"/>
      <div class="modal-content">
        <div class="flag-post box">
          <head-line :header="header"/>
          <div class="flag-post__main">
            <div class="column is-12">
              <p>
                {{ trans('flags.reason_select') }}
              </p>
            </div>
            <div
              v-for="(reason, key) in flagReasons"
              :key="key"
              class="field flag-post__field"
            >
              <p class="control">
                <label
                  :for="`reason_${reason.id}`"
                  class="radio"
                >
                  <input
                    v-model="flagReason"
                    :id="`reason_${reason.id}`"
                    :value="reason.id"
                    type="radio"
                  >
                  {{ reason.title }}
                </label>
              </p>
            </div>
            <template v-if="errors.reason_id">
              <p
                v-for="(error, key) in errors.reason_id"
                :key="key"
                class="help is-danger comment-a-post-error"
              >
                {{ error }}
              </p>
            </template>
            <div class="field flag-post__field">
              <label class="label">
                {{ trans('flags.reason_comment') }}
              </label>
              <p class="control">
                <textarea
                  v-model="reasonComment"
                  rows="3"
                  class="textarea flag-post__comment"
                />
              </p>
            </div>
            <template v-if="errors.reason_comment">
              <p
                v-for="(error, key) in errors.reason_comment"
                :key="key"
                class="help is-danger comment-a-post-error"
              >
                {{ error }}
              </p>
            </template>
            <button
              class="flag-post__main-button flag-post__create-button"
              @click="flagPost"
            >
              {{ trans('posts.flag_post') }}
            </button>
            <button
              class="flag-post__main-button"
              @click="closePopUp"
            >
              {{ trans('common.cancel') }}
            </button>
          </div>
          <div
            v-if="flagLoading"
            class="flag-post__loading"
          >
            <i
              class="fa fa-spinner fa-spin fa-3x fa-fw
                                  has-text-centered
                                  flag-post__loading-loader"
            />
          </div>
          <button
            class="modal-close"
            @click="closePopUp"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>

import { mapActions, mapGetters } from 'vuex';
import HeadLine from '../../HeadLine/HeadLine';

const FlagPostPopup = {
  name: 'flag-post-popup',
  components: {
    HeadLine,
  },
  props: {
    flagModalActive: {
      type: Boolean,
      default: false,
    },
    flagReasons: {
      type: Array,
      default() {
        return [];
      },
    },
  },
  computed: {
    ...mapGetters('feed', [
      'feedItems',
      'activePost',
    ]),
  },
  data() {
    return {
      header: this.trans('posts.flag_post'),
      flagLoading: false,
      errors: [],
      flagReason: 1,
      reasonComment: '',
    };
  },
  methods: {
    ...mapActions('feed', [
      'setToggleFlagModal',
    ]),
    closePopUp() {
      this.setToggleFlagModal(false); // $store.dispatch
    },
    flagPost() {
      this.flagLoading = !this.flagLoading;
      this.$http.post('/api/flag/create', {
        _token: window.Laravel.csrfToken,
        reason_id: this.flagReason,
        reason_comment: this.reasonComment,
        post_id: this.activePost.id,
      }).then(() => {
        this.flagLoading = !this.flagLoading;
        this.feedItems.data.splice(this.feedItems.data.indexOf(this.activePost), 1);
        this.setToggleFlagModal(false); // $store.dispatch
      }).catch((error) => {
        this.flagLoading = !this.flagLoading;
        this.errors = error.response.data;
      });
    },
  },
};

export default FlagPostPopup;
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .flag-post {
    &__loading {
      justify-content : center;
      align-items     : center;
    }
    &:hover {
      color : #4a4a4a;
    }
    &__main {
      min-height : 410px;
      padding    : 20px;
      &-button {
        cursor           : pointer;
        margin-top       : 10px;
        height           : 34px;
        border           : none;
        float            : right;
        background-color : $primary;
        margin-right     : 10px;
        color            : $white;
      }
    }
    &__field {
      margin : 10px 0;
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
