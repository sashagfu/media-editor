<template>
  <div
    ref="draggable"
    :class="{'hide-source': isSourceHidden}"
    :item-id="itemId"
    class="Draggable"
  >
    <slot/>
    <div
      v-if="$slots.dragImage"
      ref="dragImage"
      class="draggable__image"
    >
      <slot name="dragImage"/>
    </div>
  </div>
</template>

<script>
import $ from 'jquery';
import 'jquery-ui/ui/core';
import 'jquery-ui/ui/widgets/draggable';

export default {
  name: 'Draggable',
  props: {
    itemId: {
      type: [Number, String],
      required: true,
    },
    cursorAt: {
      type: Object,
      default: () => ({}),
    },
    containment: {
      type: String,
      default: '',
    },
    appendTo: {
      type: String,
      default: '',
    },
    scroll: {
      default: true,
      type: Boolean,
    },
    errorClass: {
      type: String,
      default: 'ui-draggable-error',
    },
    error: {
      type: Boolean,
      default: false,
    },
    successClass: {
      type: String,
      default: 'ui-draggable-success',
    },
    success: {
      type: Boolean,
      default: false,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      dragging: false,
      draggable: null,
      helper: null,
    };
  },
  computed: {
    isHelper() {
      return this.$refs.dragImage;
    },
    helperForDragging() {
      return this.isHelper ? () => this.$refs.dragImage : 'clone';
    },
    isSourceHidden() {
      return this.dragging && !this.isHelper;
    },
  },
  watch: {
    error(isError) {
      const { errorClass } = this;
      if (isError) {
        this.addClass(errorClass);
      } else {
        this.removeClass(errorClass);
      }
    },
    success(isSuccess) {
      const { successClass } = this;
      if (isSuccess) {
        this.addClass(successClass);
      } else {
        this.removeClass(successClass);
      }
    },
    helper() {
      if (this.success) {
        this.addClass(this.successClass);
      }
      if (this.error) {
        this.addClass(this.errorClass);
      }
    },
    disabled(isDisabled) {
      $(this.$refs.draggable).draggable('option', 'disabled', isDisabled);
    },
  },
  mounted() {
    this.mountDragImage();
    this.draggable = $(this.$refs.draggable).draggable({
      helper: this.helperForDragging,
      appendTo: this.appendTo,
      containment: this.containment,
      cursorAt: this.cursorAt,
      scroll: this.scroll,
      zIndex: 100,
      start: this.onDragStart,
      drag: this.onDrag,
      stop: this.onDragStop,
      disabled: this.disabled,
    });
  },
  beforeDestroy() {
    $(this.$refs.draggable).draggable('destroy');
  },
  methods: {
    addClass(className) {
      if (this.helper) {
        this.helper.addClass(className);
      }
    },
    removeClass(className) {
      if (this.helper) {
        this.helper.removeClass(className);
      }
    },
    onDragStart(event, params) {
      this.dragging = true;
      this.helper = params.helper;
      this.$emit('dragstart', params);
    },
    onDrag(event, params) {
      this.$emit('dragging', params);
    },
    onDragStop(event, params) {
      this.dragging = false;
      this.helper = null;
      this.$emit('dragstop', params);
    },
    mountDragImage() {
      if (this.isHelper) {
        document.body.appendChild(this.$refs.dragImage);
      }
    },

  },
};
</script>

<style lang="stylus" scoped>
    .draggable__image {
        position: absolute;
        top: -1000px;
        z-index: 10;
    }
    .hide-source {
        visibility: hidden;
    }
</style>
