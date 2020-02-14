<template>
  <div class="MainGif post-main-gif">
    <div class="post-main-gif__gif box">
      <div class="post-main-gif__pic">
        gif
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

  .post-main-gif {
    padding : 0 26px 26px 26px;
    &__gif {
      align-items     : center;
      background      : url('http://lorempixel.com/700/580') center center no-repeat $grey-light;
      border-radius   : 3px;
      display         : flex;
      height          : 580px;
      justify-content : center;
      width           : 100%;
    }
    &__pic {
      fnt($light, 20px, $weight-bold, left);
      align-items      : center;
      background-color : transparent;
      border-radius    : 100%;
      border           : 2px dashed $light;
      cursor           : pointer;
      display          : flex;
      flex             : 0 0 auto;
      height           : 64px;
      justify-content  : center;
      text-transform   : uppercase;
      width            : 64px;
      &:hover {
        background-color : $primary;
      }
    }
  }

</style>

