<template>
  <div class="Credits credits">
    Credits:

    <a
      v-for="(credit, index) in credits"
      :key="index"
      :href="`/profile/${credit.username}`"
      target="_blank"
      class="credits__item"
    >
      @{{ credit.username }}
    </a>
  </div>
</template>

<script>
export default {
  name: 'Credits',
  props: {
    credits: {
      type: Array,
      default: () => [],
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .credits {
    fnt($text, 14px, $weight-light, left);
    fl-left();
    flex-wrap: wrap;
    padding: 0 26px 8px;

    &__item {
      fnt($primary, 14px, $weight-light, left);
      display: flex;
      text-transform: lowercase;
      transition: all .3s;
      &:hover {
        color: $lime-hover;
      }
      &:first-child {
        padding-left: 4px;
      }
      &:not(:last-child) {
        padding-right: 4px;
      }
      &:not(:last-child):after {
        content: ',';
      }
    }
  }
</style>
