<template>
  <div class="GalleryTextList me-gallery-list">
    <template
      v-if="itemsText.length && !$apollo.queries.fetchSlides.loading"
    >
      <gallery-item
        v-for="(item, $index) in itemsText"
        :key="$index"
        :item="item"
      />
    </template>
    <div
      v-if="$apollo.queries.fetchSlides.loading"
      class="me-gallery-list__empty mgl-empty"
    >
      <font-awesome-icon
        :icon="['fas', 'spinner']"
        spin
        class="fa-icon fa-icon--add-text"
      />
    </div>
    <div
      v-if="!itemsText.length && !$apollo.queries.fetchSlides.loading"
      class="me-gallery-list__empty mgl-empty"
    >
      <font-awesome-icon
        :icon="['fas', 'font']"
        class="fa-icon fa-icon--add-text fa-icon--pointer"
        @click="addItemText"
      />
      <div class="mgl-empty__title">
        Added some text for your project
      </div>
    </div>
    <text-edit-dialog v-if="itemsTextIsEditing"/>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
// import GET_SLIDES from 'Gql/slides/queries/fetchSlides.graphql';
import FETCH_SLIDES from 'Gql/slides/queries/fetchSlides.graphql';
import GalleryItem from './GalleryTextItem';
import TextEditDialog from './Edit/TextEditDialog';
import * as fileTypes from '../../../config/file-types';

export default {
  name: 'GalleryTextList',
  components: {
    GalleryItem,
    TextEditDialog,
  },
  computed: {
    ...mapGetters('gallery', [
      'itemsText',
    ]),
    ...mapGetters('text', [
      'itemsTextIsEditing',
    ]),
    ...mapGetters('project', [
      'id',
    ]),
    ...mapGetters('coordinates', [
      'previewProjectContainer',
    ]),
  },
  watch: {
    fetchSlides(slides) {
      const texts = slides.map((sl) => {
        const { width, height } = this.previewProjectContainer;
        return ({
          id: sl.id,
          fileType: fileTypes.SLIDE,
          canvasSize: {
            width,
            height,
          },
          name: sl.name,
          items: sl.texts.map(t => this.cloneItems(t)),
        });
      });
      this.setTexts(texts);
    },
  },
  methods: {
    ...mapActions('gallery', [
      'addItemText',
      'setTexts',
    ]),
    cloneItems(textItem) {
      const cloned = JSON.parse(JSON.stringify(textItem));
      const keys = Object.keys(cloned);
      const keysFiltered = keys.filter(k => k !== '__typename');
      const filteredItem = {};
      keysFiltered.forEach((key) => {
        if (Array.isArray(cloned[key])) {
          Object.assign(filteredItem, { [key]: cloned[key].map(i => this.cloneItems(i)) });
        } else if (cloned[key] && typeof cloned[key] === 'object') {
          Object.assign(filteredItem, { [key]: this.cloneItems(cloned[key]) });
        } else {
          Object.assign(filteredItem, { [key]: cloned[key] });
        }
      });
      return filteredItem;
    },
  },
  apollo: {
    fetchSlides: {
      query: FETCH_SLIDES,
      variables() {
        return {
          id: this.id,
        };
      },
    },
  },
};
</script>

<style lang="stylus" scoped>
    @import '../../../../../sass/front/components/bulma-theme';
    .fa-icon {
      color: $text-light;
      font-size: 1rem;
      transition: all .2s;
      &--add-text {
        color: $blue;
        font-size: 2rem;
        &:hover {
          opacity: .8;
        }
      }
      &--pointer {
        cursor: pointer;
      }
      &__box {
        padding: 0 4px 0;
      }
      & + & {
        margin-left: 8px;
      }
    }
    .mgl-empty {
      &__title {
        fnt($text-light, 1rem, $weight-semibold, center);
      }
    }
</style>
