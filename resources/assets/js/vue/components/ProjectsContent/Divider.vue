<template>
  <div class="Divider divider">
    <div class="divider__border"/>
  </div>
</template>

<script>
export default {
  name: 'Divider',
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .divider {
    padding 0 26px
    &__border {
      border-top 1px solid $border
      height 1px
      width 100%
    }
  }

</style>
