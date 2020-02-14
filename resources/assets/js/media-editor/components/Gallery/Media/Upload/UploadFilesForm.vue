<template>
  <div class="UploadFilesForm up-files-form">
    <div
      :class="{'up-files-form__dropbox--drag-over': dragOver}"
      class="up-files-form__dropbox"
      @dragover.prevent="onDragFile()"
      @dragleave.prevent="onDragLeave()"
      @drop.prevent="onDropFile($event)"
    >
      <label class="up-files-form__input-label">
        <input
          ref="fileInput"
          class="up-files-form__input-file"
          type="file"
          multiple
          accept="audio/*, video/*, image/*"
          @change="filesChange($event)"
          @click="resetInputFile"
        >
      </label>
      <font-awesome-icon
        :icon="['fas', 'cloud-upload-alt']"
        class="fa-icon"
        @click="clickOnInput"
      />
      <div class="up-files-form__title">
        Drop your file here or <span class="up-files-form__sub-title">
          click to upload
        </span>
      </div>
    </div>
  </div>
</template>


<script>
import { mapGetters, mapActions } from 'vuex';

export default {
  name: 'UploadFilesForm',
  data() {
    return {
      fileCounter: 0,
      dragOver: false,
    };
  },
  computed: {
    ...mapGetters('upload', [
      'files',
      'uploader',
    ]),
  },
  methods: {
    ...mapActions('upload', [
      'addFile',
    ]),
    clickOnInput() {
      this.$refs.fileInput.click();
    },
    onDragLeave() {
      this.dragOver = false;
    },
    onDropFile(e) {
      const { files } = e.dataTransfer;
      Array
        .from(Array(files.length).keys())
        .forEach((x) => {
          this.onAddFile(files[x]);
        });
    },
    onDragFile() {
      this.dragOver = true;
    },
    filesChange(e) {
      const { files } = e.target;
      Array
        .from(Array(files.length).keys())
        .forEach((x) => {
          this.onAddFile(files[x]);
        });
    },

    resetInputFile() {
      this.$refs.fileInput.value = '';
    },

    onAddFile(file) {
      if (!file) return;
      this.addFile(this.getFileObject(file));
    },
    getFileObject(file) {
      const fileObj = {
        id: this.fileCounter += 1,
        name: file.name,
        file,
        fileType: file.type.split('/')[0],
        uploaded: false,
        uploading: false,
        xAmzHeadersAtInitiate: {
          'x-amz-acl': 'public-read',
        },
        progressPercentages: 0,
      };
      return fileObj;
    },
  },
};
</script>

<style lang="stylus" scoped>
    @import '../../../../../../sass/front/components/bulma-theme';
    .fa-icon {
        color: $text-light;
        font-size: 1.75rem;
        transition: all .3s;
    }
    .up-files-form {
        &__dropbox {
            align-items: center;
            border-radius: 8px;
            border: 1px dashed $border;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 200px;
            position: relative;
            transition: all .3s;
            &:hover {
                border-color: $primary;
                & .fa-icon {
                    color: $primary;
                }
            }
            &--drag-over {
                border-color: $primary;
                & .fa-icon {
                    color: $primary;
                }
            }
        }
        &__input-label {
          cover-all();
          cursor: pointer;
        }
        &__input-file {
            display: none;
        }
        &__title {
            fnt($text, 14px, $weight-normal, left);
        }
        &__sub-title {
            fnt($primary, 14px, $weight-normal, left);
        }
    }
</style>

