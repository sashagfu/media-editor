<template>
  <div class="HeadLine projects-headline">
    <div class="header is-marginless">
      <div class="header__left">
        <a :href="`/profile/${project.author.username}`">
          <div
            v-if="!project.author.avatar.includes('/profile/default/avatar')"
            :style="{ backgroundImage: `url(${project.author.avatar })`}"
            class="header__ava"
          />
          <Avatar
            v-if="project.author.avatar.includes('/profile/default/avatar')"
            :username="project.author.displayName"
            custom-class="header__ava"
          />
        </a>
        <div class="level-item header__title">
          <div class="header__text">
            <a :href="`/profile/${project.author.username}`">
              <span class="header__text--strong">
                {{ project.author.displayName }}
              </span>
            </a>
          </div>
          <div class="header__date">
            On Actionlime from {{ formatData(project.author.createdAt) }}
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
              v-if="user.id === project.author.id"
              class="header__menu-item header__delete-btn"
              @click="deletePost(project)"
            >
              {{ trans('projects.delete_project') }}
            </a>
            <a
              v-else
              class="header__menu-item header__flag-btn"
              @click="flagPost(project)"
            >
              {{ trans('projects.flag_project') }}
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
        v-if="project.isPerformance"
        :class="{'starred': (project.userReaction || project.stars.length)}"
        class="header__button header__button--stars"
        @click.prevent="starPost(project)"
      >
        <font-awesome-icon
          v-if="project.id === clickedProject"
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
        :class="{'liked': (project.userReaction || project.likes.length)}"
        class="header__button header__button--likes"
        @click.prevent="likePost(project)"
      >
        <font-awesome-icon
          v-if="project.id === clickedProject"
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
        @click="showComments(project)"
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
import moment from 'moment';
import User from 'Models/User';
import Avatar from 'Pages/ProfilePage/Avatar';

import PROJECT_LIKE from 'Gql/projects/mutations/handleProjectLike.graphql';
import PROJECT_STAR from 'Gql/projects/mutations/handleProjectStar.graphql';
import FETCH_PROJECT from 'Gql/projects/queries/fetchProject.graphql';

export default {
  name: 'HeadLine',
  components: {
    Avatar,
  },
  props: {
    project: {
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
      clickedProject: null,
      currentUser: null,
    };
  },
  computed: {
    ...mapGetters('feed', [
      'commentsVisibility',
    ]),
    commentIsVisibility() {
      return this.commentsVisibility.find(i => i === this.project.id);
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
    formatData(dt) {
      return moment(dt).format('MMMM YYYY');
    },
    showComments(project) {
      this.toggleCommentVisibility(project);
    },
    starPost(project) {
      this.clickedProject = project.id;
      this.$apollo.mutate({
        mutation: PROJECT_STAR,
        variables: {
          id: project.id,
        },
        update: (store) => {
          const fetchProject = {
            query: FETCH_PROJECT,
            variables: {
              id: this.project.id,
            },
          };
          const dataProject = store.readQuery(fetchProject);
          if (dataProject.fetchProject.userReaction) {
            dataProject.fetchProject.userReaction = false;
            dataProject.fetchProject.stars = dataProject.fetchProject.stars
              .filter(u => u.id !== this.user.id);
          } else {
            dataProject.fetchProject.userReaction = true;
            dataProject.fetchProject.stars.push(this.currentUser);
          }
          store.writeQuery({ ...fetchProject, data: dataProject });
        },
      })
        .then(() => {
          this.clickedProject = null;
        })
        .catch(() => {
          this.clickedProject = null;
        });
    },
    likePost(project) {
      this.clickedProject = project.id;
      this.$apollo.mutate({
        mutation: PROJECT_LIKE,
        variables: {
          id: project.id,
        },
        update: (store) => {
          const fetchProject = {
            query: FETCH_PROJECT,
            variables: {
              id: this.project.id,
            },
          };
          const dataProject = store.readQuery(fetchProject);
          if (dataProject.fetchProject.userReaction) {
            dataProject.fetchProject.userReaction = false;
            dataProject.fetchProject.likes = dataProject.fetchProject.likes
              .filter(u => u.id !== this.user.id);
          } else {
            dataProject.fetchProject.userReaction = true;
            dataProject.fetchProject.likes.push(this.currentUser);
          }
          store.writeQuery({ ...fetchProject, data: dataProject });
        },
      })
        .then(() => {
          this.clickedProject = null;
        })
        .catch(() => {
          this.clickedProject = null;
        });
    },
    deletePost(project) {
      this.setActivePost(project); // $store.dispatch
      this.setTogglePostDeleteModal(true); // $store.dispatch
    },
    flagPost(project) {
      this.setActivePost(project); // $store.dispatch
      this.setToggleFlagModal(true); // $store.dispatch
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

  .projects-headline {
    position : relative;
  }

  .header {
    fl-between();
    padding: 16px 24px 12px;

    &__left {
      fl-left();
    }
    &__right {
      fl-right();
    }
    &__ava {
      fl-center();
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
      fnt($primary, 10px, $weight-normal, left);
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
      transition: all 0.3s;
      white-space: nowrap;
      &:hover {
        color       : $primary;
      }
    }
    &__buttons {
      position : absolute;
      right    : -20px;
      top      : 18px;
    }
    &__button {
      fl-center();
      background-color : $grey-light;
      border-radius    : 50%;
      cursor           : pointer;
      height           : 32px;
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
