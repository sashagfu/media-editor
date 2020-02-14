<template>
  <div class="Likes likes">
    <div
      v-if="post.isPerformance"
      class="likes__item"
    >
      <font-awesome-icon
        v-if="post.id === clickedPost"
        icon="spinner"
        spin
        class="fa-icon"
      />
      <font-awesome-icon
        v-else
        :icon="['far', 'star']"
        :class="{
          'fa-icon--starred': post.userReaction || post.stars.length,
        }"
        class="fa-icon fa-icon--pointer"
        @click.prevent="starPost(post)"
      />
      <div class="likes__number">
        {{ post.stars.length }}
      </div>
    </div>

    <div
      v-else
      class="likes__item"
    >
      <font-awesome-icon
        v-if="post.id === clickedPost"
        icon="spinner"
        spin
        class="fa-icon"
      />
      <font-awesome-icon
        v-else
        :icon="['far', 'heart']"
        :class="{'fa-icon--liked': post.userReaction || post.likes.length}"
        class="fa-icon fa-icon--pointer"
        @click.prevent="likePost(post)"
      />

      <div
        v-if="post.likes.length"
        class="likes__number"
      >
        {{ post.likes.length }}
      </div>
    </div>
  </div>
</template>

<script>
import { isEmpty } from 'lodash';
import PROJECT_LIKE from 'Gql/projects/mutations/handleProjectLike.graphql';
import PROJECT_STAR from 'Gql/projects/mutations/handleProjectStar.graphql';
import FETCH_PROJECTS from 'Gql/projects/queries/fetchProjects.graphql';
import FETCH_CLIPS from 'Gql/clips/queries/fetchClips.graphql';

export default {
  name: 'Likes',
  props: {
    post: {
      type: Object,
      default: () => ({}),
    },
    clip: {
      type: Object,
      default() {
        return {};
      },
    },
    user: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      clickedPost: null,
    };
  },
  methods: {
    updateClip(store, user) {
      const data = store.readQuery({ query: FETCH_CLIPS });
      const key = data.fetchSavedAssets.findIndex(c => c.id === this.clip.id);
      if (key !== -1) {
        if (data.fetchSavedAssets[key].project.userReaction) {
          data.fetchSavedAssets[key].project.userReaction = false;
          if (data.fetchSavedAssets[key].project.isPerformance) {
            // unstarred
            data.fetchSavedAssets[key].project.stars = data.fetchSavedAssets[key]
              .project.stars.filter(u => u.id !== user.id);
          } else {
            // unliked
            data.fetchSavedAssets[key].project.likes = data.fetchSavedAssets[key]
              .project.likes.filter(u => u.id !== user.id);
          }
        } else {
          data.fetchSavedAssets[key].project.userReaction = true;
          if (data.fetchSavedAssets[key].project.isPerformance) {
            // starred
            data.fetchSavedAssets[key].project.stars.push(user);
          } else {
            // liked
            data.fetchSavedAssets[key].project.likes.push(user);
          }
        }
        store.writeQuery({ query: FETCH_CLIPS, data });
      }
    },
    updateProjects(store, user) {
      const data = store.readQuery({ query: FETCH_PROJECTS });
      const key = data.fetchProjects.findIndex(p => p.id === this.post.id);
      if (key !== -1) {
        if (data.fetchProjects[key].userReaction) {
          data.fetchProjects[key].userReaction = false;
          if (data.fetchProjects[key].isPerformance) {
            // unstarred
            data.fetchProjects[key].stars = data.fetchProjects[key].stars
              .filter(u => u.id !== user.id);
          } else {
            // unliked
            data.fetchProjects[key].likes = data.fetchProjects[key].likes
              .filter(u => u.id !== user.id);
          }
        } else {
          data.fetchProjects[key].userReaction = true;
          if (data.fetchProjects[key].isPerformance) {
            // starred
            data.fetchProjects[key].stars.push(user);
          } else {
            // liked
            data.fetchProjects[key].likes.push(user);
          }
        }
        store.writeQuery({ query: FETCH_PROJECTS, data });
      }
    },
    starPost(post) {
      this.clickedPost = post.id;
      this.$apollo.mutate({
        mutation: PROJECT_STAR,
        variables: {
          id: post.id,
        },
        update: (store, { data: { handleProjectStar: user } }) => {
          if (isEmpty(this.clip)) {
            this.updateProjects(store, user);
          } else {
            this.updateClip(store, user);
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
        update: (store, { data: { handleProjectLike: user } }) => {
          if (isEmpty(this.clip)) {
            this.updateProjects(store, user);
          } else {
            this.updateClip(store, user);
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

    &:hover {
      color : $text-lighter;
    }

    &--pointer {
      cursor : pointer;
    }

    &--liked {
      color : $secondary;

      &:hover {
        color : $coral-hover;
      }
    }
    &--starred {
      color : $primary;

      &:hover {
        color : $lime-hover;
      }
    }

    &__box {
      padding : 0 4px 0;
    }
  }

  .likes {
    &__item {
      display     : flex;
      align-items : center;
      height      : 100%;
    }

    &__number {
      fnt($text-light, 12px, $weight-semibold, left);
      padding     : 0 8px;
      height      : 100%;
      display     : flex;
      align-items : center;
    }
  }

</style>
