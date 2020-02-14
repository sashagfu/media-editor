<template>
  <div class="TimelineHeaderControls me-tl-header-control">
    <div class="me-tl-header-control__panel">
      <button
        :class="{'me-tl-header-control__item--disabled' : !canUndo}"
        class="me-tl-header-control__item me-tl-header-control__item--left"
        title="Undo"
        @click="undo"
      >
        <font-awesome-icon
          :icon="['fas', 'undo']"
          :class="{'fa-icon--disabled':!canUndo}"
          class="fa-icon"
        />
      </button>
      <button
        :class="{'me-tl-header-control__item--disabled' : !canRedo}"
        class="me-tl-header-control__item
                           me-tl-header-control__item--left
                           me-tl-header-control__item--left-last"
        title="Redo"
        @click="redo"
      >
        <font-awesome-icon
          :icon="['fas', 'redo']"
          :class="{'fa-icon--disabled':!canRedo}"
          class="fa-icon"
        />
      </button>
      <button
        v-popover:coverImageActions
        class="me-tl-header-control__item"
        title="Create cover image"
      >
        <font-awesome-icon
          :icon="['far', 'images']"
          class="fa-icon"
        />
      </button>
      <button
        :class="{'me-tl-header-control__item--disabled' : isRemoveDisabled}"
        class="me-tl-header-control__item"
        title="Remove selected"
        @click="removeItems"
      >
        <font-awesome-icon
          :icon="['far', 'trash-alt']"
          :class="{'fa-icon--disabled':isRemoveDisabled}"
          class="fa-icon"
        />
      </button>
      <button
        :class="{'me-tl-header-control__item--disabled' : isUnlinkDisabled}"
        class="me-tl-header-control__item"
        title="Unlink audio from video"
        @click="unlinkAudio"
      >
        <font-awesome-icon
          :icon="['fas', 'unlink']"
          :class="{'fa-icon--disabled':isUnlinkDisabled}"
          class="fa-icon"
        />
      </button>
      <button
        :class="{
          'me-tl-header-control__item--active' : isSplitTool,
          'me-tl-header-control__item--disabled' : isSplitDisabled,
        }"
        class="me-tl-header-control__item"
        title="Split tools"
        @click.stop="toggleSplitTool"
      >
        <font-awesome-icon
          :icon="['fas', 'cut']"
          :class="{
            'fa-icon--disabled':isSplitDisabled,
            'fa-icon--active': isSplitTool
          }"
          class="fa-icon"
        />
      </button>


      <el-popover
        ref="coverImageActions"
        placement="top"
        trigger="click"
      >
        <div class="me-tl-header-control__title">
          Cover Image
        </div>
        <el-button
          :disabled="isShowThumbDisabled"
          size="mini"
          @click="showThumb"
        >
          Show
        </el-button>
        <el-button
          :disabled="isCreateThumbDisabled"
          size="mini"
          @click="createThumb"
        >
          <font-awesome-icon
            v-if="isThumbSelected"
            :icon="['fas', 'check']"
            class="fa-icon fa-icon--active"
          />
          Select
        </el-button>
      </el-popover>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import moment from 'moment';

export default {
  name: 'TimelineHeaderControls',
  computed: {
    ...mapGetters('timeline', [
      'isSplitTool',
      'itemsCanUnlinkInSelectedItems',
      'selectedItems',
      'itemsUnderCursor',
      'cursorPosition',
    ]),
    ...mapGetters('project', [
      'items',
      'projectData',
    ]),
    ...mapGetters('player', [
      'playing',
    ]),
    ...mapGetters('project/undoRedo', [
      'canUndo',
      'canRedo',
    ]),
    isUnlinkDisabled() {
      return !this.itemsCanUnlinkInSelectedItems.length || this.playing;
    },
    isRemoveDisabled() {
      return !this.selectedItems.length || this.playing;
    },
    isSplitDisabled() {
      return !this.items.length || this.playing;
    },
    isCreateThumbDisabled() {
      return !this.itemsUnderCursor.length;
    },
    isThumbSelected() {
      return this.cursorPosition === moment.duration(this.projectData.thumbTime).asMilliseconds();
    },
    isShowThumbDisabled() {
      return !this.projectData.thumbTime;
    },
  },
  methods: {
    ...mapActions('timeline', [
      'toggleSplitTool',
      'removeSelectedItems',
      'setCursorPosition',
    ]),
    ...mapActions('project', [
      'unlinkAudioFromItems',
      'setCoverImage',
    ]),
    ...mapActions('project/undoRedo', [
      'undo',
      'redo',
    ]),
    unlinkAudio() {
      this.unlinkAudioFromItems(this.itemsCanUnlinkInSelectedItems);
    },
    removeItems() {
      if (!this.isRemoveDisabled) {
        this.removeSelectedItems();
      }
    },
    createThumb() {
      if (this.isThumbSelected) return;
      if (!this.isCreateThumbDisabled) {
        this.setCoverImage();
      }
    },
    showThumb() {
      const cursorPosition = moment.duration(this.projectData.thumbTime)
        .asMilliseconds();
      this.setCursorPosition({
        cursorPosition,
        manual: true,
      });
    },
  },
};
</script>

<style lang="stylus" scoped>
  @import '../../../../sass/front/components/bulma-theme';

  .fa-icon {
    color: $text-light;
    transition: all .2s;
    &:hover {
      opacity: 0.5;
    }
    &--disabled {
      color: $text-lighter;
    }
    &--active {
      color: $primary;
    }
    &__box {
      padding: 0 4px;
    }
  }

  .me-tl-header-control
    &__title
      fnt($text, 14px, $weight-light, left)
      margin-bottom 10px
</style>

<style lang="stylus">
  .el-button.is-disabled {
    color: #c0c4cc !important;
  }
</style>
