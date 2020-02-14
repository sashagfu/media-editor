<template>
  <div class="HomeFriendBar hf-bar box">
    <div class="hf-bar__left">
      <div
        :style="{'background-image': `url(${friend.avatar})`}"
        class="hf-bar__avatar"/>
      <div class="hf-bar__title-box">
        <div class="hf-bar__title">
          {{ friend.fullName }}
        </div>
        <div class="hf-bar__sub-title">
          {{ friend.email }}
        </div>
      </div>
    </div>
    <div class="hf-bar__right">
      <el-button
        type="primary"
        size="small"
        plain
      >
        {{ trans('home_projects.show_all') }}
      </el-button>
    </div>

  </div>
</template>

<script>
export default {
  name: 'HomeFriendPlate',
  props: {
    friend: {
      type: Object,
      default: () => ({}),
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .hf-bar {
    fl-between();
    height: 56px;
    margin-bottom: 24px;
    padding: 0 24px;
    &__left {
      fl-left();
    }
    &__right {
      fl-right();
    }
    &__avatar {
      background: center center/cover no-repeat $grey-lighter;
      border-radius: 50%;
      height: 40px;
      width: 40px;
    }
    &__title-box {
      padding-left: 12px;
    }
    &__title {
      fnt($text, 14px, $weight-semibold, left);
      text-transform: capitalize;
    }
    &__sub-title {
      fnt($text-light, 12px, $weight-normal, left);
    }
  }

</style>
