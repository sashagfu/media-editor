<template>
  <div class="MainSharedLinkPreview shared-link">
    <div class="shared-link__box box">
      <div class="shared-link__photo"/>
      <div class="shared-link__title">
        The Secrets of the Auroris Movie Effects
      </div>
      <div class="shared-link__text">
        Lorem ipsum dolor sit amet, consectetur adipisicing elit,
        sed do eiusmod tempo incididunt
        ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis...
      </div>
      <div class="shared-link__source">
        moviemag.com | by dan stevens
      </div>
    </div>
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

  .shared-link {
    padding : 16px 26px;
    &__photo {
      background    : url('http://lorempixel.com/750/400/business') center center no-repeat $primary;
      border-bottom : 1px solid $border;
      border-radius : 3px 3px 0 0;
      height        : 280px;
      width         : 100%;
    }
    &__title {
      color      : $text;
      font       : $weight-normal 24px;
      padding    : 24px 20px 12px 20px;
      text-align : left;
    }
    &__text {
      color      : $text-light;
      font       : $weight-normal 13px;
      padding    : 16px 20px 16px 20px;
      text-align : left;
    }
    &__source {
      color          : $text-lighter;
      cursor         : pointer;
      font           : $weight-normal 10px;
      padding        : 0 20px 16px 20px;
      text-align     : left;
      text-transform : uppercase;
      &:hover {
        color       : $text;
        font-weight : $weight-normal+100;
      }
      &:active {
        color : $primary;
      }
    }
  }
</style>
