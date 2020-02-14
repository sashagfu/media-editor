<template>
  <draggable
    :class="{ 'me-gallery-item--active' : isPreviewing }"
    :item-id="item.id"
    :item-uuid="item.uuid"
    :cursor-at="{ left: 25, top: 25 }"
    :disabled="!isCanMove"
    class="GalleryMediaGridItem gmg-item"
    append-to="body"
    @dragging="onDragging"
    @dragstop="onDragStop"
  >
    <div
      :style="{ backgroundImage: `url(${thumbUrl})` }"
      class="gmg-item__thumb box"
    >
      <div
        :style="{'background-image': `url(${
          'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAIAAAACCAYAAABytg0kA'+
          'AAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAABZJREFUeNpi2r9//'+
          '38gYGAEESAAEGAAasgJOgzOKCoAAAAASUVORK5CYII='
        })`}"
        class="gmg-item__pixelization"
      />
      <div class="gmg-item__header">
        <menu-media :file="item"/>
      </div>
      <div class="gmg-item__main">
        <div
          class="gmg-item__preview-btn"
          @click.prevent="setPreviewItem"
        >
          <font-awesome-icon
            :icon="['fas', previewIcon]"
            class="fa-icon fa-icon--inverse"
          />
        </div>
      </div>
      <div class="gmg-item__footer">
        <div class="gmg-item__name">
          {{ item.project.title }}
        </div>
        <div class="gmg-item__duration">
          <duration :ms="item.time"/>
        </div>
      </div>
    </div>
    <div
      slot="dragImage"
      :class="draggingStatusClass"
      class="slot-for-drag-image"
    >
      <item-thumb-wrapper
        :file="item"
        :show-as="showAs"
        :insert-length="insertLength"
      />
    </div>
  </draggable>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import { isNil } from 'lodash';
import * as constants from 'Helpers/constants';
import Draggable from '../../common/Draggable';
import Duration from '../../common/Duration';
import ItemThumbWrapper from '../../Thumbs/ItemThumbWrapper';
import DeleteMedia from '../Manage/DeleteMedia';
import MenuMedia from '../Manage/MenuMedia';
import * as fileTypes from '../../../config/file-types';
import * as images from '../../../config/images';

export default {
  name: 'GalleryMediaGridItem',
  components: {
    Draggable,
    DeleteMedia,
    ItemThumbWrapper,
    Duration,
    MenuMedia,
  },
  props: {
    item: {
      type: Object,
      default: () => ({}),
    },
  },
  computed: {
    ...mapGetters('preview', [
      'galleryItemId',
    ]),
    ...mapGetters('dragging', [
      'isDraggingOverDroppable',
      'maxDraggingInsertLength',
      'isCanDrop',
    ]),
    ...mapGetters('timeline', [
      'zoomedPxPerSec',
    ]),
    ...mapGetters('coordinates', {
      droppableCoordinates: 'activeDroppable',
    }),
    ...mapGetters('settings', {
      itemThumbSetting: 'itemThumb',
    }),
    ...mapGetters('player', [
      'playing',
    ]),
    isCanMove() {
      return !this.playing;
    },
    /**
         * Depend on type of item icon will show
         */
    previewIcon() {
      let icon = 'paperclip';
      switch (this.item.type) {
        case constants.IMAGE:
          icon = 'eye';
          break;
        case constants.AUDIO:
        case constants.VIDEO:
        case constants.FULL:
          icon = this.isPlaying ? 'pause' : 'play';
          break;
        default: break;
      }
      return icon;
    },
    /**
         * Get thumb image url
         */
    thumbUrl() {
      switch (this.item.type) {
        case constants.IMAGE:
          return this.item.filePath;
        case constants.AUDIO:
          return images.AUDIO_DEFAULT_THUMB;
        case constants.VIDEO:
        case constants.FULL:
          return this.item.thumbPath || images.VIDEO_DEFAULT_THUMB;
        default: return '';
      }
    },
    showAs() {
      switch (this.item.type) {
        case constants.IMAGE:
          return fileTypes.IMAGE;
        case constants.AUDIO:
          return fileTypes.AUDIO;
        case constants.VIDEO:
        case constants.FULL:
          return fileTypes.VIDEO;
        default: return '';
      }
    },
    /**
         * Is this item is currently previewing
         */
    isPreviewing() {
      return this.galleryItemId === this.item.id;
    },
    /**
         * Limit thumb by available space to next item on timeline
         * @returns {*}
         */
    insertLength() {
      const { maxDraggingInsertLength } = this;
      const itemTime = this.item.time || this.itemThumbSetting.imageDefaultDuration;
      if (isNil(maxDraggingInsertLength) ||
                maxDraggingInsertLength >= itemTime) {
        return null;
      }
      return maxDraggingInsertLength * this.zoomedPxPerSec;
    },
    /**
         * Dynamic class to show how or can item will be drop on timeline
         * @returns {string}
         */
    draggingStatusClass() {
      let className = '';
      if (this.isDraggingOverDroppable) {
        if (!this.isCanDrop) {
          className = 'dragging-error';
        } else if (this.insertLength) {
          className = 'dragging-warning';
        } else {
          className = 'dragging-success';
        }
      }
      return className;
    },
  },
  methods: {
    ...mapActions('preview', {
      setPreviewItemId: 'setGalleryItemId',
    }),
    ...mapActions('dragging', [
      'resetDragging',
      'setDraggingPosition',
    ]),
    /**
         * Set this item iss previewing
         */
    setPreviewItem() {
      if (!this.playing) {
        this.setPreviewItemId({ galleryItemId: this.item.id });
      }
    },
    /**
         * Set position on current item dragging
         * @param params
         */
    onDragging(params) {
      // If this item is dragging over some droppable element
      // means on some timeline layer
      if (this.isDraggingOverDroppable) {
        // Get droppable element coordinates
        const { left: dropLeft, top: dropTop } = this.droppableCoordinates;
        // Set item current position passed by dragging component
        const { left: dragLeft } = params.position;
        // Do not position item out of left edge of droppable
        // while dragging is over droppable
        // (means cursor is over droppable)
        const left = dragLeft > dropLeft ? dragLeft : dropLeft;
        // top snap to top of droppable
        Object.assign(params.position, {
          top: dropTop,
          left,
        });
      }
      this.setDraggingPosition(params.position);
    },
    /**
         * on drag stop clear all saved params
         */
    onDragStop() {
      this.resetDragging();
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
    &--inverse {
      color: $text-invert;
    }
    &__box {
      padding: 0 4px 0;
    }
    & + & {
      margin-left: 8px;
    }
  }
  .gmg-item {
    height: 120px;
    width: 25%;
    padding: 0 12px 12px 0;

    &__thumb {
      background: center center/cover no-repeat $grey-light;
      cursor: move;
      display: flex;
      flex-direction: column;
      height: 100%;
      justify-content: space-between;
      position: relative;
    }
    &__pixelization {
      cover-all();
      opacity: 0;
      transition: all .3s;
      z-index: 1;
    }
    &:hover &__pixelization {
      opacity: 1;
    }
    &__header {
      fl-right();
      padding: 4px 8px;
      visibility: hidden;
      z-index: 2;
    }
    &:hover &__header {
      visibility: visible;
    }
    &__main {
      fl-center();
      width: 100%;
      z-index: 2;
    }
    &__footer {
      display: flex;
      flex-direction: column;
      height: 32px;
      z-index: 2;
      visibility: hidden;
    }
    &:hover &__footer {
      visibility: visible;
      padding: 0 8px;
    }
    &__name {
      fnt($text-invert, 10px, $weight-semibold, left);
      txt-ellipsis();
    }
    &__duration {
      fnt($text-invert, 10px, $weight-light, left);
    }
    &__preview-btn {
      fl-center();
      border-radius: 50%;
      border: 3px solid $white;
      background-color: $grey-dark;
      cursor: pointer;
      height: 36px;
      margin-top: 0;
      width: 36px;
      opacity: 0;
      padding-left: 2px;
    }
    &:hover &__preview-btn {
      opacity: 1;
      &:hover {
        opacity: .8;
      }
    }
  }

  @media (max-width: 1440px)
    .gmg-item
      width: calc(100% / 3)
</style>
