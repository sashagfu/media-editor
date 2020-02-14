<template>
  <div class="PreviewText me-player__image pr-text">
    <template v-if="frame.items.length">
      <div
        v-for="text in frame.items"
        :key="text.id"
        :style="{
          'top': `${text.position.y}px`,
          'left': `${text.position.x}px`,
          'color': text.color,
          'font-family': text.fontFamily,
          'font-size': `${text.fontSize}px`,
          'font-weight': text.fontWeight,
          'font-style': text.fontStyle,
          'text-align': text.textAlign,
        }"
        class="pr-text__item"
      >
        {{ text.text }}
      </div>
    </template>
    <div
      v-else
      class="pr-text__empty"
    >
      <font-awesome-icon
        :icon="['fas', 'font']"
        class="fa-icon fa-icon--add-text"
        @click.prevent="openDialog"
      />
      Texts frame is empty
    </div>
  </div>
</template>

<script>
export default {
  name: 'PreviewText',
  props: {
    frame: {
      type: Object,
      default: () => ({
        // items: [],
      }),
    },
  },
};
</script>

<style lang="stylus" scoped>
  @import '../../../../sass/front/components/bulma-theme';

  .pr-text {
    fl-center();
    position: relative;
    &__item {
      color: $white;
      display: flex;
      position: absolute;
      transform: translate(-50%, -50%);
    }
    &__empty {
      fl-center();
      fnt($text-invert, 1rem, $weight-semibold, center);
      flex-direction: column;
    }
  }
  .fa-icon {
    color: $text-light;
    font-size: 1rem;
    transition: all .2s;
    &--add-text {
      color: $blue;
      font-size: 2rem;
    }
  }

</style>
