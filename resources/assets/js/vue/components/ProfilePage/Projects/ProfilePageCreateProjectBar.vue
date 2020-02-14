
<template>
  <div class="ProfilePageCreateProjectBar pp-create-project-bar box">
    <div
      :class="[
        'pp-create-project-bar__form',
        {'pp-create-project-bar__form--is-show': open}
      ]"
    >
      <div class="pp-create-project-bar__form-row">
        <div class="pp-create-project-bar__form-column">
          <div class="input-box">
            <label
              class="input-box__label"
              for="input-box__title"
            >
              {{ trans('projects.title') }}
            </label>
            <input
              id="input-box__title"
              v-model="project.title"
              type="text"
              name="title"
              class="input-box__input"
            >
          </div>
        </div>
        <div class="pp-create-project-bar__form-column">
          <div class="input-box">
            <div class="input-box__label">
              {{ trans('projects.tags') }}
            </div>
            <div class="input-box__tags">
              <el-tag
                v-for="(tag, index) in project.tags"
                :key="`tg-${index}`"
                :disable-transitions="false"
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
                  @click="showInput"
                >
                  {{ trans('projects.new_tag') }}
                </el-button>
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="pp-create-project-bar__form-row">
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
            v-model="project.description"
            type="text"
            name="description"
            class="input-box__input input-box__input--textarea"
          />
        </div>
      </div>
      <div class="pp-create-project-bar__form-row">
        <UploadFilesForm
          :media-type="mediaType"
          :multiple="action === constants.ACTION_CREATE"
        />
      </div>
      <div
        v-if="files.length"
        class="pp-create-project-bar__form-row pp-create-project-bar__form-row--paddingless"
      >
        <transition-group
          name="list"
          tag="div"
          class="pp-create-project-bar__list"
        >
          <upload-item
            v-for="(file, index) in files"
            :key="index"
            :file="file"
          />
        </transition-group>
      </div>
      <div class="pp-create-project-bar__form-row pp-create-project-bar__form-row--pull-right">
        <el-button
          v-if="canRemoveOrStartUploadFiles"
          size="small"
          plain
          @click="clearFiles"
        >
          {{ trans('projects.remove_files') }}
        </el-button>
        <el-button
          v-if="canCancelUpload"
          size="small"
          plain
          @click.prevent="cancelUpload"
        >
          {{ trans('projects.cancel') }}
        </el-button>
        <el-button
          :disable="!canRemoveOrStartUploadFiles"
          type="primary"
          size="small"
          @click="onCreateProjectClick"
        >
          <div class="pp-create-project-bar__action-btn">
            <div
              v-if="projectsCreateLoading"
              class="fa-icon__box"
            >
              <font-awesome-icon
                :icon="['fas', 'spinner']"
                spin
                class="fa-icon fa-icon--spinner"
              />
            </div>
            <div
              v-if="action === constants.ACTION_PUBLISH"
              class="pp-create-project-bar__action-btn-title">
              {{ trans('projects.publish_project') }}
            </div>
            <div
              v-else
              class="pp-create-project-bar__action-btn-title">
              {{ files.length ? trans('projects.create_upload') : trans('projects.create') }}
            </div>
          </div>
        </el-button>
      </div>
    </div>
    <div class="pp-create-project-bar__plate">
      <div
        class="pp-create-project-bar__item"
        @click="togglePanel(constants.ACTION_CREATE)"
      >
        <div class="pp-create-project-bar__icon">
          <font-awesome-icon
            :icon="['fas', 'cut']"
            class="fa-icon"
          />
        </div>
        <div class="pp-create-project-bar__title">
          {{ trans('profile_page.create_project') }}
        </div>
      </div>
      <div
        class="pp-create-project-bar__item"
        @click="togglePanel(constants.ACTION_CREATE)"
      >
        <div
          :class="[
            'pp-create-project-bar__btn',
            {'pp-create-project-bar__btn--up': open}
          ]"
        >
          <font-awesome-icon
            :icon="['fas', 'chevron-down']"
            class="fa-icon"
          />
        </div>
      </div>
      <div
        class="pp-create-project-bar__item"
        @click="togglePanel(constants.ACTION_PUBLISH)"
      >
        <div class="pp-create-project-bar__icon">
          <font-awesome-icon
            :icon="['fas', 'cloud-upload-alt']"
            class="fa-icon"
          />
        </div>
        <div class="pp-create-project-bar__title">
          {{ trans('profile_page.publish_project') }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import autosize from 'autosize';
import Project from 'Models/Project';
import CREATE_PROJECT from 'Gql/projects/mutations/createProject.graphql';
import FETCH_PROJECTS from 'Gql/projects/queries/fetchProjects.graphql';
import RENDER_PUBLISH_PROJECT from 'Gql/projects/mutations/renderPublishProject.graphql';
import { mapGetters, mapActions } from 'vuex';
import * as constants from 'Helpers/constants';

import UploadFilesForm from './Upload/UploadFilesForm';
import UploadItem from './Upload/UploadItem';

export default {
  name: 'ProfilePageCreateProjectBar',
  components: {
    UploadFilesForm,
    UploadItem,
  },
  data() {
    return {
      constants,
      action: '',
      open: false,
      project: new Project(),
      projectsCreateLoading: false,

      inputVisible: false,
      inputValue: '',
    };
  },
  computed: {
    ...mapGetters('upload', [
      'isCreateProjectDialogOpen',
      //
      'files',
      'uploader',
    ]),
    ...mapGetters('general', [
      'activeUser',
      'newProjectId',
    ]),
    canRemoveOrStartUploadFiles() {
      return this.files.some(file => !file.uploading);
    },
    canCancelUpload() {
      return this.files.some(file => file.uploading);
    },
    mediaType() {
      return this.action === constants.ACTION_PUBLISH ? constants.VIDEO : constants.ALL;
    },
  },
  watch: {
    files(newVal, oldVal) {
      if (oldVal.length
        && !newVal.length
        && this.newProjectId
        && this.action === constants.ACTION_PUBLISH
      ) {
        this.$apollo.mutate({
          mutation: RENDER_PUBLISH_PROJECT,
          variables: {
            id: this.newProjectId,
          },
          update: (store, { data: { publishProject } }) => {
            const fetchProjects = {
              query: FETCH_PROJECTS,
              variables: {
                userId: parseInt(this.activeUser.id, 10),
              },
            };
            const data = store.readQuery(fetchProjects);
            const idx = data.fetchProjects.findIndex(p => p.id === publishProject.id);
            if (idx !== -1) {
              data.fetchProjects[idx] = Object.assign(data.fetchProjects[idx], publishProject);
              store.writeQuery({
                ...fetchProjects,
                data,
              });
            }
          },
        })
          .then(() => {
            this.$notify({
              title: 'Success',
              message: this.trans('projects.publish_started'),
              type: 'success',
            });
            this.action = '';
            this.open = false;
          })
          .catch((error) => {
            console.error(error);
          });
      }
    },
  },
  created() {
    this.createUploader();
  },
  mounted() {
    Object.assign(this.project, {
      title: 'My Video',
      description: '',
    });
  },
  updated() {
    autosize(this.$refs.textarea);
  },
  methods: {
    ...mapActions('upload', [
      'cancelUpload',
      'clearFiles',
      'createUploader',
      'startUpload',
      'uploadReadyProject',
    ]),
    ...mapActions('general', [
      'setNewProject',
    ]),
    togglePanel(action) {
      if (!action) {
        if (!this.action) {
          this.action = constants.ACTION_CREATE;
          this.open = !this.open;
          return;
        }
        this.action = '';
        this.open = !this.open;
        return;
      }
      if (!this.action) {
        this.action = action;
        this.open = !this.open;
        return;
      }
      if (this.action === action) {
        this.action = '';
        this.open = !this.open;
        return;
      }
      this.action = action;
    },
    closePanel() {
      this.open = false;
    },
    // manage tags
    handleClose(tag) {
      this.project.tags.splice(this.project.tags.indexOf(tag), 1);
    },
    showInput() {
      this.inputVisible = true;
    },
    handleInputConfirm() {
      const { inputValue } = this;
      if (inputValue) {
        this.project.createTag(inputValue);
      }
      this.inputVisible = false;
      this.inputValue = '';
    },
    async onCreateProjectClick() {
      try {
        this.projectsCreateLoading = true;
        const {
          id,
          title,
          description,
          tags,
        } = this.project;
        let project = {};
        const { data: { createProject: newProject } } = await this.$apollo.mutate({
          mutation: CREATE_PROJECT,
          variables: {
            project: {
              id,
              title,
              description,
              tags,
            },
          },
          update: (store, { data: { createProject } }) => {
            const fetchProjects = {
              query: FETCH_PROJECTS,
              variables: {
                userId: this.activeUser.id,
              },
            };
            project = Object.assign(new Project(), createProject);
            const data = store.readQuery(fetchProjects);
            data.fetchProjects.unshift(project);
            store.writeQuery({
              ...fetchProjects,
              data,
            });
          },
        });
        this.setNewProject(newProject);
        this.action = '';
        this.open = false;
        this.$notify({
          title: 'Success',
          message: this.trans('projects.created'),
          type: 'success',
        });
        if (this.files.length) {
          this.uploadReadyProject(this.action === constants.ACTION_PUBLISH);
          await this.startUpload();
        }
      } catch (e) {
        this.$notify.error({
          title: 'Error',
          message: this.trans('projects.cant_create'),
        });
      } finally {
        this.projectsCreateLoading = false;
      }
    },
  },
};
</script>

<style lang="stylus">
  @import '../../../../../sass/front/components/bulma-theme';

  .new-tag__input .el-input__inner {
    fnt($text-light, 12px, $weight-light, left);
    border-color: $primary;
    height: 20px;
    line-height: 19px;
    padding: 0 8px;
    width: 80px;
  }
</style>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .fa-icon {
    color $text-invert
    font-size 20px
    transition all .2s

    &:hover {
      opacity 0.8
    }

    &__box {
      margin -4px 8px -4px 0
    }
  }

  .el-tag {
    margin 4px 0 0 8px
  }

  .new-tag {
    &__button {
      height 20px
      line-height 19px
      margin 4px 0 0 8px
      padding 0 6px
    }

    &__input {
      height 20px
      margin 4px 0 0 8px
      vertical-align bottom
    }
  }

  .input-box {
    align-items: flex-start;
    background-color: $background-light;
    border-radius: $radius;
    border: 1px solid $border;
    display: flex;
    flex-direction: column;
    padding: 0 18px;
    position: relative;
    width: 100%;

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

  .pp-create-project-bar {
    width: 100%;

    &__plate {
      fl-between();
      background-color: $primary;
      height: 72px;
    }

    &__item {
      fl-center();
      cursor: pointer;
      height: 100%;
      width: 33%;
    }

    &__icon {
      fl-center();
      height: 48px;
      width: 48px;
    }

    &__title {
      fnt($text-invert, 16px, $weight-normal, center);
    }

    &__btn {
      fl-center();
      cursor: pointer;
      height: 48px;
      transition: all .3s;
      width: 48px;

      &--up {
        transform: rotate(180deg);
      }
    }

    &__form {
      height: 0;
      transition: all .3s;
      overflow-y: hidden;
      opacity: 0;

      &--is-show {
        padding: 24px;
        height: auto; // 200px;
        opacity: 1;
      }
    }

    &__form-row {
      fl-left();
      padding: 12px 0;

      &:first-child {
        padding-top: 0;
      }

      &:last-child {
        padding-bottom: 0;
      }

      &--paddingless {
        padding: 0;
      }

      &--pull-right {
        fl-right();
      }
    }

    &__form-column {
      padding: 0 13px;
      width: 50%;

      &:first-child {
        padding-left: 0;
      }

      &:last-child {
        padding-right: 0;
      }
    }

    &__list {
      width: 100%;
    }

    &__action-btn {
      fl-center();
    }
  }

  // animation
  .list {
    &-enter-active, &-leave-active {
      transition: all .5s;
    }

    &-enter {
      opacity: 0;
      transform: translateY(-30px);
    }

    &-leave-to {
      opacity: 0;
      transform: translateY(30px);
    }
  }

  .list-move {
    transition: transform .5s;
  }
</style>
