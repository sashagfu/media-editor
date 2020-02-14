<template>
  <div class="TopBar me-bl-main-tale">
    <div class="me-bl-main-tale__left">
      <div class="top-bar__breadcrumbs">
        <a
          class="top-bar__breadcrumbs-item"
          href="/profile#projects"
        >
          <!--Projects >-->
          {{ trans('media_editor.projects') }}
        </a>
        <span class="top-bar__breadcrumbs-arrow">
          &nbsp;>&nbsp;
        </span>
        <span class="top-bar__breadcrumbs-title">
          {{ title }}
        </span>
      </div>
    </div>
    <div class="me-bl-main-tale__right">
      <TopBarProjectStatus
        class="top-bar__status"
      />
      <el-dropdown
        type="primary"
        split-button
        size="mini"
        @click="handlePublishEditProject()"
      >
        <template v-if="!projectData.isPublished">
          {{ trans('media_editor.publish') }}
        </template>
        <template v-if="projectData.isPublished">
          {{ trans('common.save') }}
        </template>
        <el-dropdown-menu slot="dropdown">
          <el-dropdown-item
            v-if="projectData.isFiled || projectData.isDraft"
          >
            <div
              class="top-bar__dropdown-item"
              @click="renderProject"
            >
              <font-awesome-icon
                :icon="['fas', 'eye']"
                class="fa-icon fa-icon--dr-dwn"
              />
              <div class="top-bar__dropdown-title">
                {{ trans('media_editor.render') }}
              </div>
            </div>
          </el-dropdown-item>
          <el-dropdown-item>
            <div
              class="top-bar__dropdown-item"
              @click="handlePublishEditProject(constants.ACTION_RENAME)"
            >
              <font-awesome-icon
                :icon="['fas', 'info']"
                class="fa-icon fa-icon--dr-dwn"
              />
              <div class="top-bar__dropdown-title top-bar__dropdown-title--info">
                {{ trans('media_editor.project_info') }}
              </div>
            </div>
          </el-dropdown-item>
          <el-dropdown-item>
            <div
              class="top-bar__dropdown-item"
              @click="shareProject"
            >
              <font-awesome-icon
                :icon="['fas', 'share']"
                class="fa-icon fa-icon--dr-dwn"
              />
              <div class="top-bar__dropdown-title">
                {{ trans('media_editor.share') }}
              </div>
            </div>
          </el-dropdown-item>
          <el-dropdown-item divided>
            <div
              class="top-bar__dropdown-item"
              @click="deleteProjectDialogVisible = true"
            >
              <font-awesome-icon
                :icon="['far', 'trash-alt']"
                class="fa-icon fa-icon--dr-dwn"
              />
              <div class="top-bar__dropdown-title">
                {{ trans('media_editor.delete_project') }}
              </div>
            </div>
          </el-dropdown-item>
        </el-dropdown-menu>
      </el-dropdown>
    </div>
    <!--share project-->
    <ChatShareBox
      v-if="shareProjectDialogVisible"
      :project="projectData"
    />
    <!--delete ptoject-->
    <el-dialog
      :visible.sync="deleteProjectDialogVisible"
      width="30%"
    >
      <div
        slot="title"
        class="top-bar__dialog-title"
      >
        {{ trans('common.warning') }}
      </div>
      <div class="top-bar__dialog-main">
        <font-awesome-icon
          :icon="['fas', 'exclamation-circle']"
          class="fa-icon fa-icon--box fa-icon--warning"
        />
        {{ trans('projects.delete_permanent') }}
      </div>

      <span
        slot="footer"
        class="dialog-footer"
      >
        <el-button
          plain
          size="small"
          @click="deleteProjectDialogVisible = false"
        >
          {{ trans('common.cancel') }}
        </el-button>
        <el-button
          plain
          type="danger"
          size="small"
          @click="deleteProject"
        >
          <div
            v-if="deletingProject"
            class="fa-icon__box fa-icon__box"
          >
            <font-awesome-icon
              :icon="['fas', 'spinner']"
              spin
              class="fa-icon fa-icon--el fa-icon--invert"
            />
          </div>
          <template v-if="deletingProject">
            {{ trans('common.deleting') }}
          </template>
          <template v-else>
            {{ trans('common.delete') }}
          </template>
        </el-button>
      </span>
    </el-dialog>
    <!--edit project name-->
    <el-dialog
      :visible.sync="editProjectDialogVisible"
      :append-to-body="true"
      class="action-dialog"
      @open="onRenameDialogOpen"
      @close="onRenameDialogClose"
    >
      <!-- header -->
      <div
        slot="title"
        class="action-dialog__title"
      >
        <div class="action-dialog__title-text">
          <template v-if="dialogAction === constants.ACTION_RENAME">
            {{ trans('media_editor.project_info') }}
          </template>
          <template v-else-if="dialogAction === constants.ACTION_SAVE_AS_NEW">
            {{ trans('media_editor.save_as_new') }}
          </template>
          <template v-else>
            {{ trans('projects.publish_project') }}
          </template>
        </div>
      </div>
      <div
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
          @keyup.enter="handleActionProject"
          @keyup.esc="isRename = false"
        >
      </div>
      <!-- description -->
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
            type="primary"
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
              autofocus
              size="mini"
              type="primary"
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
              type="primary"
              @click="showInput"
            >
              {{ trans('projects.new_tag') }}
            </el-button>
          </span>
        </div>
      </div>
      <!-- footer -->
      <div
        slot="footer"
        class="action-dialog__footer"
      >
        <el-button
          plain
          size="small"
          @click="editProjectDialogVisible = false"
        >
          {{ trans('common.cancel') }}
        </el-button>
        <el-button
          type="primary"
          size="small"
          @click="handleActionProject"
        >
          <template v-if="projectsActionLoading">
            <font-awesome-icon
              :icon="['fas', 'spinner']"
              spin
              class="fa-icon fa-icon--spinner"
            />
          </template>
          <template v-else>
            <template v-if="dialogAction === constants.ACTION_RENAME">
              {{ trans('common.rename') }}
            </template>
            <template v-else-if="dialogAction === constants.ACTION_SAVE_AS_NEW">
              {{ trans('common.save') }}
            </template>
            <template v-else>
              {{ trans('common.publish') }}
            </template>
          </template>
        </el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import Project from 'Models/Project';
import * as constants from 'Helpers/constants';
import { cloneDeep } from 'Helpers/utils';

import UPDATE_PROJECT from 'Gql/projects/mutations/updateProject.graphql';
import PUBLISH_PROJECT from 'Gql/projects/mutations/publishProject.graphql';
import RENDER_PUBLISH_PROJECT from 'Gql/projects/mutations/renderPublishProject.graphql';
import RENDER_PROJECT from 'Gql/projects/mutations/renderProject.graphql';
import CLONE_PROJECT from 'Gql/projects/mutations/cloneProject.graphql';
import CHANGE_THUMBNAIL from 'Gql/projects/mutations/changeThumbnail.graphql';

import TopBarProjectStatus from './TopBarProjectStatus';
import ChatShareBox from '../../../vue/components/ChatWidget/ChatShareBox';

export default {
  name: 'TopBar',
  components: {
    TopBarProjectStatus,
    ChatShareBox,
  },
  data() {
    return {
      constants,
      projectModel: new Project(),
      deleteProjectDialogVisible: false,
      deletingProject: false,
      editProjectDialogVisible: false,
      shareProjectDialogVisible: false,
      dialogAction: '',
      projectsActionLoading: false,
      // variables of tags
      inputVisible: false,
      inputValue: '',
    };
  },
  computed: {
    ...mapGetters('project', [
      'id',
      'title',
      'projectData',
      'saved',
    ]),
    ...mapGetters('general', [
      'activeUser',
    ]),
  },
  watch: {
    id() {
      this.changeProjectData();
    },
    title() {
      this.changeProjectData();
    },
  },
  mounted() {
    this.changeProjectData();
  },
  methods: {
    ...mapActions('project', [
      'renameProject',
    ]),
    shareProject() {
      this.shareProjectDialogVisible = true;
    },
    changeProjectData() {
      this.projectModel.id = this.id;
      this.projectModel.title = this.title;
      this.projectModel.description = this.projectData.description;
      if (this.projectData.tags.length) {
        this.projectData.tags.forEach(i => this.projectModel.addTag(i));
      }
    },
    // methods of tags
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
    // methods of dialog
    onRenameDialogOpen() {
      this.$nextTick(() => {
        this.$refs.renameInput.setSelectionRange(0, this.projectModel.title.length);
        this.$refs.renameInput.focus();
      });
    },
    onRenameDialogClose() {
      this.projectModel.title = this.title;
      this.projectModel.description = this.projectData.description;
      this.projectModel.tags = [];
      this.projectData.tags.forEach(i => this.projectModel.addTag(i));
    },

    handlePublishEditProject(customAction = null) {
      let action = '';
      if (customAction) {
        action = customAction;
      } else if (this.projectData.isRendered) {
        action = constants.ACTION_PUBLISH;
      } else if (this.projectData.isPublished) {
        action = constants.ACTION_SAVE_AS_NEW;
      } else {
        action = constants.ACTION_RENDER_PUBLISH;
      }
      this.editProjectDialogVisible = true;
      this.dialogAction = action;
    },

    async saveAsNewProject() {
      return this.$apollo.mutate({
        mutation: CLONE_PROJECT,
        variables: {
          id: this.projectData.id,
        },
        update: async (store, { data: { cloneProject } }) => {
          const project = cloneDeep(this.projectData);
          project.id = cloneProject.id;
          project.title = this.projectModel.title;

          project.value = project.value.map((item, idx) => {
            const { id, uuid, object } = cloneProject.value[idx];

            return Object.assign(item, { id, uuid, object: cloneDeep(object) });
          });

          await this.$apollo.mutate({
            mutation: UPDATE_PROJECT,
            variables: {
              project,
            },
          });

          this.$router.push({
            name: 'EditorPage',
            params: {
              id: cloneProject.id,
            },
          });
        },
      });
    },

    async handleActionProject() {
      if (this.projectsActionLoading) return;
      this.projectsActionLoading = true;
      try {
        if (this.dialogAction === constants.ACTION_RENAME) {
          const {
            id,
            title,
            description,
            tags,
          } = this.projectModel;
          await this.$apollo.mutate({
            mutation: UPDATE_PROJECT,
            variables: {
              project: {
                id,
                title,
                description,
                tags,
              },
            },
          });
          this.$notify({
            title: 'Success',
            message: this.trans('projects.renamed'),
            type: 'success',
          });
          this.renameProject({ title, description, tags });
        }
        if (this.dialogAction === constants.ACTION_SAVE_AS_NEW) {
          await this.saveAsNewProject();
        }
        if (this.dialogAction === constants.ACTION_PUBLISH) {
          await this.$apollo.mutate({
            mutation: PUBLISH_PROJECT,
            variables: {
              id: this.projectModel.id,
            },
          });
          this.$notify({
            title: 'Success',
            message: this.trans('projects.created'),
            type: 'success',
          });
          this.goToProjects();
        }
        if (this.dialogAction === constants.ACTION_RENDER_PUBLISH) {
          await this.$apollo.mutate({
            mutation: RENDER_PUBLISH_PROJECT,
            variables: {
              id: this.projectModel.id,
            },
          });
          this.$notify({
            title: 'Success',
            message: this.trans('projects.created'),
            type: 'success',
          });
          this.goToProjects();
        }
      } catch (e) {
        if (this.dialogAction === constants.ACTION_RENAME) {
          this.$notify.error({
            title: 'Error',
            message: this.trans('projects.cant_rename'),
          });
        }
        if (this.dialogAction === constants.ACTION_PUBLISH) {
          this.$notify.error({
            title: 'Error',
            message: this.trans('projects.cant_create'),
          });
        }
      } finally {
        this.projectsActionLoading = false;
      }
    },

    async renderProject() {
      try {
        await this.$apollo.mutate({
          mutation: RENDER_PROJECT,
          variables: {
            id: this.projectModel.id,
          },
        });
        this.$notify({
          title: 'Success',
          message: this.trans('projects.created'),
          type: 'success',
        });
        this.goToProjects();
      } catch (e) {
        this.$notify.error({
          title: 'Error',
          message: this.trans('projects.cant_create'),
        });
      }
    },

    async changeThumbnail() {
      await this.$apollo.mutate({
        mutation: CHANGE_THUMBNAIL,
        variables: {
          id: this.projectModel.id,
        },
      });
      this.$notify({
        title: 'Success',
        message: this.trans('projects.thumbnail_updated'),
        type: 'success',
      });
      this.goToProjects();
    },

    deleteProject() {
      this.deletingProject = true;
      this.goToProjects();
    },

    goToProjects() {
      this.$router.push({
        name: 'ProfilePage',
        params: {
          username: this.activeUser.username,
        },
        hash: '#projects',
      });
    },
  },
};
</script>

<style lang="stylus" scoped>
  @import '../../../../sass/front/components/bulma-theme';

  .fa-icon {
    color     : $text-light;
    font-size : 1rem;
    &:hover {
      color : $text-lighter;
    }
    &--invert {
      color : $white;
      &:hover {
        color : $white-bis;
      }
    }
    &--el {
      margin : -2px;
    }
    &--dr-dwn {
      font-size : 12px;
    }
    &--pointer {
      cursor : pointer;
    }
    &--disabled {
      color  : $grey-lighter;
      cursor : auto;
      &:hover {
        color : $grey-lighter;
      }
    }
    &--spinner {
      color     : $text-invert;
      font-size : 16px;
      margin    : -2px;
      &:hover {
        color : $text-invert;
      }
    }
    &--warning {
      color     : $warning;
      font-size : 2rem;
    }
    &--box {
      margin : 0 8px;
    }
    &__box {
      padding : 0 4px 0;
      &--right {
        padding-left : 0;
      }
    }
  }

  .top-bar {
    &__breadcrumbs {
       fl-left();
    }
    &__breadcrumbs-item {
       fnt($primary, 16px, $weight-semibold, left);
      &:hover {
        text-decoration : underline;
      }
    }
    &__breadcrumbs-arrow {
       fnt($primary, 16px, $weight-semibold, left);
    }
    &__breadcrumbs-title {
       fnt($text, 16px, $weight-semibold, left);
    }
    &__status {
      margin-right : 12px;
    }
    &__dropdown-item {
       fl-left();
    }
    &__dropdown-title {
       fl-left();
      padding-left : 6px;
      &--info {
        padding-left : 14px;
      }
    }
    &__dialog-title {
       fl-left();
    }
    &__dialog-main {
       fl-center();
    }
  }

  .el-tag {
    margin : 4px 0 0 8px;
  }

  .new-tag {
    &__button {
      margin      : 4px 0 0 8px;
      height      : 20px;
      line-height : 19px;
      padding     : 0 6px;
    }
    &__input {
      height         : 20px;
      margin         : 4px 0 0 8px;
      vertical-align : bottom;
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
      border        : 1px solid $border;
      border-radius : 3px 0 0 3px;
      height        : 40px;
      margin-right  : -1px;
      outline       : none;
      padding-left  : 16px;
      width         : 100%;
      transition    : box-shadow .3s;

      &:focus {
        box-shadow : inset 0 1px 2px rgba(10, 10, 10, .1);
      }
    }
    &__main {
      fl-center();
    }
    &__main-text {
      fnt($text, 14px, $weight-normal, left);
      padding-left : 12px;
    }
  }

  .input-box {
    align-items      : flex-start;
    background-color : $background-light;
    border-radius    : $radius;
    border           : 1px solid $border;
    display          : flex;
    flex-direction   : column;
    margin-bottom    : 20px;
    padding          : 0 18px;
    position         : relative;
    &__label {
      fnt($text-light, 10px, $weight-normal, left);
      display         : flex;
      flex-direction  : column;
      height          : 20px;
      justify-content : flex-end;
    }
    &__input {
      fnt($text-light, 12px, $weight-normal, left);
      border  : none;
      height  : 32px;
      outline : none;
      width   : 100%;
      resize  : none;
      &--textarea {
        padding : 8px 0;
      }
    }
    &__tags {
      fl-left();
      flex-wrap      : wrap;
      margin-left    : -8px;
      padding-bottom : 4px;
      width          : 100%;
    }
    &--is-success {
      border-color : $success;
    }
    &--is-warning {
      border-color : $warning;
    }
    &__error {
      fnt($warning-invert, .75rem, $weight-normal, left);
      background-color : $warning;
      border-radius    : $radius;
      border           : 1px solid $warning;
      box-shadow       : 0 2px 2px 0 rgba($black-bis, 0.16), 0 0 0 1px rgba($black-bis, 0.08);
      cursor           : pointer;
      padding          : 4px 12px;
      position         : absolute;
      top              : 50px;
      transition       : box-shadow .3s;
      z-index          : 2;
      &:hover {
        box-shadow : 0 3px 8px 0 rgba($black-bis, 0.2), 0 0 0 1px rgba($black-bis, 0.08);
      }
      &--accept-box {
        left : 20px;
        top  : 20px;
      }
    }
    &__diamond-error {
      deg45();
      background-color : $warning;
      height           : 8px;
      position         : absolute;
      top              : -4px;
      width            : 8px;
      z-index          : 1;
    }
  }
</style>

<style lang="stylus">
  @import '../../../../sass/front/components/bulma-theme';

  .el-dropdown-menu__item:hover {
    & .fa-icon--dr-dwn {
      color : $primary;
    }
  }

  .action-dialog .new-tag__input .el-input__inner {
     fnt($text-light, 12px, $weight-light, left);
    border-color : $primary;
    height       : 20px;
    line-height  : 19px;
    padding      : 0 8px;
    width        : 80px;
  }
</style>
