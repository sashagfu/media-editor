<template>
  <div class="events">
    <div class="events__row">
      <div class="box">
        <div class="events__divider">
          <div class="events__divider-title">
            {{ trans('circles.requests') }}
          </div>
        </div>
        <request
          v-for="(request, index) in requests"
          v-if="request.id"
          :key="request.id"
          :request="request"
          :circle="circle"
          :index="index"
        />
        <div
          v-if="requests.length === 0"
          class="no-events"
        >
          <div class="no-events__text">
            {{ trans('circles.empty-requests') }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
/* @flow */
import Request from './Request';

export default {
  components: {
    Request,
  },
  props: {
    circle: {
      type: Object,
      default: () => ({}),
    },
    requests: {
      type: Array,
      default: () => [],
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../../sass/front/components/bulma-theme';

  .events {
    &__box {
      padding : 0 13px;
      &:first-child {
        padding-left : 0;
      }
      &:last-child {
        padding-right : 0;
      }
    }
    &__row {
      padding : 13px 32px;
      &:first-child {
        padding-top : 26px;
      }
      &:last-child {
        padding-bottom : 26px;
      }
    }
    &__divider {
      height      : 36px;
      width       : 100%;
      padding     : 0 26px;
      display     : flex;
      align-items : center;
      &:not(:last-child) {
        border-bottom : 1px solid $border;
      }
    }
    &__divider-title {
      fnt($text-light, 9px, $weight-bold, left);
      text-transform : uppercase;
    }
  }

  .no-events {
    height          : 232px;
    display         : flex;
    align-items     : center;
    flex-direction  : column;
    justify-content : center;
    &__text {
      fnt($text-lighter, 14px, $weight-normal, center);
    }
  }

</style>
