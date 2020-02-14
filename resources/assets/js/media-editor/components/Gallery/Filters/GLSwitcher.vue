<template>
  <div class="GLSwitcher switcher">
    <div
      :title="getTitle"
      class="switcher__button"
      @click="handleClick"
    >
      <font-awesome-icon
        v-if="showList"
        :icon="['fas', 'th-large']"
        :class="{'fa-icon--disabled': !isActive}"
        class="fa-icon fa-icon--pointer"
      />
      <font-awesome-icon
        v-else
        :icon="['fas', 'th-list']"
        :class="{'fa-icon--disabled': !isActive}"
        class="fa-icon fa-icon--pointer"
      />
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';

export default {
  name: 'GLSwitcher',
  computed: {
    getTitle() {
      return this.showList
        ? this.trans('media_editor.show_list')
        : this.trans('media_editor.show_grid');
    },
    // TODO added vuex.length to MeProjects & MeClips
    isActive() {
      if (this.activeComponent === 'me-media') {
        return Boolean(this.items.length);
      } else if (this.activeComponent === 'me-text') {
        return Boolean(this.itemsText.length);
      } else if (this.activeComponent === 'me-projects') {
        return Boolean(this.itemsRecentProjects.length);
      } else if (this.activeComponent === 'me-clips') {
        return Boolean(this.itemsClip.length);
      }
      return false;
      // MeMedia MeText MeProjects MeProjects
    },
    ...mapGetters('gallery', [
      'activeComponent',
      'items',
      'itemsText',
      'itemsRecentProjects',
      'itemsClip',
      'showList',
    ]),
  },
  methods: {
    ...mapActions('gallery', [
      'toggleShow',
    ]),
    handleClick() {
      if (this.isActive) {
        this.toggleShow();
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
    .switcher {
        fl-left();
        border-radius: $radius;
        border: 1px solid $border;
        height: 22px;
        padding: 0 4px;
        transition: all .3s;
        margin-left: 8px;
        &--sqr {
            width: 22px;
        }
        &__button {
            fl-center();
            cursor: pointer;
            height: 22px;
            outline: none;
            width: 16px;
        }
    }
</style>
