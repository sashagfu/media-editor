<template>
  <div class="TruncateText truncated">
    <span
      v-if="!show"
      class="content"
    >
      {{ truncate(text) }}
    </span>
    <span v-if="!show">
      <span
        v-if="showLink"
        class="truncated__link truncated__link--primary"
        tabindex="-1"
        @click="toggle()"
      >
        {{ more }}
      </span>
    </span>
    <span
      v-if="show"
      class="content"
    >
      {{ text }}
    </span>
    <span v-if="show">
      <span
        v-if="showLink"
        tabindex="-1"
        class="truncated__link"
        @click="toggle()"
      >
        {{ less }}
      </span>
    </span>
  </div>
</template>

<script>

export default {
  name: 'TruncateText',
  props: {
    text: {
      type: String,
      default: '',
    },
    more: {
      type: String,
      default: 'Read More',
    },
    length: {
      type: Number,
      default: 300,
    },
    less: {
      type: String,
      default: 'Show Less',
    },
  },
  data() {
    return {
      show: false,
    };
  },
  computed: {
    showLink() {
      return this.text.length >= this.length;
    },
  },
  methods: {
    truncate(string) {
      if (string) {
        return string.toString().substring(0, this.length);
      }
      return '';
    },
    toggle() {
      this.show = !this.show;
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../sass/front/components/bulma-theme';

  .truncated
    &__link
      fnt($text-lighter, 12px, $weight-light, left)
      cursor pointer
      font-style italic
      outline none
      &--primary
        fnt($primary, 12px, $weight-light, left)

</style>
