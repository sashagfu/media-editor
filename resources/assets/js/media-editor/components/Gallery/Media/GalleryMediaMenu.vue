<template>
  <div class="GalleryMediaMenu me-gallery-control-bar-menu">
    <div class="me-gallery-control-bar-menu__left">
      <div class="me-gallery-control-bar-menu__title">
        {{ trans('media_editor.projects_gallery') }}
      </div>
      <button
        class="me-gallery-control-bar-menu__button
                               me-gallery-control-bar-menu__button--upload
                               me-gallery-control-bar-menu__button--on-left"
        title="open upload menu"
        @click.prevent="openDialog"
      >
        <font-awesome-icon
          :icon="['fas', 'upload']"
          class="fa-icon fa-icon--invert"
        />
      </button>
      <!--<div class="me-gallery-control-bar__title">-->
      <!--{{ title }}-->
      <!--</div>-->
      <!--<div class="me-gallery__select">-->
      <!--<ProjectList/>-->
      <!--</div>-->
    </div>
    <div class="me-gallery-control-bar-menu__right">
      <search/>
      <a-v-p-filter/>
      <g-l-switcher/>
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
import Search from '../Filters/Search';
import GLSwitcher from '../Filters/GLSwitcher';
import AVPFilter from '../Filters/AVPFilter';

export default {
  name: 'GalleryMediaMenu',
  components: {
    Search,
    GLSwitcher,
    AVPFilter,
  },
  data() {
    return {
      activeName: '',
    };
  },
  methods: {
    ...mapActions('upload', ['openDialog']),
  },
};
</script>

<style lang="stylus" scoped>
    @import '../../../../../sass/front/components/bulma-theme';

  .fa-icon {
    color: $text-light;
    font-size: 1rem;
    &:hover {
      color: $text-lighter;
    }
    &--invert {
      color: $white;
      &:hover {
        color: $white-bis;
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
    &__box {
      padding: 0 4px 0;
    }
  }

</style>
