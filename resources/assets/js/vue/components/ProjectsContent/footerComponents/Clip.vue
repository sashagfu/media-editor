<template>
  <div
    ref="saveContent"
    class="Clip clip"
    @click.stop="toggleSaveMenu()"
    @mouseover="getTopPosition"
  >
    <div class="clip__icon">
      <font-awesome-icon
        :icon="['fas', 'paperclip']"
        :class="{'fa-icon--saved': savedContent}"
        class="fa-icon fa-icon--pointer"
      />
    </div>
    <div
      :class="[
        'clip__diamond',
        `clip__diamond--${positions}`,
        {'clip__diamond--show': showSaveMenu}
      ]"
    />
    <div
      :class="[
        'box',
        'clip__dropdown',
        `clip__dropdown--${positions}`,
        {'clip__dropdown--show': showSaveMenu}
      ]"
    >
      <el-button
        v-for="(asset, index) in project.assets"
        :key="index"
        :type="asset.type !== constants.FULL ? 'success' : 'info'"
        size="mini"
        plain
        class="clip__btn"
        @click.prevent.stop="saveContent(asset.type)"
      >
        <font-awesome-icon
          v-if="savingContent === asset.type"
          :icon="['fas', 'spinner']"
          :class="[
            'fa-icon',
            {'fa-icon--saving': asset.type !== constants.FULL},
            {'fa-icon--info': asset.type === constants.FULL}
          ]"
          spin
        />
        <template v-else>
          Save {{ ucFirst(asset.type) }}
        </template>
      </el-button>
    </div>
  </div>
</template>

<script>
import CREATE_CLIP from 'Gql/clips/mutations/createClip.graphql';
import * as constants from 'Helpers/constants';

export default {
  name: 'Clip',
  props: {
    project: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      constants,
      showSaveMenu: false,
      positions: '',
      savingContent: null,
      savedContent: false,
    };
  },
  mounted() {
    window.addEventListener('click', this.closeSaveMenu, false);
  },
  destroyed() {
    window.removeEventListener('click', this.closeSaveMenu, false);
  },
  methods: {
    ucFirst(str) {
      if (!str) return str;
      return str[0].toUpperCase() + str.slice(1)
        .toLowerCase();
    },
    getTopPosition() {
      const { top } = this.$refs.saveContent.getBoundingClientRect();
      if (top < 200) {
        this.positions = 'bottom';
      } else {
        this.positions = 'top';
      }
    },
    toggleSaveMenu() {
      this.showSaveMenu = !this.showSaveMenu;
    },
    closeSaveMenu() {
      this.showSaveMenu = false;
    },
    saveContent(type) {
      this.savingContent = type;
      this.$apollo.mutate({
        mutation: CREATE_CLIP,
        variables: {
          projectId: this.project.id,
          assetType: type,
        },
      })
        .then(() => {
          this.savingContent = null;
          this.showSaveMenu = false;
          this.savedContent = true;
          this.$notify({
            title: this.trans('common.saved'),
            message: this.trans('clip.saved'),
            type: 'success',
            iconClass: 'el-icon-circle-check',
          });
        })
        .catch(() => {
          this.savingContent = null;
          this.$notify.error({
            title: this.trans('common.error'),
            message: this.trans('clip.cant_saved'),
          });
        });
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
    color: $text-light;
    transition: all .3s;
    font-size: 1rem;

    &:hover {
      color: $text-lighter;
    }

    &--saved {
      color: $primary;
    }

    &--info {
      color: $info;
      margin: -2px 0;
    }

    &--saving {
      color: $primary;
      margin: -2px 0;
    }
  }

  .clip {
    position: relative;
    padding: 0 4px;
    height: 100%;

    &__icon {
      cursor: pointer;
      display: flex;
      align-items: center;
      height: 100%;
    }

    &__diamond {
      display: none;
      background-color: $background-light;
      height: 8px;
      position: absolute;
      right: 7px;
      transform: rotate(225deg);
      width: 8px;
      z-index: 6;

      &--top {
        bottom: 20px;
        border-top: 1px solid $border;
        border-left: 1px solid $border;
      }

      &--bottom {
        top: 20px;
        border-bottom: 1px solid $border;
        border-right: 1px solid $border;
      }

      &--show {
        display: flex;
      }
    }

    &__dropdown {
      align-items: center;
      display: none;
      flex-direction: column;
      padding: 12px 16px;
      position: absolute;
      right: -8px;
      z-index: 5;
      +tablet() {
        left: -8px;
      }
      +desktop() {
        right: -8px;
      }

      &--top {
        bottom: 24px;
      }

      &--bottom {
        top: 24px;
      }

      &--show {
        display: flex;
      }
    }

    &:hover > &__dropdown, &:hover > &__diamond {
      display: flex;
    }

    &__btn {
      margin: 0;
      min-width: 92px;

      &:not(:first-child) {
        margin-bottom: 2px;
      }

      &:not(:last-child) {
        margin-top: 2px;
      }

      &:hover {
        & .fa-icon {
          color: $white;
        }
      }
    }
  }

</style>

<style lang="stylus">
  @import '../../../../../sass/front/components/bulma-theme';

  .clip {
    .el-checkbox__input.is-focus .el-checkbox__inner {
      border-color: $primary;
    }

    .el-checkbox__inner:hover {
      border-color: $primary;
    }

    .el-checkbox__input.is-checked .el-checkbox__inner,
    .el-checkbox__input.is-indeterminate .el-checkbox__inner {
      background-color: $primary;
      border-color: $primary;
    }

    .el-checkbox__input.is-checked + .el-checkbox__label {
      color: $primary;
    }

    .el-checkbox + .el-checkbox {
      margin: 0;
    }
  }

</style>
