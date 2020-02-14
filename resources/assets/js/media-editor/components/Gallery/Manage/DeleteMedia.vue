<template>
  <div class="DeleteMedia del-media">
    <div
      class="del-media__btn del-media__btn--delete"
      @click="open"
    >
      <font-awesome-icon
        :icon="['far', 'trash-alt']"
        class="fa-icon fa-icon--light fa-icon--delete"
      />
    </div>
    <el-dialog
      :visible.sync="dialogVisible"
      :before-close="handleClose"
      :append-to-body="true"
      title="Tips"
      width="30%"
      custom-class="del-media__modal"
    >
      <div
        slot="title"
        class="del-media-modal__title"
      >
        <div class="del-media-modal__title-text">
          {{ trans('media_editor.delete_media') }}
        </div>
      </div>
      <div class="del-media-modal__main">
        <font-awesome-icon
          :icon="['fas', 'exclamation-circle']"
          class="fa-icon fa-icon--warning"
        />
        <div class="del-media-modal__main-text">
          {{ trans('media_editor.delete_permanently') }}
        </div>
      </div>
      <el-row
        slot="footer"
        class="dialog-footer del-media-modal__footer"
      >
        <el-button
          plain
          size="small"
          class="del-media-modal__btn del-media-modal__btn--cancel"
          @click="dialogVisible = false"
        >
          {{ trans('common.cancel') }}
        </el-button>
        <el-button
          type="danger"
          size="small"
          class="del-media-modal__btn del-media-modal__btn--ok"
          @click="deleteMedia"
        >
          <template v-if="deletingMedia">
            <font-awesome-icon
              :icon="['fas', 'spinner']"
              spin
              class="fa-icon fa-icon--ok"
            />
          </template>
          <template v-else>
            {{ trans('common.ok') }}
          </template>
        </el-button>
      </el-row>
    </el-dialog>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import DELETE_VIDEO_FROM_PROJECT from 'Gql/videos/mutations/deleteVideoFromProject.gql';
import DELETE_AUDIO_FROM_PROJECT from 'Gql/audios/mutations/deleteAudioFromProject.graphql';
import DELETE_IMAGE_FROM_PROJECT from 'Gql/images/mutations/deleteImageFromProject.graphql';

export default {
  name: 'DeleteMedia',
  props: {
    file: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      dialogVisible: false,
      deletingMedia: false,
    };
  },
  computed: {
    ...mapGetters('project', [
      'items',
      'projectData',
    ]),
    canDelete() {
      const foundIndex = this.items
        .findIndex(item => String(item.file.id) === String(this.file.id));
      return foundIndex === -1;
    },
  },
  methods: {
    ...mapActions('gallery', [
      'deleteItem',
    ]),
    handleClose() {
      this.dialogVisible = false;
    },
    open() {
      if (!this.canDelete) {
        this.$notify.error({
          title: 'Error',
          message: 'Media file can\'t delete from gallery, file used in project',
        });
        return;
      }
      this.dialogVisible = true;
    },
    deleteMedia() {
      this.deletingMedia = true;
      this.$apollo.mutate({
        mutation: this.getGqlMutation(),
        variables: {
          projectId: String(this.projectData.id),
          id: String(this.file.id),
        },
      })
        .then(() => {
          this.deletingMedia = false;
          this.$notify({
            title: 'Success',
            message: 'This file deleted from project',
            type: 'success',
          });
          this.dialogVisible = false;
          this.deleteItem({ item: this.file });
        })
        .catch(() => {
          this.deletingMedia = false;
          this.$notify.error({
            title: 'Error',
            message: 'This file cant\'t delete from project',
          });
        });
    },
    getGqlMutation() {
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

<style lang="stylus" scoped>
    @import '../../../../../sass/front/components/bulma-theme';
    .fa-icon {
        color: $grey-light;
        font-size: 12px;
        transition: all .2s;
        &--warning {
            font-size: 32px;
            color: $warning;
        }
        &--ok {
            color: $text-invert;
            font-size: 16px;
            margin: -2px;
        }
    }

    .del-media {
        &__btn {
            fl-center();
            background-color: $white;
            border-radius: $radius;
            border: 1px solid $border;
            cursor: pointer;
            height: 24px;
            transitions: all .3s;
            width: 24px;
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
        &__dialog {
            min-height: 200px;
        }
    }
    .del-media-modal {
        &__title {
            fl-left();
            padding: 15px 15px 10px 15px;
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
        &__footer {
            fl-right();
        }
        &__btn + &__btn {
            margin-left: 12px;
        }
    }
</style>
