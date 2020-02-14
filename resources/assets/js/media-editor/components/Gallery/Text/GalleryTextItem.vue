<template>
  <draggable
    :class="{ 'me-gallery-item--active' : isPreviewing }"
    :item-id="item.id"
    :item-uuid="item.uuid"
    :cursor-at="{ left: 25, top: 25 }"
    :disabled="!isCanMove"
    class="GalleryTextItem me-gallery-list__item me-gallery-item gti"
    append-to="body"
    @dragging="onDragging"
    @dragstop="onDragStop"
  >
    <div class="me-gallery-item__left thumbnail">
      <div class="gti__thumb">
        <div
          class="gti__preview-btn"
          @click.prevent="setGalleryItemId({ galleryItemId: item.id })"
        >
          <font-awesome-icon
            :icon="['fas', 'font']"
            class="fa-icon fa-icon--pointer fa-icon--font"
          />
          <font-awesome-icon
            :icon="['fas', 'eye']"
            class="fa-icon fa-icon--pointer fa-icon--eye"
          />
        </div>
      </div>
      <div class="me-gallery-item__title-box gti__title-box">
        <input
          v-if="editable"
          :value="item.name"
          class="gti__inp"
          autofocus
          type="text"
          @keyup="setText"
          @keyup.enter="closeEdit"
          @keyup.esc="escEdit"
          @click.stop=""
        >
        <div
          v-if="editable"
          class="gti__inp-btn gti__inp-btn--enter"
          @click.stop="closeEdit"
        >
          <font-awesome-icon
            :icon="['fas', 'check']"
            class="fa-icon fa-icon--sm fa-icon--pointer"
          />
        </div>
        <div
          v-if="editable"
          class="gti__inp-btn gti__inp-btn--esc"
          @click.stop="escEdit"
        >
          <font-awesome-icon
            :icon="['fas', 'times']"
            class="fa-icon fa-icon--sm fa-icon--pointer"
          />
        </div>
        <div
          v-if="!editable"
          class="gti__title"
          @dblclick="setEditable(true)"
        >
          {{ item.name }}
        </div>
      </div>
    </div>
    <div class="me-gallery-item__right">
      <div
        class="gti__btn gti__btn--edit"
        @click.prevent="setEditingText(item)"
      >
        <font-awesome-icon
          :icon="['fas', 'pencil-alt']"
          class="fa-icon fa-icon--light fa-icon--pointer fa-icon--edit"
        />
      </div>
      <div
        class="gti__btn gti__btn--delete"
        @click.prevent="deleteText()"
      >
        <font-awesome-icon
          :icon="['far', 'trash-alt']"
          class="fa-icon fa-icon--light fa-icon--delete"
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
        show-as="slide"
      />
    </div>
  </draggable>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import UPDATE_SLIDE from 'Gql/slides/mutations/updateSlide.graphql';
import DELETE_SLIDE from 'Gql/slides/mutations/deleteSlide.graphql';
import Draggable from '../../common/Draggable';
import ItemThumbWrapper from '../../Thumbs/ItemThumbWrapper';
import * as images from '../../../config/images';

export default {
  name: 'GalleryTextItem',
  components: {
    Draggable,
    ItemThumbWrapper,
  },
  props: {
    item: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      editable: false,
      editableUndo: '',
    };
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
    ...mapGetters('project', [
      'id',
    ]),
    isCanMove() {
      return !this.playing;
    },
    thumbUrl() {
      if (this.item.thumb) return this.item.thumb;
      return images.TEXT_DEFAULT_THUMB;
    },
    isPreviewing() {
      return this.galleryItemId === this.item.id;
    },
    insertLength() {
      const itemTime = this.itemThumbSetting.imageDefaultDuration;
      return itemTime * this.zoomedPxPerSec;
    },
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
  mounted() {
    window.addEventListener('click', this.closeEdit, false);
  },
  beforeDestroy() {
    window.removeEventListener('click', this.closeEdit, false);
  },
  methods: {
    ...mapActions('gallery', [
      'deleteItemText',
      'setItemTextProperty',
    ]),
    ...mapActions('text', [
      'setEditingText',
    ]),
    ...mapActions('preview', [
      'setGalleryItemId',
    ]),
    ...mapActions('dragging', [
      'resetDragging',
      'setDraggingPosition',
    ]),
    // editing name of item actions
    setEditable(val) {
      if (val) {
        this.editableUndo = this.item.name;
      } else {
        this.editableUndo = '';
      }
      this.editable = val;
    },
    setText(e) {
      this.setItemTextProperty({
        id: this.item.id,
        uuid: this.item.uuid,
        property: 'name',
        value: e.srcElement.value,
      });
    },
    escEdit() {
      this.setItemTextProperty({
        id: this.item.id,
        uuid: this.item.uuid,
        property: 'name',
        value: this.editableUndo,
      });
      this.setEditable(false);
    },
    async deleteText() {
      try {
        await this.$apollo.mutate({
          mutation: DELETE_SLIDE,
          variables: {
            id: this.item.id,
          },
        });
        this.deleteItemText(this.item);
      } catch (e) {
        console.error(e);
        this.$notify.error({
          title: 'Error',
          message: this.trans('media_editor.texts_hasnt_delete'),
        });
      }
    },
    async closeEdit() {
      if (!this.editable) {
        return;
      }
      try {
        const slide = {
          id: this.item.id,
          uuid: this.item.uuid,
          projectId: this.id,
          name: this.item.name,
          effects: this.item.effects ? this.item.effects : [],
          fileType: this.item.fileType,
          texts: this.item.items,
        };
        await this.$apollo.mutate({
          mutation: UPDATE_SLIDE,
          variables: {
            slide,
          },
        });
        this.setEditable(false);
      } catch (e) {
        console.error(e);
        this.$notify.error({
          title: 'Error',
          message: this.trans('media_editor.texts_hasnt_renamed'),
        });
        this.escEdit();
      }
    },
    // preview action
    setPreviewItem() {
      this.setPreviewItemId({ galleryItemId: this.item.id });
    },
    // dragging actions
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
    onDragStop() {
      this.resetDragging();
    },
  },
};
</script>

<style lang="stylus" scoped>
    @import '../../../../../sass/front/components/bulma-theme';
    .gti {
      flex: 0 0 auto;
      &__thumb {
        fl-center();
        border-radius: 2px;
        border: 2px solid $border;
        cursor: pointer;
        height: 56px;
        margin: auto;
        transition: all .2s;
        width: 74px;
        &:hover .fa-icon--eye,
        & .fa-icon--eye {
          display: flex;
          opacity: 1;
        }
        & .fa-icon--eye,
        &:hover .fa-icon--font {
          display: none;
          opacity: 0;
        }
      }
      &__title-box {
        display: flex;
        height: 100%;
        flex-direction: row;
      }
      &__title {
        fnt($text, 14px, $weight-normal, left);
        cursor: text;
      }
      &:not(:last-child) {
        border-bottom: 1px solid $border;
      }
      &__btn {
        fl-center();
        cursor: pointer;
        width: 24px;
        height: 24px;
        border: 1px solid $border;
        border-radius: $radius;
        transition: all .3s;
        opacity: 0;
          &--delete:hover {
            border-color: $danger;
            & .fa-icon--delete {
              color: $danger;
            }
          }
      }
      &__btn + &__btn {
        margin-left: 8px;
      }
      &:hover &__btn {
        opacity: 1;
        &:hover {
          opacity: .8;
        }
      }
      &__inp {
        fnt($text, 14px, $weight-normal, left);
        background: transparent;
        border-radius: $radius 0 0 $radius;
        border: 1px solid $blue;
        border-right: none;
        height: 23px;
        outline: none;
        padding: 2px 4px;
      }
      &__inp-btn {
        fl-center();
        height: 23px;
        width: 23px;
        border: 1px solid $blue;
        &--enter {
          border-left: none;
          border-right: none;
        }
        &--esc {
          border-left: none;
          border-radius: 0 $radius $radius 0;
        }
      }
    }

    .fa-icon {
      color: $text-light;
      font-size: 12px;
      transition: all .3s;
      &--light {
        color: $text-lighter;
      }
      &--sm {
        font-size: 12px;
        color: $text-lighter;
        &:hover {
          opacity: .5;
        }
      }
      &--pointer {
        cursor: pointer;
      }
      &__box {
        padding: 0 4px 0;
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
        // flex-direction: column;
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
          cursor: pointer;

          .fa-circle {
            color: $grey-dark;
          }
        }

        &__left:hover &__preview-btn {
          opacity: 1;
        }
      }

      &--active &__preview-btn,
      &:hover &__preview-btn {
        opacity: 1;
      }
      &__opacity {
        opacity: 0;
      }
      &:hover &__opacity {
        opacity: 1;
        &:hover {
          opacity: .8;
        }
      }
    }

</style>
