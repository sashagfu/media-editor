<template>
  <div class="GalleryClipsMenu me-gallery-control-bar-menu">
    <div class="me-gallery-control-bar-menu__left">
      <div class="me-gallery-control-bar-menu__title">
        {{ trans('media_editor.clips') }}
      </div>
      <!--
            <button
                    class="me-gallery-control-bar-menu__button
                           me-gallery-control-bar-menu__button--add-text
                           me-gallery-control-bar-menu__button--on-left"
                    title="create text"
                    @click="addItemText"
            >
                <font-awesome-icon
                        :icon="['fas', 'plus-square']"
                        class="fa-icon fa-icon--invert"
                />
            </button>
            -->
    </div>
    <div class="me-gallery-control-bar-menu__right">
      <search/>
      <a-v-p-filter/>
      <g-l-switcher/>
    </div>
  </div>
</template>

<script>
// import { mapActions } from 'vuex';
import Search from '../Filters/Search';
import GLSwitcher from '../Filters/GLSwitcher';
import AVPFilter from '../Filters/AVPFilter';

export default {
  name: 'GalleryClipsMenu',
  components: {
    Search,
    GLSwitcher,
    AVPFilter,
  },
  methods: {
    // ...mapActions('gallery', [
    //     'addItemText',
    // ]),
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
