<template>
  <div
    ref="containerEl"
    class="vue-scrollbar-only"
    @click.prevent.stop.self="onScrollbarClick"
  >
    <div
      :style="draggerStyle"
      class="vue-scrollbar-only__dragger"
      @mousedown.prevent.stop="onMouseDown"
    />
  </div>
</template>

<script>
export default {
  name: 'VueScrollbarOnly',
  props: {
    contentSize: {
      type: Number,
      required: true,
      validator: val => val >= 0,
    },
    containerSize: {
      type: Number,
      required: true,
      validator: val => val >= 0,
    },
    value: {
      type: Number,
      default: 0,
      validator: val => val >= 0,
    },
    horizontal: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      scrollBarSize: 0,
      scrollBarEdges: { start: 0, end: 0 },
      containerElementRect: {},
      offset: 0,
      isDragging: false,
      lastMousePosition: null,
    };
  },
  computed: {
    containerToContentRatio() {
      return this.containerSize / this.contentSize;
    },
    contentToScrollBarSizeRatio() {
      return this.contentSize / this.scrollBarSize;
    },
    draggerSize() {
      this.setScrollBarSize();
      return this.scrollBarSize * this.containerToContentRatio;
    },
    draggerStyle() {
      if (this.horizontal) {
        return {
          width: `${this.draggerSize}px`,
          marginLeft: `${this.offset}px`,
        };
      }
      return {
        height: `${this.draggerSize}px`,
        marginTop: `${this.offset}px`,
      };
    },
    maxOffset() {
      return this.scrollBarSize - this.draggerSize;
    },
    axis() {
      return this.horizontal ? 'X' : 'Y';
    },
    realOffset() {
      return this.offset * this.contentToScrollBarSizeRatio;
    },
  },
  watch: {
    value(value) {
      this.setOffsetByValue(value);
    },
    realOffset() {
      this.onRealOffsetChange();
    },
  },
  mounted() {
    this.addListeners();
    this.setScrollBarDimensions();
    this.setOffsetByValue();
  },
  destroyed() {
    this.removeListeners();
  },
  methods: {
    setScrollBarDimensions() {
      const { containerEl } = this.$refs;
      this.containerElementRect = containerEl && containerEl.getBoundingClientRect();
      if (this.containerElementRect) {
        this.setScrollBarSize();
        this.setScrollBarEdges();
      }
    },
    setScrollBarSize(rect = this.containerElementRect) {
      if (this.horizontal) {
        this.scrollBarSize = rect.width;
      } else {
        this.scrollBarSize = rect.height;
      }
    },
    setScrollBarEdges(rect = this.containerElementRect) {
      if (this.horizontal) {
        this.scrollBarEdges = {
          start: rect.left,
          end: rect.right,
        };
      } else {
        this.scrollBarEdges = {
          start: rect.top,
          end: rect.bottom,
        };
      }
    },
    setOffset(val) {
      this.offset = this.normalizeOffset(val);
    },
    setOffsetByValue(value = this.value) {
      this.setOffset(value / this.contentToScrollBarSizeRatio || 0);
    },
    normalizeOffset(val) {
      if (val < 0) {
        return 0;
      } else if (val > this.maxOffset) {
        return this.maxOffset;
      }
      return val;
    },
    normalizeMousePosition(mousePos) {
      const { start, end } = this.scrollBarEdges;
      if (mousePos < start) {
        return start;
      } else if (mousePos > end) {
        return end;
      }
      return mousePos;
    },
    onScrollbarClick(e) {
      const clickOffset = e[`offset${this.axis}`];
      this.setOffset(clickOffset - (this.draggerSize / 2));
    },
    onRealOffsetChange() {
      this.$emit('input', this.realOffset);
    },
    onMouseDown(e) {
      this.isDragging = true;
      this.lastMousePosition = this.horizontal ? e.clientX : e.clientY;
    },
    onMouseUp(e) {
      if (this.isDragging) {
        e.preventDefault();
        e.stopPropagation();
        this.isDragging = false;
      }
    },
    onMouseMove(e) {
      if (this.isDragging) {
        e.preventDefault();
        const clientPos = e[`client${this.axis}`];
        const mousePos = this.normalizeMousePosition(clientPos);
        const deltaPos = this.lastMousePosition - mousePos;
        this.lastMousePosition = mousePos;
        this.setOffset(this.offset - deltaPos);
      }
    },
    addListeners() {
      document.addEventListener('mousemove', this.onMouseMove);
      document.addEventListener('mouseup', this.onMouseUp);
      window.addEventListener('resize', this.setScrollBarDimensions);
    },
    removeListeners() {
      document.removeEventListener('mousemove', this.onMouseMove);
      document.removeEventListener('mouseup', this.onMouseUp);
      window.removeEventListener('resize', this.setScrollBarDimensions);
    },
  },
};
</script>

<style lang="stylus" scoped>
    @import '../../../../sass/front/components/bulma-theme';
    .vue-scrollbar-only {
        bottom: 0;
        height: 100%;
        left: 0;
        position: absolute;
        transition: all .3s;
        width: 100%;

        &__dragger {
            height: 100%;
            background-color: $grey;
        }
    }
</style>
