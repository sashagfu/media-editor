<template>
  <div class="HomeCircleBar hc-bar box">
    <div class="hc-bar__left">
      <div class="hc-bar__title">
        {{ circle.title }}
      </div>
      <div class="hc-bar__ava-box ava-box">
        <el-tooltip
          v-for="(member, index) in circle.members"
          v-if="index < 6"
          :key="index"
          :content="member.displayName"
          placement="top"
        >
          <div
            :style="{'background-image': `url(${member.avatar})`}"
            class="ava-box__avatar"/>
        </el-tooltip>
      </div>
      <div class="hc-bar__members-box members-box">
        <span
          v-for="(member, index) in circle.members"
          v-if="index < 2"
          :key="index"
          class="members-box__title"
        >
          {{ member.displayName }},
        </span>
        <span class="members-box__text">
          &nbsp;and {{ circle.members.length - 3 }} members
        </span>
      </div>
    </div>
    <div class="hc-bar__right">
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
  name: 'HomeCircleBar',
  props: {
    circle: {
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

  .hc-bar {
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
    &__title {
      fnt($text, 14px, $weight-semibold, left);
      text-transform: capitalize;
    }
    &__ava-box {
      padding-left: 24px;
    }
    &__members-box {
      padding-left: 12px;
    }
  }
  .ava-box {
    fl-left();

    &__avatar {
      background: center center/cover no-repeat $grey-lighter;
      border-radius: 50%;
      border: 2px solid $white;
      height: 32px;
      width: 32px;
      &:not(:first-child) {
        margin-left: -12px;
      }
    }
  }
  .members-box {
    &__title {
      fnt($text, 12px, $weight-semibold, left);
    }
    &__text {
      fnt($text-light, 12px, $weight-semibold, left);
    }
  }

</style>
