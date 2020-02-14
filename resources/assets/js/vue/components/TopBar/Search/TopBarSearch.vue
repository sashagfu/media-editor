<template>
  <div
    :class="{'top-bar-search--is-active' : items.length}"
    class="TopBarSearch top-bar-search"
  >
    <form
      class="top-bar-search__form"
      @submit.prevent="globalSearch()"
    >
      <input
        v-model="query"
        :placeholder="trans('search.type_to_search')"
        class="top-bar-search__inp"
        type="text"
        name="search"
        @input="debounceInput"
        @keydown.esc="reset"
      >
      <div class="top-bar-search__btn top-bar-search__btn--clips">
        <div
          v-if="hasProject"
          :class="[
            'top-bar-search__clip-btn',
            'top-bar-search__clip-btn--audio',
            {'top-bar-search__clip-btn--active': searchAudio}
          ]"
          @click="handleAudio"
        >
          <font-awesome-icon
            :icon="['fas', 'music']"
            class="fa-icon fa-icon--audio"
          />
        </div>
      </div>
      <div class="top-bar-search__btn top-bar-search__btn--clips">
        <div
          v-if="hasProject"
          :class="[
            'top-bar-search__clip-btn',
            {'top-bar-search__clip-btn--active': searchVideo}
          ]"
          @click="handleVideo"
        >
          <font-awesome-icon
            :icon="['fas', 'film']"
            class="fa-icon fa-icon--video"
          />
        </div>
      </div>
      <div
        class="top-bar-search__btn  top-bar-search__btn--search"
        @click="reset"
      >
        <font-awesome-icon
          v-if="loading"
          :icon="['fas', 'spinner']"
          spin
          class="fa-icon"
        />
        <font-awesome-icon
          v-else-if="query || items.length"
          :icon="['fas', 'times']"
          class="fa-icon"
        />
        <font-awesome-icon
          v-else
          :icon="['fas', 'search']"
          class="fa-icon"
        />
      </div>
    </form>
    <div
      v-if="items.length && showResultsBox"
      :class="{'top-bar-search__dropdown-content--is-active' : items.length}"
      class="top-bar-search__dropdown-content"
    >
      <SearchDropdownBox
        v-for="(item, index) in filteredItems"
        v-if="index < 10"
        :key="`item-${index}`"
        :item="item"
        @handle-tag="handleTag"
        @reset="reset"
        @close-box="closeSearchResultsBox"
      />
      <div
        v-if="items.length > 9"
        class="top-bar-search__see-more"
        @click="globalSearch"
      >
        see more...
      </div>
    </div>
  </div>
</template>

<script>
import { debounce } from 'lodash';
import * as constants from 'Helpers/constants';
import SearchDropdownBox from 'Pages/TopBar/Search/SearchDropdownBox';

import FETCH_PROJECT from 'Gql/projects/queries/fetchProjects.graphql';
import FETCH_USERS from 'Gql/users/queries/fetchUsersProjects.graphql';
import FETCH_TAGS from 'Gql/tags/queries/fetchTags.graphql';

export default {
  name: 'TopBarSearch',
  components: {
    SearchDropdownBox,
  },
  data() {
    return {
      searchClips: false,
      searchVideo: true,
      searchAudio: true,
      showResultsBox: false,
      globalSearchPassed: false,
      query: '',
      loading: false,
      items: [],
    };
  },
  computed: {
    hasProject() {
      return this.items.some(i => i.__typename === 'Project');
    },
    filteredItems() {
      return this.items.filter((item) => {
        if (item.__typename !== 'Project') {
          return true;
        }
        let filtersVideos = false;
        let filtersAudios = false;
        let filtersFull = false;

        if (this.searchVideo) {
          filtersVideos = item.assets.some(a => a.type === constants.VIDEO);
        }
        if (this.searchAudio) {
          filtersAudios = item.assets.some(a => a.type === constants.AUDIO);
        }
        if (this.searchVideo && this.searchAudio) {
          filtersFull = item.assets.some(a => a.type === constants.FULL);
        }
        if (!this.searchVideo && !this.searchAudio) {
          filtersVideos = true;
          filtersAudios = true;
          filtersFull = true;
        }
        return (filtersVideos || filtersAudios || filtersFull);
      });
    },
  },
  watch: {
    query(val) {
      if (val.length < 3 && this.items.length) {
        this.items = [];
        this.loading = false;
      }
    },
  },
  methods: {
    handleVideo() {
      this.searchVideo = !this.searchVideo;
    },
    handleAudio() {
      this.searchAudio = !this.searchAudio;
    },

    handleTag({ id, name }) {
      this.query = `#${name}`;
      this.items = [];
      this.loading = true;
      this.$apollo.query({
        query: FETCH_PROJECT,
        variables: {
          tag: { id, name },
        },
      })
        .then(({ data: { fetchProjects } }) => {
          this.items = fetchProjects;
          this.loading = false;
        })
        .catch((e) => {
          console.error(e);
          this.loading = false;
        });
    },
    reset() {
      this.query = '';
      this.items = [];
      this.loading = false;
      this.showResultsBox = false;
    },
    closeSearchResultsBox() {
      this.showResultsBox = false;
    },
    getQueryText() {
      switch (this.query.charAt(0, 1)) {
        case '@':
          return this.query.substring(1);
        case '#':
          return this.query.substring(1);
        default:
          return this.query;
      }
    },
    getQueryType() {
      switch (this.query.charAt(0, 1)) {
        case '@':
          return FETCH_USERS;
        case '#':
          return FETCH_TAGS;
        default:
          return FETCH_PROJECT;
      }
    },
    setResult(data) {
      this.items = [];
      switch (this.query.charAt(0, 1)) {
        case '@':
          data.fetchUsers.forEach((user) => {
            this.items.push(user);
            user.projects.forEach(project => this.items.push(project));
          });
          break;
        case '#':
          this.items = data.fetchTags;
          break;
        default:
          this.items = data.fetchProjects;
      }
    },
    debounceInput: debounce(function updateSearch() {
      if (this.query.length < 3) {
        return;
      }
      this.loading = true;
      this.$apollo.query({
        query: this.getQueryType(),
        variables: {
          term: this.getQueryText(),
        },
      })
        .then(({ data }) => {
          // To dont show fast results in box, after redirect to search page
          if (!this.globalSearchPassed) {
            this.setResult(data);
            this.showResultsBox = true;
          }
          this.loading = false;
          this.globalSearchPassed = false;
        })
        .catch((e) => {
          console.error(e);
          this.loading = false;
        });
    }, 600),
    globalSearch() {
      this.showResultsBox = false;
      // To dont show fast results in box, after redirect to search page
      this.globalSearchPassed = true;
      this.$router.push({
        name: 'SearchResultsPage',
        query: {
          q: this.makeTerm(this.query),
        },
      });
    },
    makeTerm(term) {
      return term.replace(/[^\w\s]/gi, '');
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
    color      : $grey-light;
    font-size  : 16px;
    transition : all .2s;
    &:hover {
      opacity: 0.8;
    }
    &--audio {
      color: $white;
      font-size: 13px;
    }
    &--video {
      color: $white;
      font-size: 14px;
    }
  }

  .top-bar-search {
    align-items: flex-start;
    display: flex;
    flex-direction: column;
    padding: 0;
    position: relative;
    transition: width 1s ease-in-out;
    width: 100%;
    +desktop() {
      width: 100%;
    }
    &--is-active {
      +tablet() {
        position: absolute;
        right: 0;
        width: 100%;
        z-index: 10;
      }
    }
    &__form {
      fl-between();
      height: 72px;
      width: 100%;
      +tablet() {
        background: none;
      }
      +desktop() {
        background: none;
      }
    }
    &__inp {
      fnt($text-light, 14px, $weight-normal, left);
      border: 1px solid $border;
      border-right: none;
      border-radius:  3px 0 0 3px;
      padding: 0 16px;
      height: 36px;
      flex: 1 1 auto;
      outline: none;

      &::-webkit-input-placeholder { /* Chrome/Opera/Safari */
        fnt($text-lighter, 14px, $weight-normal, left);
      }
      &::-moz-placeholder { /* Firefox 19+ */
        fnt($text-lighter, 14px, $weight-normal, left);
      }
      &:-ms-input-placeholder { /* IE 10+ */
        fnt($text-lighter, 14px, $weight-normal, left);
      }
      &:-moz-placeholder { /* Firefox 18- */
        fnt($text-lighter, 14px, $weight-normal, left);
      }
    }
    &__btn {
      fl-center();
      fnt($text-light, 14px, $weight-normal, center);
      cursor: pointer;
      height: 36px;
      border: 1px solid $border;
      border-left: none;
      padding: 0 12px;
      flex: 0 0 auto;
      &--search {
        border-radius: 0 3px 3px 0;
        width: 42px;
      }
      &--clips {
        fl-center();
        border-right: none;
        padding: 0 2px;
      }
      &--active {
        background-color: rgba($primary, 0.1);
        color: $primary;
      }
    }
    &__btn + &__btn {
      margin-left: 0;
    }
    &__clip-btn {
      fl-center();
      background-color: $grey-lighter;
      border-radius: $radius;
      cursor: pointer;
      height: 24px;
      transition: all 0.3s;
      width: 24px;
      &--active {
        background-color: $primary;
      }
      &--audio {
        padding-right: 1px;
      }
    }
    &__dropdown-content {
      align-items: flex-start;
      border-radius: $radius;
      border: 1px solid $border;
      box-shadow: 0 2px 2px 0px rgba($black, .05);
      display: none;
      flex-direction: column;
      position: absolute;
      top: 58px;
      width: 100%;
      z-index: 1;
      &--is-active {
        display : flex;
      }
    }
    &__see-more {
      fl-center();
      fnt($primary, 14px, $weight-normal, center);
      background-color: $white;
      border-top: 1px solid $border;
      cursor: pointer;
      height: 32px;
      transition: all .3s;
      width: 100%;
      &:hover {
        background-color: $white-bis;
      }
    }
  }
</style>

<style lang="stylus">
  @import '../../../../../sass/front/components/bulma-theme';

  .top-bar-search {
    .el-input__inner {
      &:hover {
        border-color: $white-shadow !important;
      }
      &:focus {
        border-color: $grey-lighter !important;
      }
    }
  }

</style>
