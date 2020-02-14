<template>
  <div class="HeadLine posts-headline">
    <div class="header is-marginless">
      <div class="header__left">
        <a
          :href="`/profile/${post.author.username}`"
          target="_blank"
        >
          <div
            v-if="!post.author.avatar.includes('/profile/default/avatar')"
            :style="{ backgroundImage: `url(${post.author.avatar })`}"
            class="header__ava"
          />
          <Avatar
            v-if="post.author.avatar.includes('/profile/default/avatar')"
            :username="post.author.displayName"
            custom-class="header__ava"
          />
        </a>
        <div class="level-item header__title">
          <div class="header__text">
            <a
              :href="`/profile/${post.author.username}`"
              target="_blank"
            >
              <span class="header__text--strong">
                {{ post.author.displayName }}
              </span>
            </a>
            <!--wrote a <span class="header__text&#45;&#45;em">blog post</span>-->
          </div>
          <div class="header__date">
            <a :href="`/post/${post.slug}`">
              {{ post.createdAtDiff }}
            </a>
          </div>
        </div>
      </div>
      <div class="header__right">
        <div class="header__setting-box">
          <div class="header__settings">
            <font-awesome-icon
              icon="ellipsis-h"
              class="fa-icon fa-icon--pointer"
            />
          </div>
          <div class="header__dropdown">
            <a
              v-if="user.id === post.author.id"
              class="header__menu-item header__delete-btn"
              @click="deletePost(post)"
            >
              {{ trans('posts.delete_post') }}
            </a>
            <a
              v-else
              class="header__menu-item header__flag-btn"
              @click="flagPost(post)"
            >
              {{ trans('posts.flag_post') }}
            </a>
          </div>
        </div>
      </div>
    </div>
    <div
      v-if="user && showButton"
      class="header__buttons"
    >
      <div
        v-if="post.isPerformance"
        :class="{'starred': (post.userReaction || post.stars.length)}"
        class="header__button header__button--stars"
        @click.prevent="starPost(post)"
      >
        <font-awesome-icon
          v-if="post.id === clickedPost"
          icon="spinner"
          spin
          class="fa-icon fa-icon--invert"
        />
        <font-awesome-icon
          v-else
          :icon="['far', 'star']"
          class="fa-icon fa-icon--invert fa-icon--pointer"
        />
      </div>
      <div
        v-else
        :class="{'liked': (post.userReaction || post.likes.length)}"
        class="header__button header__button--likes"
        @click.prevent="likePost(post)"
      >
        <font-awesome-icon
          v-if="post.id === clickedPost"
          icon="spinner"
          spin
          class="fa-icon fa-icon--invert"
        />
        <font-awesome-icon
          v-else
          :icon="['far', 'heart']"
          class="fa-icon fa-icon--invert fa-icon--pointer"
        />
      </div>
      <div
        :class="{'header__button--comments--visible': commentIsVisibility}"
        class="header__button header__button--comments"
        @click="showComments(post)"
      >
        <font-awesome-icon
          :icon="['far', 'comment']"
          class="fa-icon fa-icon--invert fa-icon--pointer"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import User from 'Models/User';
import Avatar from 'Pages/ProfilePage/Avatar';
import PROJECT_LIKE from 'Gql/projects/mutations/handleProjectLike.graphql';
import PROJECT_STAR from 'Gql/projects/mutations/handleProjectStar.graphql';
import FETCH_PROJECTS from 'Gql/projects/queries/fetchProjects.graphql';

export default {
  name: 'HeadLine',
  components: {
    Avatar,
  },
  props: {
    post: {
      type: Object,
      default() {
        return {};
      },
    },
    user: {
      type: Object,
      default() {
        return {};
      },
    },
    showButton: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      isActiveDelete: false,
      clickedPost: null,
      currentUser: null,
    };
  },
  computed: {
    ...mapGetters('feed', [
      'commentsVisibility',
    ]),
    commentIsVisibility() {
      return this.commentsVisibility.find(i => i === this.post.id);
    },
  },
  mounted() {
    this.currentUser = new User(this.user);
  },
  methods: {
    ...mapActions('feed', [
      'setActivePost',
      'setTogglePostDeleteModal',
      'toggleCommentVisibility',
    ]),
    showComments(post) {
      this.toggleCommentVisibility(post);
    },
    starPost(post) {
      this.clickedPost = post.id;
      this.$apollo.mutate({
        mutation: PROJECT_STAR,
        variables: {
          id: post.id,
        },
        update: (store) => {
          const fetchProjects = { query: FETCH_PROJECTS };
          const dataProjects = store.readQuery(fetchProjects);
          const key = dataProjects.posts.findIndex(p => p.id === post.id);
          if (key !== -1) {
            if (dataProjects.fetchProjects[key].userReaction) {
              dataProjects.fetchProjects[key].userReaction = false;
              dataProjects.fetchProjects[key].stars = dataProjects.fetchProjects[key].stars
                .filter(u => u.id !== this.user.id);
            } else {
              dataProjects.fetchProjects[key].userReaction = true;
              dataProjects.fetchProjects[key].stars.push(this.currentUser);
            }
            store.writeQuery({ ...fetchProjects, data: dataProjects });
          }
        },
      })
        .then(() => {
          this.clickedPost = null;
        })
        .catch(() => {
          this.clickedPost = null;
        });
    },
    likePost(post) {
      this.clickedPost = post.id;
      this.$apollo.mutate({
        mutation: PROJECT_LIKE,
        variables: {
          id: post.id,
        },
        update: (store) => {
          const fetchProjects = { query: FETCH_PROJECTS };
          const dataProjects = store.readQuery(fetchProjects);
          const key = dataProjects.fetchProjects.findIndex(p => p.id === post.id);
          if (key !== -1) {
            if (dataProjects.fetchProjects[key].userReaction) {
              dataProjects.fetchProjects[key].userReaction = false;
              dataProjects.fetchProjects[key].likes = dataProjects.fetchProjects[key].likes
                .filter(u => u.id !== this.user.id);
            } else {
              dataProjects.fetchProjects[key].userReaction = true;
              dataProjects.fetchProjects[key].likes.push(this.currentUser);
            }
            store.writeQuery({ ...fetchProjects, data: dataProjects });
          }
        },
      })
        .then(() => {
          this.clickedPost = null;
        })
        .catch(() => {
          this.clickedPost = null;
        });
    },
    deletePost(post) {
      this.setActivePost(post); // $store.dispatch
      this.setTogglePostDeleteModal(true); // $store.dispatch
    },
    flagPost(post) {
      this.setActivePost(post); // $store.dispatch
      this.setToggleFlagModal(true); // $store.dispatch
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

  .posts-headline {
    position : relative;
  }

  .header {
    fl-between;
    padding: 16px 24px 12px;

    &__left {
      fl-left;
    }
    &__right {
      fl-right;
    }
    &__ava {
      fl-center;
      background    : center center/cover no-repeat $grey-light;
      border-radius : 50%;
      flex          : 0 0 auto;
      height        : 40px;
      margin-right  : 16px;
      width         : 40px;
    }
    &__title {
      align-items     : flex-start;
      display         : flex;
      flex-direction  : column;
      justify-content : center;
    }
    &__text {
      fnt($text-light, 14px, $weight-semibold, left);
      &--strong {
        color       : $text;
        font-weight : $weight-semibold+100;
      }
      &--em {
        color : $primary;
      }
    }
    &__date {
      fnt($text-light, 12px, $weight-normal, left);
    }
    &__settings {
      cursor : pointer;
      height : 40px;
      width  : 40px;
    }
    &__setting-box {
      position : relative;
      width    : 40px;
    }
    &__dropdown {
      box-shadow       : 0 2px 2px 0 rgba($black-bis, 0.16), 0 0 0 1px rgba($black-bis, 0.08);
      background-color : $white;
      display          : none;
      border-radius    : $box-radius;
      flex-direction   : column;
      min-width        : 100px;
      padding          : 6px 12px;
      position         : absolute;
      right            : 12px;
      top              : 24px;
      z-index          : 5;
      &:hover {
        box-shadow : 0 3px 8px 0 rgba($black-bis, 0.2), 0 0 0 1px rgba($black-bis, 0.08);
      }
    }
    &__setting-box:hover &__dropdown {
      display : flex;
    }
    &__menu-item {
      fnt($text-light, 12px, $weight-normal, left);
      &:hover {
        color       : $primary;
        font-weight : $weight-semibold;
      }
    }
    &__buttons {
      position : absolute;
      right    : -20px;
      top      : 18px;
    }
    &__button {
      align-items      : center;
      background-color : $grey-light;
      border-radius    : 50%;
      cursor           : pointer;
      display          : flex;
      height           : 32px;
      justify-content  : center;
      margin           : 4px;
      transition       : all .3s;
      width            : 32px;

      &:hover {
        background-color : $grey;
      }
      &--likes {
        &.liked {
          background-color : $secondary;
          &:hover {
            background-color : $coral-hover;
          }
        }
      }
      &--stars {
        &.starred {
          background-color : $primary;
          &:hover {
            background-color : $lime-hover;
          }
        }
      }
      &--comments {
        &:hover {
          background-color : $blue;
        }
        &--visible {
          background-color : $blue;
          &:hover {
            background-color : $blue-hover;
          }
        }
      }
      &--shares {
        &:hover {
          background-color : $turquoise;
        }
      }
    }
  }
</style>
