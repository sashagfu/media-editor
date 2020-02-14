<template>
  <div class="FollowingButton">
    <div
      class="following-btn following-btn--followed"
      @click="handleClick"
    >
      <div class="following-btn__count">
        {{ user.following.length }}
      </div>
      <div class="following-btn__text">
        {{ trans('profile_page.following') }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'FollowingButton',
  props: {
    user: {
      type: Object,
      default: () => ({}),
    },
  },
  methods: {
    handleClick() {
      window.location = '/network#following';
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
    fnt($blue, 11px, $weight-light, left)
  }

  .following-btn {
    fl-center()
    cursor pointer
    background-color $white
    border 1px solid $grey-light
    border-radius 9px
    height 18px
    transition all .3s
    width 108px
    margin-bottom 8px
    &__text {
      fnt($grey-light, 12px, $weight-normal, center)
      padding-left 8px
    }
    &__count {
      fnt($grey-light, 12px, $weight-normal, center)
    }
  }

</style>
