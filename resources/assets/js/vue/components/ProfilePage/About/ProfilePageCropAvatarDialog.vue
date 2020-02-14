<template>
  <div class="ProfilePageCropAvatarDialog crop-avatar">
    <el-dialog
      :visible="isDialogOpen"
      :close-on-click-modal="false"
      top="10vh"
      @update:visible="handleClose"
    >
      <div class="crop-avatar__body">
        <croppa
          ref="profilePhoto"
          v-model="myCroppa"
          :accept="'image/*'"
          :width="420"
          :height="420"
          :placeholder-font-size="16"
          :show-remove-button="false"
          :show-loading="false"
          :prevent-white-space="true"
          :class="['crop-avatar__box', {'crop-avatar__box--active': !hasImage}]"
          remove-button-color="black"
          canvas-color="transparent"
          placeholder="Click to choose an image"
          placeholder-color="#000"
        />
      </div>
      <span
        slot="footer"
        class="dialog-footer"
      >
        <span class="dialog-footer__left"/>
        <span class="dialog-footer__center">
          <el-button
            size="small"
            @click="myCroppa.flipX()"
          >
            <font-awesome-icon
              :icon="['fas', 'arrows-alt-h']"
              class="fa-icon"
            />
          </el-button>
          <el-button
            size="small"
            @click="myCroppa.flipY()"
          >
            <font-awesome-icon
              :icon="['fas', 'arrows-alt-v']"
              class="fa-icon"
            />
          </el-button>
          <el-button
            size="small"
            @click="myCroppa.rotate(-1)"
          >
            <font-awesome-icon
              :icon="['fas', 'undo']"
              class="fa-icon"
            />
          </el-button>
          <el-button
            size="small"
            @click="myCroppa.rotate()"
          >
            <font-awesome-icon
              :icon="['fas', 'redo']"
              class="fa-icon"
            />
          </el-button>
          <el-button
            size="small"
            @click="myCroppa.remove()"
          >
            <font-awesome-icon
              :icon="['far', 'trash-alt']"
              class="fa-icon"
            />
          </el-button>
        </span>
        <span class="dialog-footer__right">
          <el-button
            size="small"
            @click="handleClose"
          >
            {{ trans('users.cancel') }}
          </el-button>
          <el-button
            :disabled="!hasImage"
            size="small"
            type="primary"
            @click="avatarUpload"
          >
            <font-awesome-icon
              v-if="uploading"
              :icon="['fas', 'spinner']"
              spin
              class="fa-icon fa-icon__spinner"
            />
            <span class="dialog-footer__btn-title">
              {{ trans('users.confirm') }}
            </span>
          </el-button>
        </span>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { isEmpty } from 'lodash';
import Croppa from 'vue-croppa';
import 'vue-croppa/dist/vue-croppa.css';

export default {
  name: 'ProfilePageCropAvatarDialog',
  components: {
    croppa: Croppa.component,
  },
  props: {
    isDialogOpen: {
      type: Boolean,
      default: false,
    },
    uploading: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      myCroppa: {},
    };
  },
  computed: {
    hasImage() {
      if (!isEmpty(this.myCroppa)) {
        return this.myCroppa.hasImage();
      }
      return false;
    },
  },
  methods: {
    handleClose() {
      this.$emit('close-dialog');
    },
    avatarUpload() {
      const img = this.myCroppa.generateDataUrl('image/jpg', 0.5);
      if (img) {
        this.$emit('avatar-upload', img);
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
    fnt($text-light, 14px, $weight-light, left);
    transition : all .3s;
    &__spinner {
      fnt($text-invert, 16px, $weight-light, left);
    }
    &__box {
      margin: -2px 0;
    }
  }

  .crop-avatar {
    &__body {
      fl-center();
      width: 100%;
    }
    &__box {
      border-radius: 50%;
      border: 2px dashed transparent;
      height: 420px;
      overflow: hidden;
      width: 420px;
      &--active {
        border-color: $primary;
      }
    }
  }

  .dialog-footer {
    fl-between();
    &__left {
      width: 200px;
    }
    &__center {
      fl-center();
    }
    &__right {
      fl-right();
      width: 200px;
    }
    &__btn-title {
      padding-left: 4px;
    }
  }
  .croppa-container {
    background-color: rgba($primary, 0.02);
    &:hover {
      opacity: 1;
      background-color: rgba($primary, 0.1);
    }
  }

</style>
