<template>
  <div class="MainPhoto post-main-photo">
    <div class="post-main-photo__photo box"/>
  </div>
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
  @import '../../../../../sass/front/components/bulma-theme';

  .post-main-photo {
    padding : 0 26px 26px 26px;
    &__photo {
      background    : url('http://lorempixel.com/700/58') center center no-repeat $grey-light;
      border-radius : 3px;
      height        : 580px;
      width         : 100%;
    }
  }
</style>
