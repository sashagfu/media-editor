<template>
  <div class="UploadDialog upload-dialog">
    <el-dialog
      :visible="isDialogOpen"
      :close-on-click-modal="false"
      @update:visible="toggleDialog"
      @close="hideDialog"
    >

      <div
        slot="title"
        class="upload-dialog__header"
      >
        <div class="del-media-modal__header-text">
          <!--{{ trans('media_editor.') }}-->
          Upload your media files
        </div>
      </div>
      <div class="me-upload-main">
        <upload-files/>
      </div>
      <div
        class="upload-dialog__hide-btn"
        @click="hideDialog">
        <el-button
          type="text"
          icon="el-icon-minus"
        />
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import UploadFiles from './UploadFiles';

export default {
  name: 'UploadDialog',
  components: {
    UploadFiles,
  },
  data() {
    return {
      currentComponent: 'UploadMenu',
    };
  },
  computed: {
    ...mapGetters('upload', [
      'isDialogOpen',
      'files',
    ]),
    returnHeaderText() {
      return 'Add media files';
    },
  },
  methods: {
    ...mapActions('upload', [
      'openDialog',
      'closeDialog',
      'setProcessing',
    ]),
    toggleDialog(dialogStatus) {
      if (dialogStatus) {
        this.openDialog();
      } else {
        this.closeDialog();
      }
    },
    hideDialog() {
      const file = this.files.find(f => f.uploading);
      if (!file || file === -1) {
        this.closeDialog();
        return;
      }
      this.closeDialog();
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
    color: $text-light;
    font-size: 1.25rem;
    transition: all .2s;

    &--hoverable {
      &:hover {
        color: $text-lighter;
      }
    }

    &--invert {
      color: $white;

      &:hover {
        color: $white-bis;
      }
    }

    &--pointer {
      cursor: pointer;
    }

    &--disable {
      color: $grey-lighter;
    }

    &__box {
      padding: 0 4px;
    }
  }

  .upload-dialog {
    &__header {
      fl-left();
    }

    &__header-text {
      fnt($text, 18px, $weight-normal, left);
    }

    &__hide-btn {
      position absolute
      top 13px
      right 43px
    }
  }
</style>

