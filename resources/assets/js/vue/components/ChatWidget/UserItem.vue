<template>
  <div class="UserBox user-box">
    <Avatar
      v-if="user.avatar.includes('/profile/default/avatar')"
      :size="36"
      :username="user.displayName || user.display_name"
      custom-class="user-box__avatar"
    />
    <div
      v-else
      :style="{'background-image': `url(${user.avatar})`}"
      class="user-box__avatar"
    />
    <div class="user-box__title-box">
      <div class="user-box__title">
        {{ user.displayName ? user.displayName : user.display_name }}
      </div>
      <div class="user-box__sub-title">
        @{{ user.username }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'UserItem',
  props: {
    user: {
      type: Object,
      default: () => {},
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .user-box
    fl-left()
    padding 5px 0

    &__avatar
      fl-center()
      circle(36)
      width 40px
      height 40px
      background center center/cover no-repeat $primary
    &--multi
      size(18, 18)
      margin 0 2px 2px 0
    &--count
      fnt($text-invert, 8px, $weight-normal, center)
    &__title-box
      display flex
      flex-direction column
      padding-left 8px
    &__title
      fnt($text, 14px, $weight-semibold, left)
      line-height 1
    &__sub-title
      fnt($text-light, 10px, $weight-light, left)
</style>
