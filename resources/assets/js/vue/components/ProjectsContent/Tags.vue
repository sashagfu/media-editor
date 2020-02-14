<template>
  <div class="Tags tags">
    <div
      v-for="(tag, key) in tags"
      :key="`tag-${key}`"
      class="tags__item">
      <div class="tags__text">
        #{{ tag.name }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Tags',
  props: {
    tags: {
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
  @import '../../../../sass/front/components/bulma-theme';

  .tags {
    fl-left();
    flex-wrap: wrap;
    padding: 8px 24px;
    margin-bottom: 0;
    width: 100%;
    &__item {
      fl-left();
      padding: 0 6px;
      background-color: $primary;
      border-radius: $radius-small;
      margin-right: 2px;
    }
    &__text {
      fnt($text-invert, 12px, $weight-normal, left);
    }
  }

</style>
