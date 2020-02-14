<template>
  <div class="ProfilePageProjects projects">
    <ProfilePageProjectsSearchBar
      :user="user"
      :unpublished="unpublished"
      :published="published"
      :rendered="rendered"
      :failed="failed"
      :processing="processing"
      :search-text="searchText"
      @toggle-unpublished="toggleUnpublished"
      @toggle-rendered="toggleRendered"
      @toggle-published="togglePublished"
      @toggle-failed="toggleFailed"
      @toggle-processing="toggleProcessing"
      @input-search-text="updateSearch"
    />
    <font-awesome-icon
      v-if="$apollo.queries.fetchProjects.loading"
      :icon="['fas', 'spinner']"
      spin
      class="fa-icon"
    />
    <div
      v-else
      class="projects__main"
    >
      <div
        v-for="(project, key) in filteredProjects"
        :key="`pr-${key}`"
        class="projects__tile"
      >
        <ProjectBox
          :project="project"
          :user="user"
        />
      </div>
    </div>
    <div
      v-if="!$apollo.queries.fetchProjects.loading && !fetchProjects.length"
      class="projects__message"
    >
      <span>
        There are no projects yet
      </span>
    </div>
  </div>
</template>

<script>
import FETCH_PROJECTS from 'Gql/projects/queries/fetchProjects.graphql';
import SUBSCRIPTION_PROJECT_UPDATED from 'Gql/projects/subscriptions/projectUpdated.graphql';

import { has } from 'lodash';
import { mapGetters } from 'vuex';

import * as constants from 'Helpers/constants';
import ProjectBox from 'Pages/MediaItem/ProjectBox';
import ProfilePageProjectsSearchBar from './ProfilePageProjectsSearchBar';

export default {
  name: 'ProfilePageProjects',
  components: {
    ProjectBox,
    ProfilePageProjectsSearchBar,
  },
  props: {
    user: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      constants,
      searchText: '',
      published: true,
      unpublished: true,
      rendered: true,
      failed: true,
      processing: true,
      fkTimer: null,
      fkProjects: [{
        id: 101,
        authorId: 1,
        author: {
          id: 1,
          displayName: 'Adriane Casse',
          username: 'adriane.casse',
          avatar: 'https://robohash.org/iustoestvoluptatem.png?size=50x50&set=set1',
        },
        title: 'My full video',
        description: 'My full video',
        isDraft: false,
        isProcessing: false,
        rendering: 0,
        tags: [],
        updatedAt: '12/12/2012',
        createdAt: '12/12/2012',
        userReaction: true,
        stars: [{
          id: 1,
          displayName: 'Alaine Postill',
          username: 'hilarius.banger',
          avatar: 'https://robohash.org/maioresquiaassumenda.png?size=50x50&set=set1',
        }, {
          id: 2,
          displayName: 'Renate Egginson',
          username: 'veronica.drains',
          avatar: 'https://robohash.org/asperioressuscipitipsam.png?size=50x50&set=set1',
        }, {
          id: 3,
          displayName: 'Georgia Romaines',
          username: 'michal.franchi',
          avatar: 'https://robohash.org/inlaborumex.png?size=50x50&set=set1',
        }],
        likes: [{
          id: 1,
          displayName: 'Alaine Postill',
          username: 'hilarius.banger',
          avatar: 'https://robohash.org/maioresquiaassumenda.png?size=50x50&set=set1',
        }, {
          id: 2,
          displayName: 'Renate Egginson',
          username: 'veronica.drains',
          avatar: 'https://robohash.org/asperioressuscipitipsam.png?size=50x50&set=set1',
        }, {
          id: 3,
          displayName: 'Georgia Romaines',
          username: 'michal.franchi',
          avatar: 'https://robohash.org/inlaborumex.png?size=50x50&set=set1',
        }],
        thumbPath: 'https://surmon-china.github.io/vue-quill-editor/static/images/surmon-1.jpg',
        assets: [{
          id: 1011,
          type: constants.FULL,
          path: 'http://vjs.zencdn.net/v/oceans.mp4',
          thumbUrl: 'https://surmon-china.github.io/vue-quill-editor/static/images/surmon-1.jpg',
        }, {
          id: 1012,
          type: constants.AUDIO,
          path: 'http://vjs.zencdn.net/v/oceans.mp4',
          thumbUrl: 'https://surmon-china.github.io/vue-quill-editor/static/images/surmon-1.jpg',
        }, {
          id: 1013,
          type: constants.VIDEO,
          path: 'http://vjs.zencdn.net/v/oceans.mp4',
          thumbUrl: 'https://surmon-china.github.io/vue-quill-editor/static/images/surmon-1.jpg',
        }],
      }, {
        id: 102,
        authorId: 1,
        author: {
          id: 1,
          displayName: 'Adriane Casse',
          username: 'adriane.casse',
          avatar: 'https://robohash.org/iustoestvoluptatem.png?size=50x50&set=set1',
        },
        title: 'My rendering video',
        description: 'My rendering video',
        isDraft: false,
        isProcessing: true,
        rendering: 0,
        tags: [],
        updatedAt: '12/12/2012',
        createdAt: '12/12/2012',
        thumbPath: 'https://surmon-china.github.io/vue-quill-editor/static/images/surmon-1.jpg',
        assets: [{
          id: 1021,
          type: constants.FULL,
          typeVideo: 'video/mp4',
          path: 'http://vjs.zencdn.net/v/oceans.mp4',
          version: 4,
          thumbUrl: 'https://surmon-china.github.io/vue-quill-editor/static/images/surmon-1.jpg',
        }],
      }],
    };
  },
  computed: {
    ...mapGetters('general', ['activeUser']),
    filteredProjects() {
      return this.fetchProjects.filter((item) => {
        let searchText = true;
        let filtersUnpublished = false;
        let filtersRendered = false;
        let filtersPublished = false;
        let filtersProcessing = false;
        let filtersFailed = false;

        if (this.searchText) {
          const index = item.title.toLowerCase()
            .indexOf(this.searchText.toLowerCase());
          searchText = index !== -1;
        }
        if (this.published) {
          filtersPublished = item.isPublished;
        }
        if (this.unpublished) {
          filtersUnpublished = item.isDraft;
        }
        if (this.rendered) {
          filtersRendered = item.isRendered;
        }
        if (this.processing) {
          filtersProcessing = item.isProcessing;
        }
        if (this.failed) {
          filtersFailed = item.isFailed;
        }
        if (
          !this.published
          && !this.unpublished
          && !this.failed
          && !this.processing
          && !this.rendered
        ) {
          filtersUnpublished = true;
          filtersRendered = true;
          filtersPublished = true;
          filtersProcessing = true;
          filtersFailed = true;
        }
        return searchText
          && (filtersUnpublished
            || filtersPublished
            || filtersRendered
            || filtersProcessing
            || filtersFailed);
      });
    },
  },
  apollo: {
    fetchProjects: {
      query: FETCH_PROJECTS,
      variables() {
        return {
          userId: this.user.id,
        };
      },
      subscribeToMore: [
        {
          document: SUBSCRIPTION_PROJECT_UPDATED,
          variables() {
            return {
              userId: this.activeUser.id,
            };
          },
          updateQuery: (previousResult, { subscriptionData }) => {
            if (!has(previousResult, 'fetchProjects')) {
              return { fetchProjects: [] };
            }
            if (!has(subscriptionData, 'data')) {
              return previousResult;
            }
            const { fetchProjects } = previousResult;
            const { projectUpdated } = subscriptionData.data;

            const idx = fetchProjects.findIndex(fp => fp.id === projectUpdated.id);

            const updatedProjects = JSON.parse(JSON.stringify(fetchProjects));
            if (idx !== -1) {
              Object.assign(updatedProjects[idx], { ...projectUpdated });
            }
            return { fetchProjects: [...updatedProjects] };
          },
        },
      ],
    },
  },
  methods: {
    closePopup() {
      this.isActive = false;
    },
    toggleUnpublished() {
      this.unpublished = !this.unpublished;
    },
    toggleRendered() {
      this.rendered = !this.rendered;
    },
    togglePublished() {
      this.published = !this.published;
    },
    toggleFailed() {
      this.failed = !this.failed;
    },
    toggleProcessing() {
      this.processing = !this.processing;
    },
    updateSearch(inputValue) {
      this.searchText = inputValue;
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
    fnt($text-light, 1rem, $weight-normal, center)
  }

  .projects {
    // padding : 16px 0;
    &__main {
      display flex
      flex-wrap wrap
      margin 0 -24px -24px 0
    }
    &__tile {
      padding 0 24px 24px 0
      width 25%
      +tablet() {
        width 100%
      }
      +desktop() {
        width 50%
      }
    }
    &__message {
      display flex
      justify-content center
      color #dbdbdb
      height 80px
      align-items center
      font-weight bold
    }
  }
</style>
