<template>
  <div
    ref="droppable"
    class="Droppable"
  >
    <slot/>
  </div>
</template>

<script>
// import jQuery from 'jquery';
import $ from 'jquery';
import 'jquery-ui/ui/core';
import 'jquery-ui/ui/widgets/droppable';

export default {
  name: 'Droppable',
  props: {
    idAttrName: {
      type: String,
      default: '',
    },
    uuidAttrName: {
      type: String,
      default: '',
    },
    tolerance: {
      type: String,
      default: '',
    },
    disabled: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      droppable: null,
    };
  },
  watch: {
    tolerance(tolerance) {
      this.setOptions({ tolerance });
    },
    disabled(isDisabled) {
      $(this.$refs.droppable).droppable('option', 'disabled', isDisabled);
    },
  },
  mounted() {
    this.init();
  },
  beforeDestroy() {
    $(this.$refs.droppable).droppable('destroy');
  },
  methods: {
    init() {
      this.droppable = $(this.$refs.droppable).droppable({
        drop: this.drop,
        tolerance: this.tolerance,
        over: this.onOver,
        out: this.onOut,
      });
    },
    setOptions(options) {
      Object.keys(options).forEach(optionName => (
        this.droppable.droppable('option', optionName, options[optionName])
      ));
    },
    getDroppableAbsolutePosition() {
      const box = this.$refs.droppable.getBoundingClientRect();
      return {
        top: box.top + window.pageYOffset,
        left: box.left + window.pageXOffset,
      };
    },
    getInnerPosition(position) {
      const droppablePosition = this.getDroppableAbsolutePosition();
      return {
        top: position.top - droppablePosition.top,
        left: position.left - droppablePosition.left,
      };
    },
    getId(draggable) {
      return draggable.attr(this.idAttrName);
    },
    getUuid(draggable) {
      return draggable.attr(this.uuidAttrName);
    },
    prepareParamsToEmit(params) {
      // add innerPosition to parameters object
      Object.assign(params, {
        innerPosition: this.getInnerPosition(params.position),
      });
      if (this.idAttrName) {
        // add id from attribute to parameters object
        Object.assign(params, {
          id: this.getId(params.draggable),
        });
      }
      if (this.uuidAttrName) {
        // add uuid from attribute to parameters object
        Object.assign(params, {
          uuid: this.getUuid(params.draggable),
        });
      }
      return params;
    },
    drop(event, params) {
      this.$emit('drop', this.prepareParamsToEmit(params));
    },
    onOver(event, params) {
      this.$emit('over', params);
    },
    onOut(event, params) {
      this.$emit('out', params);
    },
  },
};
</script>
