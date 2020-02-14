<template>
  <draggable
    :class="{ 'me-gallery-item--active' : isPreviewing }"
    :item-id="item.id"
    :item-uuid="item.uuid"
    :cursor-at="{ left: 25, top: 25 }"
    :disabled="!isCanMove"
    class="GalleryMediaListItem me-gallery-list__item me-gallery-item"
    append-to="body"
    @dragging="onDragging"
    @dragstop="onDragStop"
  >
    <div class="me-gallery-item__left thumbnail">
      <div
        :style="{ backgroundImage: `url(${thumbUrl})` }"
        class="me-gallery-item__thumb"
      >
        <div
          class="me-gallery-item__preview-btn me-gallery-item__preview-btn"
          @click.prevent="setPreviewItem"
        >
          <font-awesome-icon
            :icon="['fas', previewIcon]"
            class="fa-icon fa-icon--inverse"
          />
        </div>
      </div>
      <div class="me-gallery-item__title-box">
        <div
          class="me-gallery-item__title"
          v-html="item.name"
        />
        <div class="me-gallery-item__sub-title">
          <duration :ms="item.time"/>
        </div>
      </div>
    </div>
    <div class="me-gallery-item__right">
      <div class="me-gallery-item__opacity">
        <delete-media
          :file="item"
        />
      </div>
    </div>
    <div
      slot="dragImage"
      :class="draggingStatusClass"
      class="slot-for-drag-image"
    >
      <item-thumb-wrapper
        :file="item"
        :insert-length="insertLength"
      />
    </div>
  </draggable>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import _ from 'lodash';
import Draggable from '../../common/Draggable';
import Duration from '../../common/Duration';
import ItemThumbWrapper from '../../Thumbs/ItemThumbWrapper';
import DeleteMedia from '../Manage/DeleteMedia';
import * as fileTypes from '../../../config/file-types';
import * as images from '../../../config/images';

export default {
  name: 'GalleryMediaListItem',
  components: {
    Draggable,
    ItemThumbWrapper,
    Duration,
    DeleteMedia,
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
      let icon;

      switch (this.item.fileType) {
        case fileTypes.IMAGE:
          icon = 'eye';
          break;
        case fileTypes.AUDIO:
        case fileTypes.VIDEO:
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
      switch (this.item.fileType) {
        case fileTypes.IMAGE:
          return this.item.filePath;
        case fileTypes.AUDIO:
          return images.AUDIO_DEFAULT_THUMB;
        case fileTypes.VIDEO:
          return this.item.thumbPath || images.VIDEO_DEFAULT_THUMB;
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
      if (
        _.isNil(maxDraggingInsertLength) ||
                maxDraggingInsertLength >= itemTime
      ) {
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
      this.setPreviewItemId({ galleryItemId: this.item.id });
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

  .me-gallery-item {
    // $b: &;
    fl-between();
    position: relative;
    &:hover {
      cursor: move;
    }
    &__left {
      fl-left();
    }
    &__right {
      fl-right();
    }
    &__title-box {
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding-left: 12px;
    }
    &__thumb {
      fl-center();
      background: center center/cover no-repeat;
      border-radius: 3px;
      flex-direction: column;
      height: 56px;
      margin: auto;
      width: 56px;
    }
    &__title {
      fnt($text, 12px, $weight-semibold, left);
      background-color: transparent;
      bottom: 0;
      margin: 0;
      overflow: hidden;
      padding: 0;
      text-overflow: ellipsis;
      transition: all .3s;
      white-space: nowrap;
      width: 100%;
    }
    &__sub-title {
      fnt($text-light, 10px, $weight-normal, left);
    }

    &__preview-btn {
      fl-center();
      background-color: $grey-dark;
      border-radius: 50%;
      border: 3px solid $white;
      cursor: pointer;
      height: 36px;
      margin-top: 0;
      opacity: 0;
      transition: all .3s;
      width: 36px;
      .fa {
        font-size: 16px;
      }
      .fa-circle {
        opacity: .7;
      }
      .fa-play {
        margin-left: 2px;
      }
      &:hover {
        opacity: 0.8;
      }
      /*&__left:hover &__preview-btn {
        opacity: 1;
      } */
    }
    &--active &__preview-btn,
    &:hover &__preview-btn {
      opacity: 1;
      &:hover {
        opacity: 0.8;
      }
    }
    &__opacity {
      opacity: 0;
    }
    &:hover &__opacity {
      opacity: 1;
    }
  }

</style>
