<template>
  <div class="UploadFile">
    <div class="me-upload-progress">
      <div class="me-upload-progress__title-box">
        <div
          class="me-upload-progress__title"
        >
          {{ decodeURI(file.name) }}
        </div>
        <div class="me-upload-progress__sub-title">
          <div class="me-upload-progress__actions">
            <a @click.prevent="onUploadAction">
              <!--icon of element-ui-->
              <i class="el-icon-close"/>
            </a>
          </div>
          <div class="me-upload-progress__status">
            <!--icon of element-ui-->
            <i
              v-if="!(file.uploaded || file.uploading)"
              class="el-icon-upload2"
            />
            <span v-if="file.uploading && !file.uploaded">
              {{ statusText }}
            </span>
            <!--icon of element-ui-->
            <i
              v-if="file.uploaded"
              class="el-icon-circle-check"
              @click.prevent="removeFile(file.id)"
            />
          </div>
        </div>
      </div>
    </div>
    <div
      class="me-upload-progress__progress"
      role="progressbar"
      tabIndex="0"
    >
      <div
        :class="{
          'me-upload-progress__progress-meter--warning': processing && !done,
          'me-upload-progress__progress-meter--success': done
        }"
        :style="{width: `${progress}%`}"
        class="me-upload-progress__progress-meter"
      />
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex';

export default {
  name: 'UploadItem',
  props: {
    file: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      progress: 0,
      processing: false,
    };
  },
  computed: {
    statusText() {
      if (this.done) {
        return 'Done';
      } else if (this.processing) {
        return 'Processing';
      }
      return `${this.progress}%`;
    },
    done() {
      return this.file.uploaded;
    },
  },
  mounted() {
    this.setHooks();
  },
  methods: {
    ...mapActions('upload', [
      'processUploadedFile',
      'addToUploaded',
      'removeFile',
      'cancelUpload',
    ]),
    onProgress(progress) {
      this.progress = Math.round(progress * 100);
      this.file.progressPercentages = this.progress;
    },
    onUploadComplete(xhr) {
      this.processing = true;
      this.addToUploaded({
        file: this.file,
        xhr,
      });
    },
    onUploadAction() {
      const { id, uploading } = this.file;
      if (uploading) {
        this.cancelUpload({ id });
      }
      this.removeFile({ id });
    },
    setHooks() {
      this.file.progress = this.onProgress;
      this.file.complete = this.onUploadComplete;
    },
  },
};
</script>

<style lang="stylus" scoped>
  @import '../../../../../../sass/front/components/bulma-theme';

  .fa-icon {
    color: $text-light;
    font-size: 1rem;
    transition: all .2s;
    &--upload {
      color: $green;
      font-size: 2rem;
      &:hover {
        opacity: .8;
      }
    }
    &--pointer {
      cursor: pointer;
    }
    &__box {
      padding: 0 4px 0;
    }
    & + & {
      margin-left: 8px;
    }
  }
  .me-upload-progress {
    padding: 6px 0;

    &__title-box {
      align-items: center;
      display: flex;
      justify-content: space-between;
      padding: 0 2px 2px 2px;
    }
    &__title {
      fnt($text, 12px, $weight-normal, left);
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      width: 80%;
    }
    &__sub-title {
      fnt($text-light, 12px, $weight-normal, right);
    }
    &__progress {
      background-color: $grey-light;
      border-radius: 3px;
      height: 6px;
      outline: none;
      position: relative;
      width: 100%;
    }

    &__progress-meter {
      background-color: $info;
      border-radius: 3px;
      height: 6px;
      left: 0;
      outline: none;
      position: absolute;
      &--success {
        background-color: $green;
      }
      &--warning {
        background-color: $warning;
      }
    }

    &__actions {
      display: none;
    }

    &:hover &__actions {
      display: block;
    }
    &:hover &__status {
      display: none;
    }
  }
</style>
