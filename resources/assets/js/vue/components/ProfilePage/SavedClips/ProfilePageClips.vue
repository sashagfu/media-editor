<template>
  <div class="ProfilePageClips clips">
    <ProfilePageClipsSearchBar
      :user="user"
      :show-full-clips="showFullClips"
      :show-video-clips="showVideoClips"
      :show-audio-clips="showAudioClips"
      :search-text="searchText"
      @toggle-full-clips="toggleFullClips"
      @toggle-video-clips="toggleVideoClips"
      @toggle-audio-clips="toggleAudioClips"
      @input-search-text="updateSearch"
    />
    <font-awesome-icon
      v-if="$apollo.queries.fetchSavedAssets.loading"
      :icon="['fas', 'spinner']"
      spin
      class="fa-icon"
    />
    <div
      v-else
      class="clips__main"
    >
      <div
        v-for="(clip, key) in clips"
        :key="`pr-${key}`"
        class="clips__tile"
      >
        <MediaBox
          :item="clip"
          :user="user"
        />
      </div>
    </div>
  </div>
</template>

<script>
/* @flow */
import FETCH_CLIPS from 'Gql/clips/queries/fetchClips.graphql';
import * as constants from 'Helpers/constants';
import MediaBox from 'Pages/MediaItem/MediaBox';
import ProfilePageClipsSearchBar from './ProfilePageClipsSearchBar';


export default {
  name: 'ProfilePageClips',
  components: {
    ProfilePageClipsSearchBar,
    MediaBox,
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
      showFullClips: true,
      showVideoClips: true,
      showAudioClips: true,
    };
  },
  computed: {
    filteredClips() {
      return this.fetchSavedAssets.filter((item) => {
        let searchText = true;
        let filtersVideos = false;
        let filtersAudios = false;
        let filtersFull = false;

        if (this.searchText) {
          const index = item.project.title.toLowerCase()
            .indexOf(this.searchText.toLowerCase());
          searchText = index !== -1;
        }
        if (this.showVideoClips) {
          filtersVideos = item.type === constants.VIDEO;
        }
        if (this.showAudioClips) {
          filtersAudios = item.type === constants.AUDIO;
        }
        if (this.showFullClips) {
          filtersFull = item.type === constants.FULL;
        }
        if (!this.showVideoClips && !this.showAudioClips && !this.showFullClips) {
          filtersVideos = true;
          filtersAudios = true;
          filtersFull = true;
        }
        return searchText && (filtersVideos || filtersAudios || filtersFull);
      });
    },
    clips() {
      return this.filteredClips.map(item => ({
        id: item.id,
        title: item.project.title,
        author: {
          id: item.project.authorId,
          avatar: item.project.author.avatar,
          displayName: item.project.author.displayName,
          createdAt: item.project.author.createdAt,
        },
        project: item.project,
        comment: item.project.comments.length,
        starred: item.project.stars.length + item.project.likes.length,
        clipped: item.project.clipsCount,
        coverImg: item.thumbPath,
        type: item.type,
      }));
    },
  },
  apollo: {
    fetchSavedAssets: {
      query: FETCH_CLIPS,
    },
  },
  methods: {
    toggleFullClips() {
      this.showFullClips = !this.showFullClips;
    },
    toggleVideoClips() {
      this.showVideoClips = !this.showVideoClips;
    },
    toggleAudioClips() {
      this.showAudioClips = !this.showAudioClips;
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

  .clips {
    &__main {
      display flex
      flex-wrap wrap
      margin 0 -24px -24px 0
    }
    &__tile {
      padding 0 24px 24px 0
      height 320px
      +tablet() {
        width 100%
      }
      +desktop() {
        width 50%
      }
      +widescreen() {
        width 25%
      }
    }
  }

</style>
