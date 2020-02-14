<template>
  <div class="Feed column is-12">
    <feed-form
      v-if="showForm"
      :type="formType"
      :circle="circle"
    />
    <font-awesome-icon
      v-if="$apollo.queries.fetchProjects.loading"
      :icon="['fas', 'spinner']"
      spin
      class="fa-icon"
    />
    <div
      v-if="!$apollo.queries.fetchProjects.loading"
      class="timeline__box"
    >
      <div
        v-for="(post, key) in fetchProjects"
        :key="key"
        class="box"
      >
        <post-container
          :user="user"
          :post="post"
          :show-email-share="showEmailShare"
        />
        <comment-a-post
          v-if="commentIsVisibility(post.id)"
          :post="post"
          :user="user"
        />
        <comments-of-post
          v-if="commentIsVisibility(post.id)"
          :post="post"
          :user="user"
        />
      </div>
      <p v-if="!fetchProjects.length">Sorry no posts yet!</p>
    </div>
    <div class="modals">
      <!-- email share modal -->
      <!--
      <div
              class="modal email-share-modal"
              :class="{'is-active': activePost.emailShareVisibility}"
      >
          <div class="modal-background"/>
          <div class="modal-content">
              <form method="POST"
                    @submit.prevent="emailSharePost()"
                    enctype="multipart/form-data"
              >
                  <div class="field">
                      <label>{{ trans('posts.email_share') }}</label>
                      <br>
                      <p class="control">
                          <input v-model="shareEmails"
                                 class="email-share-modal__emails-input"
                                 type="email"
                                 multiple
                                 pattern="/^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$/"
                                 :placeholder="trans('common.email')"
                          >
                      </p>
                      <br>
                      <input type="submit"
                             :disabled="shareEmails.length === 0"
                             class="button email-share-modal__share"
                             :value="trans('posts.share')"
                      >
                  </div>
              </form>
              <div class="default-progress-bar stripes"
                   :class="{'is-hidden': !emailShareLoading}"
              >
                  <span class="default-progress-bar__width"/>
              </div>
              <p v-if="postShared">{{ trans('posts.share_successful') }}</p>
          </div>
          <button
                  class="modal-close"
                  @click = "activePost.emailShareVisibility = false"
          />
      </div> -->

      <!-- chat share modal -->
      <!--
      <div
              class="modal chat-share-modal"
              :class="{'is-active': activePost.chatShareVisibility}"
      >
          <div class="modal-background"/>
          <div class="modal-content">
              <p>{{ trans('posts.share_to_chat') }}</p>
              <multiselect
                      v-model="selectedToShare"
                      :options="usersToShare"
                      :multiple="true"
                      label="display_name"
                      track-by="id"
                      :internal-search="false"
                      :options-limit="20"
                      :loading="userSelectLoading"
                      :clear-on-select="false"
                      :max="5"
                      placeholder=""
                      @search-change="getUsersToShare"
              />
              <div class="small-12">
                  <input type="submit"
                         @click="shareToChat"
                         :disabled="selectedToShare.length === 0"
                         class="button chat-share__btn"
                         :value="trans('posts.share')"
                  >
              </div>
              <div class="default-progress-bar stripes"
                   :class="{'is-hidden': !sharingLoading}"
              >
                  <span class="default-progress-bar__width"/>
              </div>
              <p v-if="postShared">{{ trans('posts.share_successful') }}</p>
          </div>
          <button class="modal-close"
                  @click = "activePost.chatShareVisibility = false"
          />
      </div> -->
    </div>

    <!-- <PostDeleteModal
            :delete-modal-active="togglePostDeleteModal"
    />
    <CommentDeleteModal
            :delete-modal-active="toggleCommentDeleteModal"
    />
    <FlagModal
            :flag-modal-active="toggleFlagModal"
            :flag-reasons="flagReasons"
    /> -->
  </div>
</template>

<script>
/* @flow */
import { mapGetters } from 'vuex';
// import GET_PERFORMANCE_POSTS from 'Gql/Post/GetPerformancePosts.gql';
import FETCH_PROJECTS from 'Gql/projects/queries/fetchProjects.graphql';

// POSTS
//    import Post from '../PostVersions/Post';
//    import PostFeatured from './PostFeatured';
//    import PostWithGif from '../PostVersions/PostWithGif';
//    import PostWithLink from '../PostVersions/PostWithLink';
//    import PostWithPhoto from '../PostVersions/PostWithPhoto';
import PostWithPhotoPack from './PostWithPhotoPack';
import PostContainer from './PostContainer';
//    import PostWithPicture from '../PostVersions/PostWithPicture';
//    import PostWithSharedContent from '../PostVersions/PostWithSharedContent';
import PostWithSharedVideo from './PostWithSharedVideo';

// COMMENTS
// import Comment from './Comment';
import CommentsOfPost from './CommentsOfPost';
//    import CommentAPost from '../CommentVersion/CommentAPost';
//    import CommentExpandAPost from '../CommentVersion/CommentExpandAPost';
import CommentAPost from './CommentAPost';
//    import CommentsReply from './CommentsReply';
//    import CommentV2 from '../CommentVersion/CommentV2';
import ViewMoreComments from './commentComponents/ViewMoreComments';
//
//    import Border from '../CommentVersion/components/Border';
//    import BorderBetweenReplay from '../CommentVersion/components/BorderBetweenReplay';
//    import LoadMoreBtn from '../LoadMoreBtn';
import FeedForm from './FeedForm';
import PostDeleteModal from './postComponents/DeletePostPopup';
import CommentDeleteModal from './commentComponents/DeleteCommentPopup';
import FlagModal from './postComponents/FlagPostPopup';

const Feed = {
  name: 'feed',
  components: {
    PostContainer,
    PostWithSharedVideo,
    PostWithPhotoPack,
    CommentsOfPost,
    CommentAPost,
    ViewMoreComments,
    FeedForm,
    PostDeleteModal,
    CommentDeleteModal,
    FlagModal,
  },
  props: {
    flagReasons: {
      type: Array,
      default: () => [],
    },
    user: {
      type: Object,
      default: () => ({}),
    },
    showForm: {
      type: Boolean,
      default: false,
    },
    formType: {
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
      showEmailShare: false,
      userSelectLoading: false,
    };
  },
  computed: {
    ...mapGetters('feed', [
      'commentsVisibility',
    ]),
  },
  methods: {
    commentIsVisibility(id) {
      return this.commentsVisibility.find(i => i === id);
    },
    toggleDeleteModal() {
      this.isActiveDelete = !this.isActiveDelete;
    },
  },
  apollo: {
    fetchProjects: {
      query: FETCH_PROJECTS,
    },
  },
};

export default Feed;

</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';
  @import '../../../../../../node_modules/vue-multiselect/dist/vue-multiselect.min.css';

  .fa-icon {
    color     : $text-light;
    font-size : 1rem;
    margin    : 12px;
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

  .modal-content {
    background : white;
    padding    : 20px;
    min-height : 365px;
    & input {
      width   : 100%;
      padding : 15px;
    }
    & input.button {
      position : absolute;
      bottom   : 20px;
      width    : 94%;
      left     : 3%;
    }
  }
</style>
