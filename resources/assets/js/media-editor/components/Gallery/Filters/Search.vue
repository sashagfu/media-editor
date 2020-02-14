<template>
  <div class="Search search">
    <input
      v-if="searchIsOpen"
      :value="searchText"
      class="search__input"
      type="text"
      autofocus
      @keyup.esc="clickEsc"
      @input="debounceInput"
    >
    <div
      :title="trans('common.search')"
      class="search__button"
      @click="handleClick"
    >
      <font-awesome-icon
        v-if="searchText"
        :icon="['fas', 'times']"
        class="fa-icon fa-icon--pointer"
      />
      <font-awesome-icon
        v-else
        :icon="['fas', 'search']"
        :class="{'fa-icon--disabled': !isActive}"
        class="fa-icon fa-icon--pointer"
      />
    </div>
  </div>
</template>

<script>
import _ from 'lodash';
import { mapGetters, mapActions } from 'vuex';

export default {
  name: 'Search',
  data() {
    return {
      searchIsOpen: false,
    };
  },
  computed: {
    ...mapGetters('gallery', [
      'searchText',
      'activeComponent',
      'items',
      'itemsText',
      'itemsRecentProjects',
    ]),
    // TODO added vuex.length to MeProjects & MeClips
    isActive() {
      if (this.activeComponent === 'me-media') {
        return Boolean(this.items.length);
      } else if (this.activeComponent === 'me-text') {
        return Boolean(this.itemsText.length);
      } else if (this.activeComponent === 'me-projects') {
        return Boolean(this.itemsRecentProjects.length);
      } else if (this.activeComponent === 'me-clips') {
        return true;
      }
      return false;
      // MeMedia MeText MeProjects MeProjects
    },
  },
  methods: {
    ...mapActions('gallery', [
      'setSearch',
    ]),
    handleClick() {
      if (this.isActive) {
        this.setSearch('');
        this.searchIsOpen = !this.searchIsOpen;
      }
    },
    clickEsc() {
      this.setSearch('');
      this.searchIsOpen = false;
    },
    debounceInput: _.debounce(function updateSearch(e) {
      this.setSearch(e.target.value);
    }, 600),
  },
};
</script>

<style lang="stylus" scoped>
    @import '../../../../../sass/front/components/bulma-theme';

    .fa-icon {
        color: $text-light;
        font-size: 14px;
        transition: all .3s;
        &:hover {
            color: $text-lighter;
        }
        &--pointer {
            cursor: pointer;
        }
        &--disabled {
            color: $grey-lighter;
            cursor: auto;
            &:hover {
                color: $grey-lighter;
            }
        }
    }
    .search {
        fl-left();
        border-radius: $radius;
        border: 1px solid $border;
        height: 22px;
        padding: 0 4px;
        transition: all .3s;
        &--sqr {
            width: 22px;
        }
        &__button {
            fl-center();
            cursor: pointer;
            outline: none;
            width: 16px;
        }
        &__input {
            border: none;
            outline: none;
        }
    }

</style>
