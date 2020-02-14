<template>
  <div class="BorderBetweenReplay border-between-replay">
    <div class="border"/>
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

  .border-between-replay {
    padding-left     : 37px;
    background-color : $background;
  }

  .border {
    border-top : 1px solid $border;
    height     : 1px;
    width      : 100%;
  }
</style>
