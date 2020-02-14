<template>
  <router-link
    :to="{name: 'FeedPage'}"
    class="TopBarLogo top-bar-logo">
    <div class="top-bar-logo__logo"/>
  </router-link>
</template>

<script>
/* @flow */
export default {
  components: {},
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .top-bar-logo {
    fl-center();
    background-color $white
    height 72px
    padding 0 26px
    width 278px
    +tablet() {
      padding 8px 26px
      width 100%
    }
    +desktop() {
      width 100%
    }
    &__logo {
      background center center/contain no-repeat
      background-image url('../../../../img/logo/actionlime-logo--full.png')
      height 100%
      width 100%
    }
  }

</style>
