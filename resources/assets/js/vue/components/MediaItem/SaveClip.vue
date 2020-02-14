<template>
  <div class="SaveClip save-clip">
    <el-dropdown
      trigger="hover"
      size="small"
      @command="saveContent"
    >
      <div class="save-clip__dropdown-button">
        <font-awesome-icon
          :icon="['fas', 'paperclip']"
          class="fa-icon fa-icon--invert"
        />
      </div>
      <el-dropdown-menu
        slot="dropdown"
        class="save-clip__menu"
      >
        <el-dropdown-item
          v-for="(asset, index) in project.assets"
          :key="index"
          :command="asset.type"
          class="save-clip__item"
        >
          Save {{ ucFirst(asset.type) }}
        </el-dropdown-item>
      </el-dropdown-menu>
    </el-dropdown>
  </div>
</template>

<script>
import CREATE_CLIP from 'Gql/clips/mutations/createClip.graphql';

export default {
  name: 'SaveClip',
  props: {
    project: {
      type: Object,
      required: true,
    },
  },
  methods: {
    ucFirst(str) {
      if (!str) return str;
      return str[0].toUpperCase() + str.slice(1).toLowerCase();
    },
    saveContent(command) {
      this.$apollo.mutate({
        mutation: CREATE_CLIP,
        variables: {
          projectId: this.project.id,
          assetType: command,
        },
      })
        .then(() => {
          this.$notify({
            title: this.trans('common.saved'),
            message: this.trans('clip.saved'),
            type: 'success',
            iconClass: 'el-icon-circle-check',
          });
        })
        .catch(() => {
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
  @import '../../../../sass/front/components/bulma-theme';

  .fa-icon {
    fnt($text-light, 1rem, $weight-normal, left);
    transition: all .3s;
    cursor: pointer;
    &:hover {
      color: $text;
    }
    &--invert {
      color: $text-invert;
      &:hover {
        color: $grey-lighter;
      }
    }
  }
  .save-clip {
    &__dropdown-button {
      fl-center();
      cursor: pointer;
      height: 26px;
      width: 26px;
    }

    &__item {
      fnt($text-light, 12px, $weight-normal, left);
      transition: color .3s;
      line-height: 26px;
      padding: 0 12px;
      &:hover {
        color: $text;
        background-color: $grey-lighter;
      }
    }
  }
</style>
