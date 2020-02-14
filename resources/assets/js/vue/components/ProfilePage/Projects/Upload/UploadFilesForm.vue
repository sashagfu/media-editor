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
          :accept="getMediaTypeAccept"
          :multiple="multiple"
          type="file"
          class="up-files-form__input-file"
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
        {{ trans('projects.drop_file') }}
        <span class="up-files-form__sub-title">
          {{ trans('projects.click_upload') }}
        </span>
      </div>
    </div>
  </div>
</template>


<script>
import { mapGetters, mapActions } from 'vuex';
import * as constants from 'Helpers/constants';

export default {
  name: 'UploadFilesForm',
  props: {
    mediaType: {
      type: String,
      default: constants.ALL,
    },
    multiple: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      constants,
      fileCounter: 0,
      dragOver: false,
    };
  },
  computed: {
    ...mapGetters('upload', [
      'files',
      'uploader',
    ]),
    getMediaTypeAccept() {
      if (this.mediaType === constants.AUDIO) {
        return 'audio/*';
      } else if (this.mediaType === constants.IMAGE) {
        return 'image/*';
      } else if (this.mediaType === constants.VIDEO) {
        return 'video/*';
      }
      return 'video/*, image/*, audio/*';
    },
  },
  methods: {
    ...mapActions('upload', [
      'addFile',
      'resetFiles',
    ]),
    clickOnInput() {
      this.$refs.fileInput.click();
    },
    onDragLeave() {
      this.dragOver = false;
    },
    onDropFile(e) {
      const { files } = e.dataTransfer;
      this.onAddFile(files[0]);
    },
    onDragFile() {
      this.dragOver = true;
    },
    filesChange(e) {
      const { files } = e.target;
      if (!this.multiple) {
        this.resetFiles();
      }
      // this.onAddFile(files[0]);
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

<style
  lang="stylus"
  scoped
>
  @import '../../../../../../sass/front/components/bulma-theme';

  .fa-icon {
    color      : $text-light;
    font-size  : 1.75rem;
    transition : all .3s;
  }

  .up-files-form {
    width: 100%;
    &__dropbox {
      fl-center();
      border-radius  : 8px;
      border         : 1px dashed $border;
      cursor         : pointer;
      flex-direction : column;
      min-height     : 142px;
      position       : relative;
      transition     : all .3s;
      &:hover {
        border-color : $primary;
        & .fa-icon {
          color : $primary;
        }
      }
      &--drag-over {
        border-color : $primary;
        & .fa-icon {
          color : $primary;
        }
      }
    }
    &__input-label {
      cover-all();
      cursor : pointer;
      background-color: rgba($primary, 0.05);
    }
    &__input-file {
      display : none;
    }
    &__title {
      fnt($text, 14px, $weight-normal, left);
    }
    &__sub-title {
      fnt($primary, 14px, $weight-normal, left);
    }
  }
</style>

