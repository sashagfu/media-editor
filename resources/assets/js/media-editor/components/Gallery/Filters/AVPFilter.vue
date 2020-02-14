<template>
  <div class="AVPFilter filter">
    <div
      v-if="activeComponent === 'me-clips'"
      :class="{'filter__button--invert': filtersFull}"
      :title="trans('media_editor.filter_full')"
      class="filter__button"
      @click="toggleFilter('full')"
    >
      <font-awesome-icon
        :icon="['fas', 'video']"
        :class="{
          'fa-icon--invert': filtersFull,
          'fa-icon--disabled': !isActive
        }"
        class="fa-icon fa-icon--pointer"
      />
    </div>
    <div
      :class="{'filter__button--invert': filtersVideos}"
      :title="trans('media_editor.filter_videos')"
      class="filter__button"
      @click="toggleFilter('videos')"
    >
      <font-awesome-icon
        :icon="['fas', 'film']"
        :class="{
          'fa-icon--invert': filtersVideos,
          'fa-icon--disabled': !isActive
        }"
        class="fa-icon fa-icon--pointer"
      />
    </div>
    <div
      :class="{'filter__button--invert': filtersAudios}"
      :title="trans('media_editor.filter_audios')"
      class="filter__button"
      @click="handleClick('audios')"
    >
      <font-awesome-icon
        :icon="['fas', 'music']"
        :class="{
          'fa-icon--invert': filtersAudios,
          'fa-icon--disabled': !isActive
        }"
        class="fa-icon fa-icon--pointer"
      />
    </div>
    <div
      v-if="activeComponent !== 'me-clips'"
      :class="{'filter__button--invert': filtersImages}"
      :title="trans('media_editor.filter_images')"
      class="filter__button"
      @click="handleClick('images')"
    >
      <font-awesome-icon
        :icon="['fas', 'image']"
        :class="{
          'fa-icon--invert': filtersImages,
          'fa-icon--disabled': !isActive
        }"
        class="fa-icon fa-icon--pointer"
      />
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';

export default {
  name: 'AVPFilter',
  data() {
    return {
      showVideo: false,
      showAudio: false,
      showImage: false,
    };
  },
  computed: {
    ...mapGetters('gallery', [
      'filtersFull',
      'filtersVideos',
      'filtersAudios',
      'filtersImages',
      'activeComponent',
      'items',
      'itemsText',
      'itemsClip',
    ]),
    isActive() {
      if (this.activeComponent === 'me-media') {
        return Boolean(this.items.length);
      } else if (this.activeComponent === 'me-text') {
        return Boolean(this.itemsText.length);
      } else if (this.activeComponent === 'me-projects') {
        return false;
      } else if (this.activeComponent === 'me-clips') {
        return Boolean(this.itemsClip.length);
      }
      return false;
      // MeMedia MeText MeProjects MeProjects
    },
  },
  methods: {
    ...mapActions('gallery', [
      'toggleFilter',
    ]),
    handleClick(action) {
      if (this.isActive) {
        this.toggleFilter(action);
      }
    },
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
        &--invert {
            color: $text-invert;
            &:hover {
                color: $grey-lighter;
            }
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
    .filter {
        fl-left();
        transition: all .3s;
        margin-left: 8px;
        &--sqr {
            width: 22px;
        }
        &__button {
            fl-center();
            border-radius: $radius;
            border: 1px solid $border;
            height: 22px;
            cursor: pointer;
            outline: none;
            width: 22px;
            &--invert {
                background-color: $text-light;
                border: 1px solid $text-light;
            }
        }
        &__button + &__button {
            margin-left: 4px;
        }
    }
</style>
