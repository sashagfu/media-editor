<template>
  <div class="top-bar-title">
    <!--<div class="top-bar-title__text">-->
    <!--Profile page-->
    <!--</div>-->
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
  @import '../../../../sass/front/components/bulma-theme';

  .top-bar-title {
    fnt($text-invert, 14px, $weight-semibold, left);
    fl-left()
    text-transform: uppercase;
    height: 100%;
    width: 50%;
    +tablet() {
      display : none;
    }
    +desktop() {
      display : none;
    }
  }

</style>
