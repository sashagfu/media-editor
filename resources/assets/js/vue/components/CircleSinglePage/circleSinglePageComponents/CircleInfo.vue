<template>
  <div class="profile-info">
    <HeadLine :header="header"/>
    <div class="main-box">
      <div class="main-box__item">
        <div class="main-box__title">
          {{ trans('circles.title') }}
        </div>
        <div class="main-box__text">
          {{ circle.title }}
        </div>
      </div>
      <div class="main-box__item">
        <div class="main-box__title">
          {{ trans('circles.description') }}
        </div>
        <div class="main-box__text">
          {{ circle.description }}
        </div>
      </div>
      <div class="main-box__item">
        <div class="main-box__title">
          {{ `${trans('circles.type')}: ${circle.type}` }}
        </div>
      </div>
      <div class="main-box__item">
        <div class="main-box__title">
          {{ `${trans('circles.circle_total_members')}: ${circle.totalMembers}` }}
        </div>
      </div>
      <div class="main-box__item">
        <div class="main-box__title">
          {{ `${trans('circles.circle_total_posts')}: ${circle.totalPosts}` }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
/* @flow */
import HeadLine from '../../HeadLine/HeadLine';

export default {
  components: {
    HeadLine,
  },
  props: {
    circle: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      header: this.trans('circles.circle_info'),
    };
  },
};
</script>

<style
  lang="stylus"
  scoped
>
    @import '../../../../../sass/front/components/bulma-theme';

    .header-box {
        border-bottom: 1px solid $border;
        height: 60px;
        padding: 0 26px;
        width: 100%;
        &__title {
            color: $text;
            font-size: 14px;
            font-weight: $weight-semibold;
            text-align: left;
        }
    }

    .main-box {
        padding: 26px;
        &__item {
            padding-bottom: 32px;
            &:last-child {
                padding-bottom: 0;
            }
        }
        &__title {
            color: $text;
            font-size: 13px;
            font-weight: $weight-semibold;
            text-align: left;
        }
        &__text {
            color: $text-light;
            font-size: 13px;
            font-weight: $weight-normal;
            text-align: left;
        }
    }
</style>
