<template>
  <div class="Likes likes">
    <div
      v-if="project.isPerformance"
      class="likes__item"
    >
      <font-awesome-icon
        v-if="project.id === clickedProject"
        icon="spinner"
        spin
        class="fa-icon"
      />
      <font-awesome-icon
        v-else
        :icon="['far', 'star']"
        :class="{
          'fa-icon--starred': project.userReaction || project.stars.length,
        }"
        class="fa-icon fa-icon--pointer"
        @click.prevent="starPost(project)"
      />
      <div class="likes__number">
        {{ project.stars.length }}
      </div>
    </div>
    <div
      v-else
      class="likes__item"
    >
      <font-awesome-icon
        v-if="project.id === clickedProject"
        icon="spinner"
        spin
        class="fa-icon"
      />
      <font-awesome-icon
        v-else
        :icon="['far', 'heart']"
        :class="{'fa-icon--liked': project.userReaction || project.likes.length}"
        class="fa-icon fa-icon--pointer"
        @click.prevent="likePost(project)"
      />

      <div
        v-if="project.likes.length"
        class="likes__number"
      >
        {{ project.likes.length }}
      </div>
    </div>
  </div>
</template>

<script>
import PROJECT_LIKE from 'Gql/projects/mutations/handleProjectLike.graphql';
import PROJECT_STAR from 'Gql/projects/mutations/handleProjectStar.graphql';
import FETCH_PROJECT from 'Gql/projects/queries/fetchProject.graphql';

export default {
  name: 'Likes',
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
      clickedProject: null,
    };
  },
  methods: {
    updateProject(store, user) {
      const fetchProject = {
        query: FETCH_PROJECT,
        variables: {
          uuid: this.project.uuid,
        },
      };
      const data = store.readQuery(fetchProject);
      if (data.fetchProject.userReaction) {
        data.fetchProject.userReaction = false;
        data.fetchProject.stars = data.fetchProject.stars
          .filter(u => u.id !== user.id);
      } else {
        data.fetchProject.userReaction = true;
        data.fetchProject.stars.push(user);
      }
      store.writeQuery({ ...fetchProject, data });
    },
    starPost(project) {
      if (this.clickedProject) return;
      this.clickedProject = project.id;
      this.$apollo.mutate({
        mutation: PROJECT_STAR,
        variables: {
          id: project.id,
        },
        update: (store, { data: { handleProjectStar: user } }) => {
          this.updateProject(store, user);
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
      if (this.clickedProject) return;
      this.clickedProject = project.id;
      this.$apollo.mutate({
        mutation: PROJECT_LIKE,
        variables: {
          id: project.id,
        },
        update: (store, { data: { handleProjectLike: user } }) => {
          this.updateProject(store, user);
        },
      })
        .then(() => {
          this.clickedProject = null;
        })
        .catch(() => {
          this.clickedProject = null;
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
    font-size  : 1rem;
    transition : all .3s;
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
      align-items : center;
      display     : flex;
      height      : 100%;
      padding     : 0 8px;
    }
  }

</style>
