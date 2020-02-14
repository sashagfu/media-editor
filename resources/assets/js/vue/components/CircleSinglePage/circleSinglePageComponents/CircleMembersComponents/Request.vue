<template>
  <div class="event level is-marginless">
    <div class="level-left">
      <div class="ava-box level-item">
        <div
          :style="{'background-image':`url(${request.avatar})`}"
          class="ava-box__ava"
        />
      </div>
      <div class="title-box level-item">
        <div class="title-box__title">
          {{ request.display_name }}
        </div>
        <div class="title-box__time">
          @{{ request.username }}
        </div>
      </div>
      <div class="description-box level-item">
        {{ request.quote }}
      </div>
    </div>
    <div class="level-right">
      <div class="btn-box level-item">
        <button
          class="btn btn--approve"
          @click="approveRequest(circle, request.id)"
        >
          {{ trans('circles.approve') }}
        </button>
      </div>
      <div
        v-if="isLoading"
        class="btn-box level-item"
      >
        <i class="fa fa-spinner fa-spin fa-3x fa-fw has-text-centered"/>
      </div>
      <div class="btn-box level-item">
        <button
          class="btn btn--cancel"
          @click="cancelRequest(circle, request.id)"
        >
          {{ trans('circles.cancel') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
/* @flow */
export default {
  props: {
    circle: {
      type: Object,
      default: () => ({}),
    },
    request: {
      type: Object,
      default: () => ({}),
    },
    index: {
      type: Number,
      default: 0,
    },
  },
  data() {
    return {
      isLoading: false,
    };
  },
  methods: {
    approveRequest(circle, requesterId) {
      this.isLoading = !this.isLoading;
      this.$http.patch('/api/circle/approve_request', {
        user_id: requesterId,
        slug: circle.slug,
      })
        .then(() => {
          this.$parent.requests.splice(this.index, 1);
          this.$parent.$forceUpdate();
          this.isLoading = !this.isLoading;
        });
    },
    cancelRequest(circle, requesterId) {
      this.isLoading = !this.isLoading;
      this.$http.patch('/api/circle/cancel_request', {
        user_id: requesterId,
        slug: circle.slug,
      })
        .then(() => {
          this.$parent.requests.splice(this.index, 1);
          this.$parent.$forceUpdate();
          this.isLoading = !this.isLoading;
        });
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../../sass/front/components/bulma-theme';

    .event {
        padding: 32px 26px;
        border-bottom: 1px solid $border;
    }

    .level-left {
        width: 75%;
    }

    .ava-box {
        padding: 0 12px;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        &__ava {
            height: 36px;
            width: 36px;
            border-radius: 50%;
            background: center center/cover no-repeat $grey-light;
        }
    }

    .title-box {
        align-items: flex-start;
        display: flex;
        flex-direction: column;
        justify-content: center;
        width: 240px;
        &__title {
            fnt($text, 14px, $weight-bold, left);
            text-transform: capitalize;
        }
        &__time {
            fnt($text-light, 11px, $weight-normal, left);
        }
    }

    .description-box {
        fnt($text-light, 12px, $weight-normal, left);
        align-items: left;
        display: flex;
        flex: 1 1 auto;
        height: 100%;
        justify-content: flex-start;
        width: 100%;
    }

    .btn {
        fnt($text-invert, 11px, $weight-semibold, center);
        align-items: flex-start;
        border-radius: 3px;
        cursor: pointer;
        display: flex;
        height: 30px;
        justify-content: center;
        margin-right: 15px;
        outline: none;
        transition: all .3s;
        width: 100%;
        &--approve {
            background-color: $turquoise;
            border: 1px solid $turquoise;
        }
        &--cancel {
            background-color: $red;
            border: 1px solid $red;
        }

    }
</style>
