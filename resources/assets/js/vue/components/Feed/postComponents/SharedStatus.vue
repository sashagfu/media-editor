<template>
  <div class="SharedStatus shared-status">
    <div class="shared-status__main">
      <div class="shared-status__side-detail"/>
      <div class="head-line">
        <div class="head-line__ava"/>
        <div class="head-line__title-box">
          <div class="head-line__title">
            Diana Jameson
          </div>
          <div class="head-line__time">
            16 hours ago
          </div>
        </div>
      </div>
      <div class="shared-status__main-text">
        Here’s the first photo of our incredible photoshoot from yesterday.
        If you like it please say so and tel me what you wanna see next!
      </div>
    </div>
  </div>
</template>

<script>

export default {
  components: {},
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .shared-status {
    margin-top : -26px;
    padding    : 26px;
    &__main {
      border-left : 1px solid $border;
      padding     : 22px;
      position    : relative;
      width       : 100%;
    }
    &__side-detail {
      width            : 9px;
      height           : 9px;
      border-radius    : 100%;
      position         : absolute;
      background-color : $background-light;
      border           : 2px solid $border;
      top              : 34px;
      left             : -5px;
    }
    &__main-text {
      color       : $text-light;
      font-size   : 13px;
      font-weight : $weight-normal;
      padding     : 22px 0 0 0;
      text-align  : left;
    }
  }

  .head-line {
    align-items : center;
    display     : flex;
    &__ava {
      flex          : 0 0 auto;
      height        : 26px;
      width         : 26px;
      border-radius : 100%;
      background    : url('http://lorempixel.com/26/26/people') center center no-repeat $grey-light;
    }
    &__title-box {
      padding-left   : 12px;
      display        : flex;
      flex-direction : column;
    }
    &__title {
      color       : $text;
      font-size   : 12px;
      font-weight : $weight-semibold;
      text-align  : left;
    }
    &__time {
      color       : $text-light;
      font-size   : 11px;
      font-weight : $weight-normal;
      text-align  : left;
    }
  }
</style>
