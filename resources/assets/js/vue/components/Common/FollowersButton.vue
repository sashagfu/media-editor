<template>
  <div class="FollowersButton">
    <div
      class="followers-btn followers-btn--followed"
      @click="handleClick"
    >
      <div class="followers-btn__count">
        {{ user.totalFollowers ? user.totalFollowers : '0' }}
      </div>
      <div class="followers-btn__text">
        {{ trans('profile_page.followers') }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'FollowersButton',
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

  .followers-btn {
    fl-center()
    cursor pointer
    background-color $white
    border 1px solid $grey-light
    border-radius 9px
    height 18px
    transition all .3s
    width 92px
    margin-bottom 8px
    margin-left: 8px;
    &__text {
      fnt($grey-light, 12px, $weight-normal, center)
      padding-left 8px
    }
    &__count {
      fnt($grey-light, 12px, $weight-normal, center)
    }
  }

</style>
