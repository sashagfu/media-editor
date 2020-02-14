<template>
  <div class="ActionsBox actions">
    <el-dropdown
      trigger="hover"
      size="small"
      @command="handleCommand"
    >
      <div class="actions__dropdown-button">
        <font-awesome-icon
          :icon="['fas', 'ellipsis-h']"
          class="fa-icon fa-icon--invert"
        />
      </div>
      <el-dropdown-menu
        slot="dropdown"
        class="actions__menu"
      >
        <el-dropdown-item
          class="actions__item"
          command="edit"
        >
          {{ trans('common.edit_video') }}
        </el-dropdown-item>
        <el-dropdown-item
          v-if="!project.isPublished && !project.isRendered"
          class="actions__item"
          command="publish"
        >
          {{ trans('common.publish') }}
        </el-dropdown-item>
        <el-dropdown-item
          v-if="project.isDraft || project.isFailed"
          class="actions__item"
          command="render"
        >
          {{ trans('media_editor.render') }}
        </el-dropdown-item>
        <el-dropdown-item
          class="actions__item"
          command="share"
        >
          {{ trans('media_editor.share') }}
        </el-dropdown-item>
        <el-dropdown-item
          class="actions__item"
          command="rename"
        >
          {{ trans('common.edit_description') }}
        </el-dropdown-item>
        <el-dropdown-item
          v-if="project.isPublished"
          class="actions__item"
          command="change_thumbnail"
        >
          {{ trans('common.change_thumbnail') }}
        </el-dropdown-item>
        <el-dropdown-item
          class="actions__item"
          command="clone"
        >
          {{ trans('common.create_copy') }}
        </el-dropdown-item>
        <el-dropdown-item
          class="actions__item actions__item--danger"
          command="delete"
        >
          {{ trans('common.delete') }}
        </el-dropdown-item>
      </el-dropdown-menu>
    </el-dropdown>
    <!-- deleting project -->
    <el-dialog
      :visible.sync="isDelete"
      :append-to-body="true"
      width="30%"
      class="action-dialog"
    >
      <div
        slot="title"
        class="action-dialog__title"
      >
        <div class="action-dialog__title-text">
          {{ trans('common.warning') }}
        </div>
      </div>
      <div class="action-dialog__main">
        <font-awesome-icon
          :icon="['fas', 'exclamation-circle']"
          class="fa-icon fa-icon--warning"
        />
        <div class="action-dialog__main-text">
          {{ trans('projects.delete_permanent') }}
        </div>
      </div>
      <div
        slot="footer"
        class="action-dialog__footer"
      >
        <el-button
          plain
          size="small"
          @click="isDelete = false"
        >
          {{ trans('common.cancel') }}
        </el-button>
        <el-button
          type="danger"
          size="small"
          @click="handleDeleteProject"
        >
          <template v-if="projectsUpdateLoading">
            <font-awesome-icon
              :icon="['fas', 'spinner']"
              spin
              class="fa-icon fa-icon--spinner"
            />
          </template>
          <template v-else>
            {{ trans('common.ok') }}
          </template>
        </el-button>
      </div>
    </el-dialog>
    <!-- editing project -->
    <el-dialog
      v-if="isRename"
      :visible.sync="isRename"
      :append-to-body="true"
      class="action-dialog"
      @open="onRenameDialogOpen"
      @close="onRenameDialogClose"
    >
      <div
        slot="title"
        class="action-dialog__title"
      >
        <div class="action-dialog__title-text">
          {{ trans('projects.edit') }}
        </div>
      </div>
      <div
        v-if="!project.isPublished"
        class="input-box"
      >
        <label
          class="input-box__label"
          for="input-box__title"
        >
          {{ trans('projects.title') }}
        </label>
        <input
          id="input-box__title"
          ref="renameInput"
          v-model.trim="projectModel.title"
          type="text"
          name="title"
          class="input-box__input"
          @keyup.enter="handleRenameMedia"
          @keyup.esc="isRename = false"
        >
      </div>
      <div class="input-box">
        <label
          class="input-box__label"
          for="input-box__desc"
        >
          {{ trans('projects.description') }}
        </label>
        <textarea
          id="input-box__desc"
          ref="textarea"
          v-model.trim="projectModel.description"
          type="text"
          name="description"
          class="input-box__input input-box__input--textarea"
        />
      </div>
      <!-- tag -->
      <div class="input-box">
        <div class="input-box__label">
          {{ trans('projects.tags') }}
        </div>
        <div class="input-box__tags">
          <el-tag
            v-for="(tag, index) in projectModel.tags"
            :key="`tg-${index}`"
            :disable-transitions="false"
            type="info"
            size="mini"
            closable
            @close="handleClose(tag)"
          >
            {{ tag.name }}
          </el-tag>
          <span v-if="inputVisible">
            <el-input
              ref="saveTagInput"
              v-model="inputValue"
              :autofocus="true"
              size="mini"
              type="info"
              class="new-tag__input"
              @keyup.enter.native="handleInputConfirm"
              @blur="handleInputConfirm"
            />
          </span>
          <span v-else>
            <el-button
              plain
              class="new-tag__button"
              size="mini"
              type="info"
              @click="showInput"
            >
              {{ trans('projects.new_tag') }}
            </el-button>
          </span>
        </div>
      </div>
      <div class="input-box">
        <div class="input-box__label">
          Additional settings
        </div>
        <el-checkbox
          v-model="projectModel.pinned"
          class="input-box--checkbox"
        >
          {{ trans('projects.pin') }}
        </el-checkbox>
      </div>
      <div
        slot="footer"
        class="action-dialog__footer"
      >
        <el-button
          plain
          size="small"
          @click="isRename = false"
        >
          {{ trans('common.cancel') }}
        </el-button>
        <el-button
          type="info"
          size="small"
          @click="handleRenameProject"
        >
          <font-awesome-icon
            v-if="projectsUpdateLoading"
            :icon="['fas', 'spinner']"
            spin
            class="fa-icon fa-icon--spinner"
          />
          <font-awesome-icon
            v-else
            :icon="['fas', 'save']"
            class="fa-icon fa-icon--spinner"
          />
          <span class="action-dialog__btn-title">
            {{ trans('common.save') }}
          </span>
        </el-button>
      </div>
    </el-dialog>
    <!-- Changing thumbnail -->
    <ChangeThumbnailBox
      v-if="isChangingThumb"
      :project="project"
      :user="user"
      @close="isChangingThumb = false"
    />
  </div>
</template>

<script>
import autosize from 'autosize';
import Project from 'Models/Project';
import FETCH_PROJECTS from 'Gql/projects/queries/fetchProjects.graphql';
import DELETE_PROJECT from 'Gql/projects/mutations/deleteProject.graphql';
import UPDATE_PROJECT from 'Gql/projects/mutations/updateProject.graphql';
import RENDER_PROJECT from 'Gql/projects/mutations/renderProject.graphql';
import RENDER_PUBLISH_PROJECT from 'Gql/projects/mutations/renderPublishProject.graphql';
import CLONE_PROJECT from 'Gql/projects/mutations/cloneProject.graphql';
import ChangeThumbnailBox from '../ProfilePage/Projects/ChangeThumbnail/ChangeThumbnailBox';

export default {
  name: 'ActionsBox',
  components: { ChangeThumbnailBox },
  props: {
    project: {
      type: Object,
      required: true,
    },
    user: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      isDelete: false,
      isRename: false,
      projectModel: new Project(),
      projectsUpdateLoading: false,

      inputVisible: false,
      inputValue: '',
      isChangingThumb: false,
    };
  },
  created() {
    this.projectModel.id = this.project.id;
    this.projectModel.title = this.project.title;
    this.projectModel.description = this.project.description;
    this.projectModel.pinned = this.project.pinned;
    if (this.project.tags.length) {
      this.project.tags.forEach(i => this.projectModel.addTag(i));
    }
  },
  mounted() {
    autosize(this.$refs.textarea);
  },
  updated() {
    autosize(this.$refs.textarea);
  },
  methods: {
    handleClose(tag) {
      this.projectModel.tags.splice(this.projectModel.tags.indexOf(tag), 1);
    },
    showInput() {
      this.inputVisible = true;
    },
    handleInputConfirm() {
      const { inputValue } = this;
      if (inputValue) {
        this.projectModel.createTag(inputValue);
      }
      this.inputVisible = false;
      this.inputValue = '';
    },

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
        case 'edit': {
          this.handleEditProject();
          break;
        }
        case 'change_thumbnail': {
          this.isChangingThumb = true;
          break;
        }
        case 'clone': {
          this.handleCloneProject();
          break;
        }
        case 'render': {
          this.renderProject();
          break;
        }
        case 'publish': {
          this.publishProject();
          break;
        }
        case 'share': {
          this.shareProject();
          break;
        }
        default: {
          this.isDelete = false;
          this.isRename = false;
        }
      }
    },
    onRenameDialogOpen() {
      this.$nextTick(() => {
        this.$refs.renameInput.setSelectionRange(0, this.projectModel.title.length);
        this.$refs.renameInput.focus();
      });
    },
    onRenameDialogClose() {
      this.projectModel.title = this.title;
      this.projectModel.description = this.description;
    },
    handleCloneProject() {
      this.$apollo.mutate({
        mutation: CLONE_PROJECT,
        variables: {
          id: this.project.id,
        },
        update: (store, { data: { cloneProject } }) => {
          const fetchProjects = {
            query: FETCH_PROJECTS,
            variables: {
              userId: this.user.id,
            },
          };
          const data = store.readQuery(fetchProjects);
          data.fetchProjects.unshift(cloneProject);

          store.writeQuery({
            ...fetchProjects,
            data,
          });
        },
      });
    },
    handleEditProject() {
      this.$router.push({
        name: 'EditorPage',
        params: {
          id: this.project.id,
        },
      });
    },
    handleRenameProject() {
      this.projectsUpdateLoading = true;
      const {
        id,
        title,
        description,
        tags,
        pinned,
      } = this.projectModel;
      this.$apollo.mutate({
        mutation: UPDATE_PROJECT,
        variables: {
          project: {
            id,
            title,
            description,
            tags,
            pinned,
          },
        },
        update: (store) => {
          const fetchProjects = {
            query: FETCH_PROJECTS,
            variables: {
              userId: this.user.id,
            },
          };
          const data = store.readQuery(fetchProjects);
          const index = data.fetchProjects.findIndex(p => p.id === this.project.id);
          data.fetchProjects[index] = Object.assign(data.fetchProjects[index], {
            id,
            title,
            description,
            pinned,
          });
          store.writeQuery({
            ...fetchProjects,
            data,
          });
        },
      })
        .then(() => {
          this.projectsUpdateLoading = false;
          this.$notify({
            title: 'Success',
            message: this.trans('projects.renamed'),
            type: 'success',
          });
          this.isRename = false;
        })
        .catch(() => {
          this.projectsUpdateLoading = false;
          this.$notify.error({
            title: 'Error',
            message: this.trans('projects.cant_rename'),
          });
        });
    },
    handleDeleteProject() {
      if (this.projectsUpdateLoading) return;
      this.projectsUpdateLoading = true;
      this.$apollo.mutate({
        mutation: DELETE_PROJECT,
        variables: {
          id: this.project.id,
        },
        update: (store) => {
          const fetchProjects = {
            query: FETCH_PROJECTS,
            variables: {
              userId: this.user.id,
            },
          };
          const data = store.readQuery(fetchProjects);
          data.fetchProjects = data.fetchProjects.filter(p => p.id !== this.project.id);
          store.writeQuery({
            ...fetchProjects,
            data,
          });
        },
      })
        .then(() => {
          this.projectsUpdateLoading = false;
          this.$notify({
            title: 'Success',
            message: this.trans('projects.delete_successful'),
            type: 'success',
          });
          this.isDelete = false;
        })
        .catch(() => {
          this.projectsUpdateLoading = false;
          this.$notify({
            title: 'Error',
            message: this.trans('projects.cant_delete'),
            type: 'error',
          });
        });
    },
    async publishProject() {
      try {
        await this.$apollo.mutate({
          mutation: RENDER_PUBLISH_PROJECT,
          variables: {
            id: this.project.id,
          },
        });
        this.$notify({
          title: 'Success',
          message: this.trans('projects.publish_started'),
          type: 'success',
        });
      } catch (e) {
        this.$notify.error({
          title: 'Error',
          message: this.trans('projects.cant_create'),
        });
      }
    },
    async renderProject() {
      try {
        await this.$apollo.mutate({
          mutation: RENDER_PROJECT,
          variables: {
            id: this.project.id,
          },
        });
        this.$notify({
          title: 'Success',
          message: this.trans('projects.rendering_started'),
          type: 'success',
        });
      } catch (e) {
        this.$notify.error({
          title: 'Error',
          message: this.trans('projects.cant_create'),
        });
      }
    },
    shareProject() {
      this.$emit('share-project');
    },
  },
};
</script>

<style lang="stylus">
  @import '../../../../sass/front/components/bulma-theme';

  .new-tag__input .el-input__inner {
    fnt($text-light, 12px, $weight-light, left);
    border-color: $info;
    height: 20px;
    line-height: 19px;
    padding: 0 8px;
    width: 80px;
  }

  .input-box--checkbox {
    margin-top: 3px;

    .el-checkbox__label {
      font-size: 12px;
    }
  }
</style>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .el-tag {
    margin: 4px 0 0 8px;
  }

  .new-tag {
    &__button {
      margin: 4px 0 0 8px;
      height: 20px;
      line-height: 19px;
      padding: 0 6px;
    }

    &__input {
      height: 20px;
      margin: 4px 0 0 8px;
      vertical-align: bottom;
    }
  }

  .fa-icon {
    fnt($text-light, 1rem, $weight-normal, left);
    transition: all .3s;
    cursor: pointer;

    &:hover {
      color: $text;
    }

    &--warning {
      font-size: 32px;
      color: $warning;

      &:hover {
        color: $warning;
      }
    }

    &--invert {
      color: $text-invert;

      &:hover {
        color: $grey-lighter;
      }
    }

    &--edit {
      font-size: 12px;
      color: $primary;
    }

    &--spinner {
      color: $text-invert;
      font-size: 16px;
      margin: -2px;

      &:hover {
        color: $text-invert;
      }
    }
  }

  .el-button:hover .fa-icon--edit {
    color: $text-invert;
  }

  .actions {
    &__dropdown-button {
      fl-center();
      cursor: pointer;
      height: 12px;
      width: 26px;
    }

    &__item {
      fnt($text-light, 12px, $weight-normal, left);
      transition: color .3s;
      line-height: 26px;
      padding: 0 12px;

      &:hover {
        color: $text;
        background-color: $grey-lighter;
      }

      &--danger {
        color: $red;

        &:hover {
          color: $red;
        }
      }
    }

  }

  .action-dialog {
    &__title {
      fl-left();
    }

    &__title-text {
      fnt($text, 18px, $weight-normal, left);
    }

    &__input {
      fnt($text, 12px, $weight-normal, left);
      border: 1px solid $border;
      border-radius: 3px 0 0 3px;
      height: 40px;
      margin-right: -1px;
      outline: none;
      padding-left: 16px;
      width: 100%;
      transition: box-shadow .3s;

      &:focus {
        box-shadow: inset 0 1px 2px rgba(10, 10, 10, .1);
      }
    }

    &__main {
      fl-center();
    }

    &__main-text {
      fnt($text, 14px, $weight-normal, left);
      padding-left: 12px;
    }

    &__btn-title {
      margin-left: 8px;
    }
  }

  .input-box {
    align-items: flex-start;
    background-color: $background-light;
    border-radius: $radius;
    border: 1px solid $border;
    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
    padding: 0 18px;
    position: relative;

    &__label {
      fnt($text-light, 10px, $weight-normal, left);
      display: flex;
      flex-direction: column;
      height: 20px;
      justify-content: flex-end;
    }

    &__input {
      fnt($text-light, 12px, $weight-normal, left);
      border: none;
      height: 32px;
      outline: none;
      width: 100%;
      resize: none;

      &--textarea {
        padding: 8px 0;
      }
    }

    &__tags {
      fl-left();
      flex-wrap: wrap;
      margin-left: -8px;
      padding-bottom: 4px;
      width: 100%;
    }

    &--is-success {
      border-color: $success;
    }

    &--is-warning {
      border-color: $warning;
    }

    &__error {
      fnt($warning-invert, .75rem, $weight-normal, left);
      background-color: $warning;
      border-radius: $radius;
      border: 1px solid $warning;
      box-shadow: 0 2px 2px 0 rgba($black-bis, 0.16), 0 0 0 1px rgba($black-bis, 0.08);
      cursor: pointer;
      padding: 4px 12px;
      position: absolute;
      top: 50px;
      transition: box-shadow .3s;
      z-index: 2;

      &:hover {
        box-shadow: 0 3px 8px 0 rgba($black-bis, 0.2), 0 0 0 1px rgba($black-bis, 0.08);
      }

      &--accept-box {
        left: 20px;
        top: 20px;
      }
    }

    &__diamond-error {
      deg45();
      background-color: $warning;
      height: 8px;
      position: absolute;
      top: -4px;
      width: 8px;
      z-index: 1;
    }
  }
</style>
