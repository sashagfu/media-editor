<template>
  <div class="Upload upload">
    <a
      class="fa-box fa-icon--pointer"
    >
      <font-awesome-icon
        :icon="['fas', 'upload']"
        class="fa-icon"
      />
      <div
        :style="{
          width: `${progress}%`,
          'background-color': (processing)
            ? '#ffd52e'
            : '#00b7ff',
        }"
        class="upload__status"
      />
    </a>
    <div class="upload__dropdown-menu">
      <UploadDropdownMenu/>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import UploadDropdownMenu from './UploadDropdownMenu';

export default {
  name: 'TopBarUpload',
  components: { UploadDropdownMenu },
  computed: {
    ...mapGetters('upload', [
      'files',
      'uploadedFiles',
      'processing',
      'uploading',
    ]),
    progress() {
      return (this.uploadedFiles.length / this.files.length) * 100;
    },
  },
  watch: {
    uploading() {
      this.setCloseWindowConfirm();
    },
    uploadedFiles() {
      if (this.files.length && this.uploadedFiles.length === this.files.length) {
        this.processUploadedFiles(this.uploadedFiles);
      }
    },
  },
  methods: {
    ...mapActions('upload', [
      'processUploadedFiles',
    ]),

    /**
     * Set if show confirm on close browser tab or window
     */
    setCloseWindowConfirm() {
      window.onbeforeunload = this.uploading ? this.showOnCloseConfirm : null;
    },
    /**
     * Function to set confirm on close browser tab or window
     * ~*~*~ It's magic here ~*~*~
     * (I don't know how it works and is it works at all)
     *
     * @param e
     * @returns {string}
     * @private
     */
    showOnCloseConfirm(e) {
      const localEv = e || window.event;
      if (localEv) {
        // localEv.returnValue = 'Changes you made not be saved';
        Object.assign(localEv, {
          returnValue: this.trans('projects.uploading_not_finished'),
        });
      }
      // For Safari
      return this.trans('projects.uploading_not_finished');
    },
  },
};
</script>

<style lang="stylus"
       scoped>
  @import '../../../../../sass/front/components/bulma-theme';

  .fa-icon {
    color: $text-light;
    font-size: 1.5rem;
    transition: all .3s;

    &--invert {
      color: $white;

      &--pointer {
        cursor: pointer;
      }

      &--disabled {
        color: $grey-lighter;
        cursor: auto;

        &__box {
          padding: 0 4px 0;
        }
      }
    }
  }

  .upload {
    fl-center();
    height: 68px;
    width: 62px;
    position: relative;

    &:hover &__dropdown-menu {
      display flex
    }

    &__status {
      background-color $yellow
      height 3px
      width 100%
      margin-top: 1px;
    }

    &__dropdown-menu {
      align-items flex-start
      box-shadow 0px 0px 20px 0px rgba($grey-darker, .1)
      display none
      flex-direction column
      left -142px
      max-height 400px
      position absolute
      top 56px
      z-index: 1
    }
  }
</style>
