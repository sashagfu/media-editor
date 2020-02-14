<template>
  <div class="ProfilePageNavBar pp-nav-bar box">
    <div
      class="pp-nav-bar__item"
      @click="handleClick('ProfilePageAboutMain')"
    >
      <div
        :class="[
          'pp-nav-bar__title',
          {'pp-nav-bar__title--active': activeComponent === 'ProfilePageAboutMain'}
        ]"
      >
        {{ trans('profile_page.about') }}
      </div>
    </div>
    <div
      class="pp-nav-bar__item"
      @click="handleClick('ProfilePageProjectsMain')"
    >
      <div
        :class="[
          'pp-nav-bar__title',
          {'pp-nav-bar__title--active': activeComponent === 'ProfilePageProjectsMain'}
        ]"
      >
        {{ trans('profile_page.projects') }}
      </div>
    </div>
    <div
      v-if="activeUser.id == user.id"
      class="pp-nav-bar__item"
      @click="handleClick('ProfilePageClipsMain')"
    >
      <div
        :class="[
          'pp-nav-bar__title',
          {'pp-nav-bar__title--active': activeComponent === 'ProfilePageClipsMain'}
        ]"
      >
        {{ trans('profile_page.saved_clips') }}
      </div>
    </div>
    <div
      class="pp-nav-bar__item"
      @click="handleClick('ProfilePagePlaylistsMain')"
    >
      <div
        :class="[
          'pp-nav-bar__title',
          {'pp-nav-bar__title--active': activeComponent === 'ProfilePagePlaylistsMain'}
        ]"
      >
        {{ trans('profile_page.playlists') }}
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  name: 'ProfilePageNavBar',
  props: {
    activeComponent: {
      type: String,
      default: '',
    },
    user: {
      type: Object,
      default: () => {},
    },
  },
  computed: {
    ...mapGetters('general', ['activeUser']),
  },
  methods: {
    handleClick(item) {
      this.$emit('change-component', item);
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .pp-nav-bar {
    fl-left();
    height: 72px;
    justify-content: space-between;
    &__item {
      fl-center();
      width: 25%;
      height: 100%;
      cursor: pointer;
    }
    &__title {
      fnt($text-light, 16px, $weight-normal, center);
      &--active {
        color: $primary;
      }
    }
  }

</style>
