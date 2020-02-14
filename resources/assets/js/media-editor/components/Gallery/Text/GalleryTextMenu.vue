<template>
  <div class="GalleryTextMenu me-gallery-control-bar-menu">
    <div class="me-gallery-control-bar-menu__left">
      <div class="me-gallery-control-bar-menu__title">
        Text
      </div>
      <button
        class="me-gallery-control-bar-menu__button
                           me-gallery-control-bar-menu__button--add-text
                           me-gallery-control-bar-menu__button--on-left"
        title="create text"
        @click="createItemText"
      >
        <font-awesome-icon
          :icon="['fas', 'plus-square']"
          class="fa-icon fa-icon--invert"
        />
      </button>
    </div>
    <div class="me-gallery-control-bar-menu__right">
      <search/>
      <!--<a-v-p-filter/>-->
      <g-l-switcher/>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import CREATE_SLIDE from 'Gql/slides/mutations/createSlide.graphql';
import Search from '../Filters/Search';
import GLSwitcher from '../Filters/GLSwitcher';
import AVPFilter from '../Filters/AVPFilter';
import * as fileTypes from '../../../config/file-types';

export default {
  name: 'GalleryTextMenu',
  components: {
    Search,
    GLSwitcher,
    AVPFilter,
  },
  computed: {
    ...mapGetters('coordinates', [
      'previewProjectContainer',
    ]),
    ...mapGetters('gallery', [
      'itemsText',
    ]),
    ...mapGetters('project', [
      'id',
    ]),
  },
  methods: {
    ...mapActions('gallery', [
      'addItemText',
    ]),
    async createItemText() {
      const { width, height } = this.previewProjectContainer;
      const item = {
        id: Math.floor(Math.random() * 10000),
        name: `Basic Text ${this.itemsText.length ? this.itemsText.length : ''}`,
        fileType: fileTypes.SLIDE,
        canvasSize: {
          width,
          height,
        },
        effects: [],
        items: [],
      };
      try {
        const slide = {
          id: item.id,
          name: item.name,
          effects: [],
          fileType: item.fileType,
          texts: item.items,
        };
        await this.$apollo.mutate({
          mutation: CREATE_SLIDE,
          variables: {
            slide,
            projectId: this.id,
          },
        })
          .then(({ data: { createSlide } }) => {
            item.id = createSlide.id;
          });
        this.addItemText(item);
      } catch (e) {
        console.error(e);
        this.$notify.error({
          title: 'Error',
          message: this.trans('media_editor.texts_hasnt_added'),
        });
      }
    },
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
