<template>
  <div class="MediaEditorProjectsItemActions actions">
    <el-dropdown
      trigger="click"
      size="small"
      @command="handleCommand"
    >
      <div class="actions__dropdown-button">
        <font-awesome-icon
          :icon="['fas', 'ellipsis-h']"
          class="fa-icon "
        />
      </div>
      <el-dropdown-menu
        slot="dropdown"
        class="actions__menu"
      >
        <el-dropdown-item
          class="actions__item"
          command="rename"
        >
          Rename
        </el-dropdown-item>
        <el-dropdown-item
          class="actions__item actions__item--danger"
          command="delete"
        >
          Delete
        </el-dropdown-item>
      </el-dropdown-menu>
    </el-dropdown>
    <el-dialog
      :visible.sync="isDelete"
      class="action-dialog"
      title="Delete project"
    >
      <div class="action-dialog__text">
        Do you want to delete project?
      </div>
      <div
        slot="footer"
        class="action-dialog__footer"
      >
        <button
          class="action-dialog__button"
          @click="isDelete = false"
        >
          Cancel
        </button>
        <button
          class="action-dialog__button action-dialog__button--danger"
          @click="handleDeleteProject"
        >
          Delete
        </button>
      </div>
      <div
        v-loading="true"
        v-if="projectsUpdateLoading"
        class="action-dialog__loading"
      />
    </el-dialog>
    <el-dialog
      :visible.sync="isRename"
      title="Rename"
      class="action-dialog"
      @open="onRenameDialogOpen"
      @close="onRenameDialogClose"
    >
      <input
        ref="renameInput"
        v-model.trim="titleModel"
        type="text"
        class="action-dialog__input"
        @keyup.enter="handleRenameProject"
        @keyup.esc="isRename = false"
      >
      <div
        slot="footer"
        class="action-dialog__footer"
      >
        <button
          class="action-dialog__button"
          @click="isRename = false"
        >
          Cancel
        </button>
        <button
          class="action-dialog__button action-dialog__button--success"
          @click="handleRenameProject"
        >
          Rename
        </button>
      </div>
      <div
        v-loading="true"
        v-if="projectsUpdateLoading"
        class="action-dialog__loading"
      />
    </el-dialog>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';

export default {
  name: 'MediaEditorProjectsItemActions',
  props: {
    id: {
      type: Number,
      required: true,
      validator: val => val > 0,
    },
    title: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      isDelete: false,
      isRename: false,
      titleModel: this.title,
    };
  },
  computed: {
    ...mapGetters('mediaEditorProjects', [
      'projectsUpdateLoading',
    ]),
  },
  methods: {
    ...mapActions('mediaEditorProjects', [
      'deleteProject',
      'updateProject',
    ]),
    handleCommand(command) {
      switch (command) {
        case 'delete': {
          this.isDelete = true;
          break;
        }
        case 'rename': {
          this.isRename = true;
          break;
        }
        default: {
          this.isDelete = true;
          this.isRename = true;
        }
      }
    },
    onRenameDialogOpen() {
      this.$nextTick(() => {
        this.$refs.renameInput.setSelectionRange(0, this.titleModel.length);
        this.$refs.renameInput.focus();
      });
    },
    onRenameDialogClose() {
      this.titleModel = this.title;
    },
    handleRenameProject() {
      this.updateProject({
        id: this.id,
        title: this.titleModel,
      })
        .then((response) => {
          this.isRename = false;
          return response;
        });
    },
    handleDeleteProject() {
      this.deleteProject(this.id)
        .then((response) => {
          this.isDelete = false;
          return response;
        });
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .fa-icon
    fnt($text-light, 1rem, $weight-normal, left)
    transition all .3s
    cursor pointer
    &:hover
      clolor $text

  .actions
    &__dropdown-button
      fl-center()
      cursor pointer
      heigh 26px
      width 26px
    &__menu
      background-color $white-bis
      border-radius 3px
      border 1px solid $border
      display flex
      flex-direction column
      margin-top 0

    .popper__arrow
      border-bottom-color $border

    &__item
      fnt($text-light, 12px, $weight-normal, left)
      transition color .3s
      &:hover
        color $text
        background-color $grey-lighter
      &--danger
        color $red
        &:hover
          color $red

  .action-dialog
    &__input
      fnt($text, 12px, $weight-normal, left)
      border 1px solid $border
      border-radius 3px 0 0 3px
      height 40px
      margin-right -1px
      outline none
      padding-left 16px
      width 100%
      transition box-shadow .3s
      &:focus
        box-shadow inset 0 1px 2px rgba(10, 10, 10, .1)

    &__loading
      cover-all()

    &__button
      fnt($text-light, 11px, $weight-bold, center)
      border-radius 5px
      border 1px solid $border
      cursor pointer
      display inline-block
      line-height 1.25
      padding 12px 30px
      transition all 0.15s ease-in-out
      user-select none
      vertical-align middle
      white-space nowrap
      &--danger
        background-color $danger
        border-color $danger
        color $background-light
      &--success
        background-color $success
        border-color $success
        color $background-light
      &:hover
        opacity .8

</style>
