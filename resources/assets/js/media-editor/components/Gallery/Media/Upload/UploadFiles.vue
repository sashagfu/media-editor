<template>
  <div class="UploadFiles upload-files">
    <upload-files-form/>
    <div class="me-upload-main__progress">
      <transition-group
        name="list"
        tag="div"
      >
        <upload-item
          v-for="(file, index) in files"
          :key="index"
          :file="file"
        />
      </transition-group>
      <transition name="item">
        <div
          class="upload-files__ctr-button"
        >
          <button
            v-if="canRemoveOrStartUploadFiles"
            class="upload-files__btn
                                   upload-files__btn--remove"
            @click="clearFiles"
          >
            {{ trans('media_editor.remove_files') }}
          </button>
          <button
            v-if="canCancelUpload"
            class="upload-files__btn
                                   upload-files__btn--cancel"
            @click.prevent="cancelUpload"
          >
            {{ trans('media_editor.cancel') }}
          </button>
          <button
            v-if="!uploading"
            :class="{'disable': !canRemoveOrStartUploadFiles}"
            class="upload-files__btn
                                   upload-files__btn--success"
            @click.prevent="startUpload"
          >
            {{ trans('media_editor.start_upload') }}
          </button>
          <button
            v-if="uploading"
            class="upload-files__btn
                                   upload-files__btn--hide"
            @click.prevent="hideDialog"
          >
            {{ trans('media_editor.hide') }}
          </button>
        </div>
      </transition>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import UploadItem from './UploadItem';
import UploadFilesForm from './UploadFilesForm';

export default {
  name: 'UploadFiles',
  components: {
    UploadItem,
    UploadFilesForm,
  },
  computed: {
    ...mapGetters('upload', [
      'files',
      'uploader',
    ]),
    canRemoveOrStartUploadFiles() {
      return this.files.some(file => !file.uploading);
    },
    canCancelUpload() {
      return this.files.some(file => file.uploading);
    },
    uploading() {
      return this.files.length && this.files.some(file => file.uploading);
    },
  },
  created() {
    this.createUploader();
  },
  methods: {
    ...mapActions('upload', [
      'setProcessing',
      'cancelUpload',
      'clearFiles',
      'closeDialog',
      'createUploader',
      'startUpload',
    ]),
    hideDialog() {
      this.closeDialog();
    },
  },
};
</script>

<style lang="stylus" scoped>
    @import '../../../../../../sass/front/components/bulma-theme';

    .upload-files {
        transition: all .5s;

        &__form {
            padding: 12px 0;
        }
        &__ctr-button {
            padding-top: 12px;
            display: flex;
            justify-content: flex-end;
            transition: transform .5s;
        }
        &__btn {
            fnt($text-invert, 12px, $weight-semibold, left);
            fl-center();
            border-radius: 3px;
            cursor: pointer;
            height: 36px;
            outline: none;
            transition: all .3s;
            padding: 0 12px;

            &--success {
                background-color: $primary;
                border: 1px solid $primary;
                &:hover {
                    background-color: rgba($primary, .7);
                }
                &.disable {
                    opacity: .5;
                    cursor: not-allowed;
                    &:hover {
                        background-color: $primary;
                    }
                }
            }
            &--hide {
              background-color: $blue;
              border: 1px solid $blue;
              &:hover {
                background-color: rgba($blue, .7);
              }
              &.disable {
                opacity: .5;
                cursor: not-allowed;
                &:hover {
                  background-color: $blue;
                }
              }
            }
            &--alert {
                color: $text;
                border: 1px solid $grey;
                &:hover {
                    background-color: rgba($grey-light, .3);
                }
                &.disable {
                    opacity: .5;
                    cursor: not-allowed;
                    &:hover {
                        background-color: $white;
                    }
                }
            }
            &--remove, &--cancel {
                color: $danger;
                border: 1px solid $danger;
                &:hover {
                    background-color: rgba($danger, .1);
                }
                &.disable {
                    opacity: .5;
                    cursor: not-allowed;
                    &:hover {
                        background-color: $white;
                    }
                }
            }
        }
        &__btn + &__btn {
            margin-left: 12px;
        }
    }
    .list, .item {
        &-enter-active, &-leave-active {
            transition: all .5s;
        }
        &-enter, &-leave-to {
            opacity: 0;
            transform: translateY(-30px);
        }
    }
    .list-leave-to {
        opacity: 0;
        transform: translateY(30px);
    }
    .list-move {
        transition: transform .5s;
    }
</style>
