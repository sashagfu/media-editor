<template>
  <div class="MenuMedia m-media">
    <el-dropdown
      :hide-on-click="false"
      @command="handleCommand"
    >
      <div class="m-media__menu-btn">
        <font-awesome-icon
          :icon="['fas', 'ellipsis-h']"
          class="fa-icon"
        />
      </div>
      <el-dropdown-menu
        slot="dropdown"
        class="m-media__dropdown"
      >
        <el-dropdown-item
          class="m-media__item"
          command="rename"
        >
          {{ trans('common.rename') }}
        </el-dropdown-item>
        <el-dropdown-item
          class="m-media__item
                               m-media__item--danger"
          command="delete"
        >
          {{ trans('common.delete') }}
        </el-dropdown-item>
      </el-dropdown-menu>
    </el-dropdown>
    <!-- dialog for deleting -->
    <el-dialog
      :visible.sync="isDelete"
      :append-to-body="true"
      class="m-dialog"
      width="30%"
    >
      <div
        slot="title"
        class="m-dialog__title"
      >
        <div class="m-dialog__title-text">
          {{ trans('media_editor.delete_media') }}
        </div>
      </div>
      <div class="m-dialog__main">
        <font-awesome-icon
          :icon="['fas', 'exclamation-circle']"
          class="fa-icon fa-icon--warning"
        />
        <div class="m-dialog__main-text">
          {{ trans('media_editor.delete_permanently') }}
        </div>
      </div>
      <el-row
        slot="footer"
        class="m-dialog__footer"
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
          @click="handleDeleteMedia"
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
      </el-row>
    </el-dialog>
    <!-- dialog for renaming -->
    <el-dialog
      :visible.sync="isRename"
      :append-to-body="true"
      class="m-dialog"
      width="30%"
      @open="onRenameDialogOpen"
      @close="onRenameDialogClose"
    >
      <div
        slot="title"
        class="del-media-modal__title"
      >
        <div class="del-media-modal__title-text">
          {{ trans('media_editor.rename_media') }}
        </div>
      </div>
      <div class="input-box">
        <label
          class="input-box__label"
          for="input-box__title"
        >
          {{ trans('media_editor.title_media') }}
        </label>
        <input
          id="input-box__title"
          ref="renameInput"
          v-model.trim="titleModel"
          class="input-box__input"
          type="text"
          name="title"
          @keyup.enter="handleRenameMedia"
          @keyup.esc="isRename = false"
        >
      </div>
      <div
        slot="footer"
        class="m-dialog__footer"
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
          @click="handleRenameMedia"
        >
          <template v-if="projectsUpdateLoading">
            <font-awesome-icon
              :icon="['fas', 'spinner']"
              spin
              class="fa-icon fa-icon--spinner"
            />
          </template>
          <template v-else>
            {{ trans('common.rename') }}
          </template>
        </el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import DELETE_VIDEO_FROM_PROJECT from 'Gql/videos/mutations/deleteVideoFromProject.gql';
import DELETE_AUDIO_FROM_PROJECT from 'Gql/audios/mutations/deleteAudioFromProject.graphql';
import DELETE_IMAGE_FROM_PROJECT from 'Gql/images/mutations/deleteImageFromProject.graphql';
import FETCH_PROJECT_MEDIA from 'Gql/projects/queries/fetchProjectMedia.graphql';

export default {
  name: 'MenuMedia',
  props: {
    file: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      isDelete: false,
      isRename: false,
      titleModel: this.file.name,
      projectsUpdateLoading: false,
    };
  },
  computed: {
    ...mapGetters('project', [
      'items',
      'projectData',
    ]),
    canDelete() {
      const foundIndex = this.items
        .findIndex(item => String(item.file.uuid) === String(this.file.uuid));
      return foundIndex === -1;
    },
  },
  methods: {
    ...mapActions('gallery', [
      'deleteItem',
    ]),
    handleCommand(command) {
      switch (command) {
        case 'delete': {
          this.deleting();
          break;
        }
        case 'rename': {
          this.isRename = true;
          break;
        }
        default: {
          this.isDelete = false;
          this.isRename = false;
        }
      }
    },
    handleRenameMedia() {
      // this.updateProject({
      //     id: this.id,
      //     title: this.titleModel,
      // })
      //     .then((response) => {
      //         this.isRename = false;
      //         return response;
      //     });
    },
    handleDeleteMedia() {
      this.projectsUpdateLoading = true;
      this.$apollo.mutate({
        mutation: this.gqlMutation(),
        variables: {
          projectId: String(this.projectData.id),
          id: String(this.file.id),
        },
        update: (store, { data: response }) => {
          const fetchProjectMedia = {
            query: FETCH_PROJECT_MEDIA,
            variables: {
              projectId: this.projectData.id,
            },
          };
          const data = store.readQuery(fetchProjectMedia);

          /*
            The first response key will be our mutation
            deleteImage || deleteAudio || deleteVideo
           */
          data.fetchProjectMedia = data.fetchProjectMedia
            .filter(item => item.uuid !== response[Object.keys(response)[0]].uuid);

          store.writeQuery({
            ...fetchProjectMedia,
            data,
          });
        },
      })
        .then(() => {
          this.projectsUpdateLoading = false;
          this.$notify({
            title: 'Success',
            message: 'This file deleted from project',
            type: 'success',
          });
          this.isDelete = false;
          this.deleteItem({ item: this.file });
        })
        .catch(() => {
          this.projectsUpdateLoading = false;
          this.$notify.error({
            title: 'Error',
            message: 'This file cant\'t delete from project',
          });
        });
    },
    deleting() {
      if (!this.canDelete) {
        this.$notify.error({
          title: 'Error',
          message: 'Media file can\'t delete from gallery, file used in project',
        });
        return;
      }
      this.isDelete = true;
    },
    onRenameDialogOpen() {
      this.$nextTick(() => {
        this.$refs.renameInput.setSelectionRange(0, this.titleModel.length);
        this.$refs.renameInput.focus();
      });
    },
    onRenameDialogClose() {
      this.titleModel = this.file.name;
    },
    gqlMutation() {
      let mutation;
      if (this.file.fileType === 'image') {
        mutation = DELETE_IMAGE_FROM_PROJECT;
      } else if (this.file.fileType === 'audio') {
        mutation = DELETE_AUDIO_FROM_PROJECT;
      } else {
        mutation = DELETE_VIDEO_FROM_PROJECT;
      }
      return mutation;
    },
  },
};
</script>

<style lang="stylus"
       scoped>
  @import '../../../../../sass/front/components/bulma-theme';
  .fa-icon {
    color: $text-invert;
    font-size: 12px;
    transition: all .3s;

    &:hover {
      color: $grey-lighter;
    }

    &--warning {
      font-size: 32px;
      color: $warning;

      &:hover {
        color: $warning;
      }
    }

    &--spinner {
      color: $text-invert;
      font-size: 16px;
      margin: -2px;
    }
  }

  .m-media {
    &__menu-btn {
      fl-center();
      cursor: pointer;
      height: 8px;
      width: 22px;
    }

    &__dropdown {
      padding: 6px 0;
      margin-top: 8px;
    }

    &__item {
      fnt($text-light, 12px, $weight-normal, left);
      line-height: 26px;
      padding: 0 12px;
      transition: color .3s;

      &:hover {
        color: $text;
        background-color: $grey-lighter;
      }

      &--danger {
        color: $danger;

        &:hover {
          color: $danger;
          background-color: rgba($danger, 0.3);
        }
      }
    }
  }

  .m-dialog {
    &__title {
      fl-left();
    }

    &__title-text {
      fnt($text, 18px, $weight-normal, left);
    }

    &__main {
      fl-center();
    }

    &__main-text {
      fnt($text, 14px, $weight-normal, left);
      padding-left: 12px;
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
