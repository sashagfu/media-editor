<template>
  <div
    ref="resizable"
    class="Resizable"
  >
    <slot/>
  </div>
</template>

<script>
import $ from 'jquery';
import 'jquery-ui/ui/core';
import 'jquery-ui/ui/widgets/resizable';

export default {
  name: 'Resizable',
  props: {
    maxWidth: {
      type: Number,
      required: true,
    },
    maxHeight: {
      type: Number,
      required: true,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      resizable: null,
    };
  },
  watch: {
    disabled(isDisabled) {
      $(this.$refs.resizable).resizable('option', 'disabled', isDisabled);
    },
  },
  mounted() {
    this.resizable = $(this.$refs.resizable).resizable({
      ...this.$attrs,
      ...this.$props,
      start: this.onStart,
      stop: this.onStop,
      resize: this.onResize,
    });
    this.setWatchersToUpdateProps();
  },
  methods: {
    setOptions(options) {
      Object.keys(options).forEach(optionName => (
        this.resizable.resizable('option', optionName, options[optionName])
      ));
    },
    onStart(event, params) {
      const { axis } = $(params.element).data('ui-resizable');
      this.$emit('resizestart', { ...params, axis });
    },
    onResize(event, params) {
      this.$emit('resizing', params);
    },
    onStop(event, params) {
      this.$emit('resizestop', params);
    },
    // Set watch to update jquery object when props update
    setWatchersToUpdateProps() {
      Object.keys(this.$props).forEach((propName) => {
        this.$watch(propName, (newValue) => {
          this.setOptions({ [propName]: newValue });
        });
      });
    },
  },
};
</script>
