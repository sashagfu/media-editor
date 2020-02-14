<template>
  <div class="UploadDialog upload-dialog">
    <el-dialog
      :visible="isDialogOpen"
      :close-on-click-modal="false"
      @update:visible="toggleDialog"
    >

      <div class="me-upload-head-line">
        <div class="me-upload-head-line__left">
          <!-- <font-awesome-icon
                  :icon="['fas', 'cloud-upload-alt']"
                  class="fa-icon"
          /> -->
          <div class="me-upload-head-line__title">
            {{ returnHeaderText }}
          </div>
        </div>
        <div class="me-upload-head-line__right"/>
      </div>
      <div class="me-upload-main">
        <upload-files
          v-bind="$props"
        />
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import * as constants from 'Helpers/constants';
import UploadFiles from './UploadFiles';

export default {
  name: 'UploadDialog',
  components: {
    UploadFiles,
  },
  props: {
    mediaType: {
      type: String,
      default: constants.VIDEO,
    },
  },
  data() {
    return {
      constants,
    };
  },
  computed: {
    ...mapGetters('upload', [
      'isDialogOpen',
    ]),
    returnHeaderText() {
      return `Add ${String(this.mediaType).toLowerCase()}s files`;
    },
  },
  methods: {
    ...mapActions('upload', [
      'openDialog',
      'closeDialog',
    ]),
    toggleDialog(dialogStatus) {
      if (dialogStatus) {
        this.openDialog();
      } else {
        this.closeDialog();
      }
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .fa-icon {
    color      : $text-light;
    font-size  : 1.25rem;
    transition : all .2s;
    &--hoverable {
      &:hover {
        color : $text-lighter;
      }
    }
    &--invert {
      color : $white;
      &:hover {
        color : $white-bis;
      }
    }
    &--pointer {
      cursor : pointer;
    }
    &--disable {
      color : $grey-lighter;
    }
    &__box {
      padding : 0 4px;
    }
  }

  .upload-dialog {
    .el-dialog {
      &__header {
        padding : 0;
      }
      &__headerbtn {
        background-color : $white;
        border-radius    : 50%;
        height           : 22px;
        padding          : 2px;
        position         : absolute;
        right            : -25px;
        top              : -25px;
        width            : 22px;
      }
      &__body {
        padding : 0;
      }
    }
  }

  .me-upload-head-line {
    fl-between();
    border-bottom : 1px solid $border;
    height        : 60px;
    padding       : 0 26px;
    width         : 100%;
    &__left {
      fl-left();
      height : 100%;
    }
    &__right {
      fl-right();
      height       : 100%;
      margin-right : -22px;
    }
    &__title {
      fnt($text, 14px, $weight-semibold, left);
    }
    &__btn {
      fl-center();
      border-left : 1px solid $border;
      cursor      : pointer;
      height      : 100%;
      position    : relative;
      transition  : all .3s;
      width       : 68px;
    }
    &__label {
      fl-left();
    }
    &__back-btn {
      fl-left();
      cursor : pointer;
    }
    &__back-btn-title {
      padding-left : 8px;
    }
  }
</style>

<style lang="stylus">
  @import '../../../../../sass/front/components/bulma-theme';

  .modal-content--me-upload {
    background-color : $white;
    border           : 1px solid $border;
    border-radius    : 3px;
    position         : relative;
    width            : 470px;

    & .modal-close {
      position : absolute;
      right    : -48px;
      top      : -48px;
    }
  }

  .upload-dialog {
    .el-dialog {
      &__header {
        padding : 0;
      }
      &__headerbtn {
        background-color : $white;
        border-radius    : 50%;
        height           : 22px;
        padding          : 2px;
        position         : absolute;
        right            : -25px;
        top              : -25px;
        width            : 22px;
      }
      &__body {
        padding : 0;
      }
    }
  }
</style>
