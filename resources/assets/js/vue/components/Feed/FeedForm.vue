<template>
  <div class="FeedForm box">
    <form
      id="postForm"
      ref="postForm"
      enctype="multipart/form-data"
      @submit.prevent="addPost()"
    >
      <div class="column is-12">
        <div class="create-post">
          <Headline :header="trans('posts.new_to_share')"/>
          <div class="create-post__content">
            <at
              :members="mentionUsers"
              name-key="username"
            >
              <template
                slot="item"
                slot-scope="s"
              >
                <img :src="s.item.avatar">
                <span v-text="s.item.display_name"/>
              </template>
              <div
                ref="postContent"
                class="create-post__contenteditable"
                contenteditable="true"
                @keydown="hashtagParser"
              />
            </at>
            <template v-if="errors.post_content">
              <span
                v-for="(error, key) in errors.post_content"
                :key="key"
                class="form-error  form-error--top flags-modal__error is-visible"
                @click="skipError('post_content', key)"
              >
                {{ error }}
              </span>
            </template>
          </div>
          <div class="create-post__footer">
            <div class="create-post__left">
              <a
                class="button
                                      create-post__button
                                      create-post__button--first-child
                                      create-post__button--icon"
                @click="clickInput"
              >
                <font-awesome-icon
                  icon="camera-retro"
                  class="fa-icon fa-icon--pointer"
                />
              </a>
              <a
                class="button
                                      create-post__button
                                      create-post__button--icon"
                @click="toggleVideoModal"
              >
                <font-awesome-icon
                  icon="film"
                  class="fa-icon fa-icon--pointer"
                />
              </a>
            </div>
            <div class="create-post__right">
              <button
                class="button
                                           create-post__button
                                           create-post__button--primary"
              >
                {{ trans('posts.share') }}
                <div
                  v-if="postCreatingLoading"
                  class="fa-icon__box"
                >
                  <font-awesome-icon
                    icon="spinner"
                    spin
                    class="fa-icon fa-icon--invert"
                  />
                </div>
              </button>
              <input
                ref="imagesInput"
                type="file"
                class="is-hidden"
                multiple="multiple"
                name="images"
              >
            </div>
            <template v-if="errors.video_id">
              <p
                v-for="(error, key) in errors.video_id"
                :key="key"
                class="form-error form-error--bottom flags-modal__error is-visible"
                @click="skipError('video_id', key)"
              >
                {{ error }}
              </p>
            </template>
          </div>
        </div>
      </div>
    </form>
    <div
      :class="{'is-active': videoModalActive}"
      class="modal videos-modal"
    >
      <div class="modal-background"/>
      <div class="modal-content videos-content">
        <font-awesome-icon
          v-if="videosLoading"
          icon="spinner"
          spin
          fixed-width
          class="fa-icon fa-icon--invert"
          size="2x"
        />
        <div
          v-else
          class="columns"
        >
          <template v-if="videos">
            <div
              v-for="(video, key) in videos"
              :key="key"
              class="column is-4"
            >
              <div
                :class="{ active: selectedVideo.id === video.id }"
                class="videos-content__item"
              >
                <div class="videos-content__img-wrapper">
                  <a
                    class="button videos-content__select-btn"
                    @click="selectVideo(video)"
                  >
                    {{ trans('common.select') }}
                  </a>
                  <a>
                    <img
                      :src="video.file_path"
                      class="videos-content__img"
                    >
                  </a>
                </div>
                <p class="videos-content__text">
                  {{ trans('common.created_at') }} : {{ video.createdAtDiff }}
                </p>
              </div>
            </div>
          </template>
          <div v-else>
            <p>
              {{ trans('posts.no_video') }}
            </p>
          </div>
        </div>
      </div>
      <button
        class="modal-close"
        @click="toggleVideoModal"
      />
    </div>
  </div>
</template>

<script>
import At from 'vue-at';
import { mapGetters, mapActions } from 'vuex';
import Headline from '../HeadLine/HeadLine';

export default {
  components: {
    Headline,
    At,
  },
  props: {
    type: {
      type: String,
      default: '',
    },
    circle: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      selectedVideo: false,
      postCreatingLoading: false,
      errors: [],
      hashtag: false,
    };
  },
  computed: {
    ...mapGetters('feed', [
      'mentionUsers',
      'videosLoading',
      'videoModalActive',
      'videos',
    ]),
  },
  mounted() {
    this.setMentionUsers();
  },
  methods: {
    ...mapActions('feed', [
      'setMentionUsers',
      'addNewFeedItem',
      'toggleVideoModal',
    ]),
    skipError(key, index) {
      this.errors[key].splice(index, 1);
    },
    selectVideo(video) {
      this.selectedVideo = video;
    },
    clickInput() {
      this.$refs.imagesInput.click();
    },
    hashtagParser(event) {
      if (event.key === '#') {
        event.preventDefault();
        this.$refs.postContent.innerHTML = `${this.$refs.postContent.innerHTML} <span class="post-hashtag">#`;
        this.placeCaretAtEnd(this.$refs.postContent);
        this.hashtag = true;
      }
      if (event.key === '@') {
        if (!this.mentionUsers.length) {
          this.setMentionUsers();
        }
      }
      if (event.keyCode === 32 && this.hashtag) {
        event.preventDefault();
        this.$refs.postContent.innerHTML = `${this.$refs.postContent.innerHTML} </span>&nbsp`;
        this.placeCaretAtEnd(this.$refs.postContent);
        this.hashtag = false;
      }
    },
    addPost() {
      const formData = new FormData();
      const postContent = this.$refs.postContent.innerHTML;
      const link = this.type === 'user' ? '/api/post/add/user' : '/api/post/add/circle';
      this.postCreatingLoading = true;
      this.errors = [];
      Array
        .from(Array(this.$refs.imagesInput.files.length).keys())
        .forEach((x) => {
          formData.append('images[]', this.$refs.imagesInput.files[x], this.$refs.imagesInput.files[x].name);
        });
      formData.append('post_content', postContent);
      if (this.selectedVideo) {
        formData.append('video_id', this.selectedVideo.id);
      }
      if (this.circle) {
        formData.append('circle_slug', this.circle.slug);
      }
      this.$http.post(link, formData)
        .then((response) => {
          this.postCreatingLoading = false;
          const post = response.data;
          this.addNewFeedItem(post);
          this.$refs.postForm.reset();
          this.$refs.postContent.innerHTML = '';
        })
        .catch((error) => {
          this.postCreatingLoading = false;
          this.errors = error.response.data;
        });
    },
    placeCaretAtEnd(el) {
      el.focus();
      if (typeof window.getSelection !== 'undefined' && typeof document.createRange !== 'undefined') {
        const range = document.createRange();
        const sel = window.getSelection();
        range.selectNodeContents(el);
        range.collapse(false);
        sel.removeAllRanges();
        sel.addRange(range);
      } else if (typeof document.body.createTextRange !== 'undefined') {
        const textRange = document.body.createTextRange();
        textRange.moveToElementText(el);
        textRange.collapse(false);
        textRange.select();
      }
    },
  },
};


</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .form-error {
    fnt($warning-invert, .75rem, $weight-normal, left);
    background-color : $warning;
    border-radius    : 3px;
    border           : 1px solid $warning;
    box-shadow       : 0 2px 2px 0 rgba($black-bis, .16), 0 0 0 1px rgba($black-bis, .08);
    cursor           : pointer;
    left             : 20px;
    margin-bottom    : -6px;
    padding          : 4px 12px;
    position         : absolute;
    transition       : box-shadow .3s;
    width            : 80%;
    z-index          : 2;
    &--top {
      bottom : -2px;
      left   : 24px;
    }
    &--bottom {
      bottom : -36px;
      left   : 20px;
    }
    &:hover {
      box-shadow : 0 3px 8px 0 rgba($black-bis, 0.2), 0 0 0 1px rgba($black-bis, 0.08);
    }
  }

  .fa-icon {
    color      : $text-light;
    transition : all .3s;
    &--invert {
      color : $white;
      &:hover {
        color : $white-bis;
      }
    }
    &:hover {
      color : $text-lighter;
    }
    &--pointer {
      cursor : pointer;
    }
    &__box {
      padding : 0 4px 0;
    }
  }

  .create-post {
    width            : 100%;
    background-color : $background-light;
    position         : relative;
    // margin-bottom: 20px;
    &__left {
      display     : flex;
      align-items : center;
    }
    &__right {
      display         : flex;
      align-items     : center;
      justify-content : flex-end;
    }

    &__content {
      align-items      : center;
      background-color : $background-light;
      border-bottom    : 1px solid $border;
      border-radius    : 3px;
      display          : flex;
      flex-direction   : column;
      justify-content  : space-between;
      padding          : 0 24px 8px;
      position         : relative;
      width            : 100%;
    }

    &__contenteditable {
      fnt($text-light, 12px, $weight-normal, left);
      background-color : $background-light;
      border           : none;
      min-height       : 106px;
      outline          : none;
      padding          : 8px 0;
      resize           : none;
      width            : 100%;
      word-break       : break-all;
    }

    &__button {
      fnt($text-light, 12px, $weight-semibold, center);
      height     : 34px;
      border     : none;
      transition : all .3s;
      &--primary {
        background-color : $primary;
        color            : $text-invert;
        width            : 82px;
        &:hover {
          background-color : $lime-hover;
        }
      }
      &--icon {
        font-size : 1rem;
      }
      &--first-child {
        margin-left : -12px;
      }
    }
    &__footer {
      align-items     : center;
      display         : flex;
      justify-content : space-between;
      min-height      : 55px;
      padding         : 0 24px;
      position        : relative;
    }
  }

  .videos-loader {
    color     : $white;
    font-size : 51px;
  }

  .videos-content {
    &__item {
      padding       : 20px;
      border-radius : 7px;
      &:hover {
        .videos-content__img {
          -webkit-filter : grayscale(0);
        }
        .videos-content__select-btn {
          bottom : 0;
        }
      }
      &.active {
        .videos-content__img {
          -webkit-filter : grayscale(0);
        }
      }
    }

    &__img-wrapper {
      margin-bottom : 20px;
      position      : relative;
      overflow      : hidden;
    }
    &__select-btn {
      background-color : $primary;
      border           : none;
      bottom           : -50px;
      box-shadow       : none;
      color            : $white;
      height           : 30px;
      margin           : 0 auto;
      padding          : 20px;
      position         : absolute;
      transition       : all 0.2s ease;
      width            : 100%;
      z-index          : 2;
      &:hover {
        background-color : $primary;
      }
    }
    &__img {
      -webkit-transition : all 0.2s ease;
      transition         : all 0.2s ease;
      -webkit-filter     : grayscale(1);
      width              : 100%;
    }
    &__text {
      color         : $white;
      font-size     : 13px;
      margin-bottom : 0;
    }
  }

  .atwho-wrap {
    position : relative;
    width    : 100%;
  }
</style>

<style>
  .atwho-panel {
    position : absolute;
    top      : 10px !important;
  }

  .atwho-li {
    padding : 20px 5px !important;
  }

  .atwho-li img {
    max-width    : 20%;
    margin-right : 8px;
  }
</style>
