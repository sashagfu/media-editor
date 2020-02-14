<template>
  <div class="UploadDropdownMenu ud-menu">
    <div class="menu-diamond"/>
    <div class="ud-menu__head-line">
      <div class="ud-menu__head-title">
        Upload Files
      </div>
    </div>
    <div
      v-if="files.length"
      class="ud-menu__upload-box"
    >
      <UploadItem
        v-for="(file, index) in files"
        :key="index"
        :file="file"
        class="ud-menu__upload-item"
      />
    </div>
    <div
      v-else
      class="ud-menu__loading-box"
    >
      <font-awesome-icon
        :icon="['fas', 'spinner']"
        spin
        class="fa-icon"
      />
      <span>Processing...</span>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import UploadItem from '../../Common/Upload/UploadItem';

export default {
  name: 'UploadDropdownMenu',
  components: {
    UploadItem,
  },
  computed: {
    ...mapGetters('upload', [
      'files',
      'uploader',
    ]),
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .fa-icon {
    color $yellow
    font-size 1.5rem

    &__box {
      fl-center()
      size(24, 24)
      flex 0 0 auto
    }
  }

  .menu-diamond {
    deg45();
    background-color: $white;
    border-left: 1px solid $border;
    border-top: 1px solid $border;
    height: 8px;
    left: 169px;
    position: absolute;
    top: -5px;
    width: 8px;
    z-index: 1;
    +desktop() {
      left: 254px;
    }
  }

  .ud-menu {
    background-color: $white;
    border-radius: 3px;
    border: 1px solid $border;
    position: relative;
    width: 304px;

    &__head-line {
      fl-between();
      border-bottom: 1px solid $border;
      display: flex;
      height: 36px;
      justify-content: space-between;
      padding: 0 24px;
    }

    &__head-title {
      fnt($text, 9px, $weight-normal, left);
      text-transform: uppercase;
      margin-bottom: -1px;
    }

    &__upload-box {
      min-height: 64px;
      padding: 0 24px;
      margin: 10px 0 16px 0;
    }

    &__upload-item {
      height: 33px;
    }

    &__loading-box {
      min-height: 64px;
      color: $text-light;
      display: flex;
      justify-content center;
      align-items: center;

      .fa-icon {
        margin-right: 5px;
      }
    }
  }

</style>
