<template>
  <div
    v-loading="!url"
    :style="{ backgroundImage: `url(${url})` }"
    class="ImagePreview me-player__image"
  />
</template>

<script>
export default {
  name: 'PreviewImage',
  props: {
    src: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      url: '',
    };
  },
  watch: {
    src() {
      this.preload();
    },
  },
  mounted() {
    this.preload();
  },
  methods: {
    preload() {
      this.url = '';
      const img = new Image();
      img.src = this.src;
      img.onload = () => {
        this.url = this.src;
      };
    },
  },
};
</script>
