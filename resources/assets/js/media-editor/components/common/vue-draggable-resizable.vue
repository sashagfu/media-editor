<template>
  <div
    ref="vdr"
    :class="{
      draggable: draggable,
      resizable: resizable,
      active: isEnabled,
      dragging: dragging,
      resizing: resizing
    }"
    :style="style"
    class="vdr"
    draggable="false"
    @mousedown.prevent.left="elmDown"
    @dblclick="fillParent"
  >
    <template v-if="resizable">
      <div
        v-for="(handle, key) in handles"
        :key="key"
        :class="'handle-' + handle"
        class="handle"
        @mousedown.prevent.left="handleDown(handle, $event)"
      />
    </template>
    <slot/>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  replace: true,
  name: 'VueDraggableResizable',
  props: {
    active: {
      type: Boolean,
      default: null,
    },
    draggable: {
      type: Boolean,
      default: true,
    },
    resizable: {
      type: Boolean,
      default: true,
    },
    w: {
      type: Number,
      default: 200,
      validator(val) {
        return val > 0;
      },
    },
    h: {
      type: Number,
      default: 200,
      validator(val) {
        return val > 0;
      },
    },
    minw: {
      type: Number,
      default: 50,
      validator(val) {
        return val > 0;
      },
    },
    minh: {
      type: Number,
      default: 50,
      validator(val) {
        return val > 0;
      },
    },
    maxw: {
      type: Number,
      default: Number.MAX_VALUE,
      validator(val) {
        return val > 0;
      },
    },
    maxh: {
      type: Number,
      default: Number.MAX_VALUE,
      validator(val) {
        return val > 0;
      },
    },
    x: {
      type: Number,
      default: 0,
      validator(val) {
        return val >= 0;
      },
    },
    y: {
      type: Number,
      default: 0,
      validator(val) {
        return val >= 0;
      },
    },
    z: {
      type: [String, Number],
      default: 'auto',
      validator(val) {
        return (typeof val === 'string') ? val === 'auto' : val >= 0;
      },
    },
    handles: {
      type: Array,
      default() {
        return ['tl', 'tm', 'tr', 'mr', 'br', 'bm', 'bl', 'ml'];
      },
    },
    axis: {
      type: String,
      default: 'both',
      validator(val) {
        return ['x', 'y', 'both'].indexOf(val) !== -1;
      },
    },
    grid: {
      type: Array,
      default() {
        return [1, 1];
      },
    },
    parent: {
      type: Boolean,
      default: false,
    },
    maximize: {
      type: Boolean,
      default: false,
    },
    // Distance in pixels after mousedown the mouse must move before dragging should start.
    distance: {
      type: Number,
      default: 1,
    },
    initialDragging: {
      type: Boolean,
      default: false,
    },
    lastMouseEvent: {
      type: [Object, MouseEvent],
      default: () => ({}),
    },
  },
  data() {
    return {
      top: this.y,
      left: this.x,
      width: this.w,
      height: this.h,
      resizing: false,
      dragging: false,
      enabled: this.active,
      handle: null,
      zIndex: this.z,
      startDraggingMousePosition: {},
      overDistance: false,
    };
  },
  computed: {
    ...mapGetters('player', [
      'playing',
    ]),
    style() {
      return {
        transform: `translate(${this.left}px, ${this.top}px)`,
        width: `${this.width}px`,
        height: `${this.height}px`,
        zIndex: this.zIndex,
      };
    },
    isEnabled() {
      return this.active !== null ? this.active : this.enabled;
    },
  },
  watch: {
    active(val) {
      this.enabled = val;
    },
    z(val) {
      if (val >= 0 || val === 'auto') {
        this.zIndex = val;
      }
    },
    w(newWidth) {
      this.width = newWidth;
    },
    h(newHeight) {
      this.height = newHeight;
    },
    x(newX) {
      this.left = newX;
    },
    y(newY) {
      this.top = newY;
    },
  },
  created() {
    this.parentX = 0;
    this.parentW = 9999;
    this.parentY = 0;
    this.parentH = 9999;

    this.mouseX = 0;
    this.mouseY = 0;

    this.lastMouseX = 0;
    this.lastMouseY = 0;

    this.mouseOffX = 0;
    this.mouseOffY = 0;

    this.elmX = 0;
    this.elmY = 0;

    this.elmW = 0;
    this.elmH = 0;
  },
  mounted() {
    document.documentElement.addEventListener('mousemove', this.handleMove, true);
    document.documentElement.addEventListener('mousedown', this.deselect, true);
    document.documentElement.addEventListener('mouseup', this.handleUp, true);

    if (this.minw > this.w) this.width = this.minw;

    if (this.minh > this.h) this.height = this.minh;

    if (this.parent) {
      const parentW = parseInt(this.$el.parentNode.clientWidth, 10);
      const parentH = parseInt(this.$el.parentNode.clientHeight, 10);

      this.parentW = parentW;
      this.parentH = parentH;

      if (this.w > this.parentW) this.width = parentW;

      if (this.h > this.parentH) this.height = parentH;

      if ((this.x + this.w) > this.parentW) this.width = parentW - this.x;

      if ((this.y + this.h) > this.parentH) this.height = parentH - this.y;
    }
    this.$emit('resizing', this.left, this.top, this.width, this.height);

    if (this.initialDragging) {
      this.activator(this.lastMouseEvent);
      this.handleMove(this.lastMouseEvent);
    }
  },
  beforeDestroy() {
    document.documentElement.removeEventListener('mousemove', this.handleMove, true);
    document.documentElement.removeEventListener('mousedown', this.deselect, true);
    document.documentElement.removeEventListener('mouseup', this.handleUp, true);
  },
  methods: {
    getMousePosition(e) {
      if (!e) {
        return {
          x: 0,
          y: 0,
        };
      }
      const mousePosition = {
        x: e.pageX || e.clientX + document.documentElement.scrollLeft,
        y: e.pageY || e.clientY + document.documentElement.scrollTop,
      };
      // const x = e.pageX || e.clientX + document.documentElement.scrollLeft;
      // const y = e.pageY || e.clientY + document.documentElement.scrollTop;
      // return { x, y };
      return mousePosition;
    },
    activator(e) {
      if (!this.enabled) {
        this.enabled = true;
        this.$emit('activated');
        this.$emit('update:active', true);
      }
      // this.elmX = parseInt(this.$el.style.left, 10);
      // this.elmY = parseInt(this.$el.style.top, 10);
      this.elmX = this.left;
      this.elmY = this.top;
      this.elmW = this.$el.offsetWidth || this.$el.clientWidth;
      this.elmH = this.$el.offsetHeight || this.$el.clientHeight;

      if (this.draggable) {
        this.dragging = true;
        this.startDraggingMousePosition = this.getMousePosition(e);
      }
    },
    elmDown(e) {
      if (this.playing) return;
      const target = e.target || e.srcElement;

      if (this.$el.contains(target)) {
        this.activator(e);
      }
    },
    deselect(e) {
      const target = e.target || e.srcElement;
      const regex = new RegExp('handle-([trmbl]{2})', '');

      if (!this.$el.contains(target) && !regex.test(target.className)) {
        if (this.enabled) {
          this.enabled = false;

          this.$emit('deactivated');
          this.$emit('update:active', false);
        }
      }
    },
    handleDown(handle, e) {
      if (this.playing) return;
      this.handle = handle;

      if (e.stopPropagation) e.stopPropagation();
      if (e.preventDefault) e.preventDefault();

      this.resizing = true;
    },
    fillParent() {
      if (!this.parent || !this.resizable || !this.maximize) return;

      let done = false;

      const animate = () => {
        if (!done) {
          window.requestAnimationFrame(animate);
        }
        if (this.axis === 'x') {
          if (this.width === this.parentW && this.left === this.parentX) {
            done = true;
          }
        } else if (this.axis === 'y') {
          if (this.height === this.parentH && this.top === this.parentY) {
            done = true;
          }
        } else if (this.axis === 'both') {
          if (
            this.width === this.parentW &&
                        this.height === this.parentH &&
                        this.top === this.parentY &&
                        this.left === this.parentX
          ) {
            done = true;
          }
        }
        if (this.axis === 'x' || this.axis === 'both') {
          if (this.width < this.parentW) {
            this.width += 1;
            this.elmW += 1;
          }

          if (this.left > this.parentX) {
            this.left -= 1;
            this.elmX -= 1;
          }
        }
        if (this.axis === 'y' || this.axis === 'both') {
          if (this.height < this.parentH) {
            this.height += 1;
            this.elmH += 1;
          }
          if (this.top > this.parentY) {
            this.top -= 1;
            this.elmY -= 1;
          }
        }
        this.$emit('resizing', this.left, this.top, this.width, this.height);
      };
      window.requestAnimationFrame(animate);
    },
    handleMove(e) {
      const mousePosition = this.getMousePosition(e);

      // Mouse should be moved on distance before element will be move
      if (!this.overDistance && this.dragging) {
        const mouseDiffX = Math.abs(mousePosition.x - this.startDraggingMousePosition.x);
        const mouseDiffY = Math.abs(mousePosition.y - this.startDraggingMousePosition.y);
        if (mouseDiffX < this.distance && mouseDiffY < this.distance) {
          return;
        }
        this.overDistance = true;
      }

      this.mouseX = mousePosition.x;
      this.mouseY = mousePosition.y;

      let diffX = (this.mouseX - (this.lastMouseX || this.mouseX)) + this.mouseOffX;
      let diffY = (this.mouseY - this.lastMouseY) + this.mouseOffY;

      this.mouseOffX = 0;
      this.mouseOffY = 0;

      this.lastMouseX = this.mouseX;
      this.lastMouseY = this.mouseY;

      const dX = diffX;
      const dY = diffY;

      if (this.resizing) {
        if (this.handle.indexOf('t') >= 0) {
          if (this.elmH - dY < this.minh) {
            this.mouseOffY = (dY - (diffY = this.elmH - this.minh));
          } else if (this.elmH - dY > this.maxh) {
            this.mouseOffY = (dY - (diffY = this.elmH - this.maxh));
          } else if (this.elmY + dY < this.parentY) {
            this.mouseOffY = (dY - (diffY = this.parentY - this.elmY));
          }
          this.elmY += diffY;
          this.elmH -= diffY;
        }

        if (this.handle.indexOf('b') >= 0) {
          if (this.elmH + dY < this.minh) {
            this.mouseOffY = (dY - (diffY = this.minh - this.elmH));
          } else if (this.elmH + dY > this.maxh) {
            this.mouseOffY = (dY - (diffY = this.maxh - this.elmH));
          } else if (this.elmY + this.elmH + dY > this.parentH) {
            this.mouseOffY = (dY - (diffY = this.parentH - this.elmY - this.elmH));
          }
          this.elmH += diffY;
        }

        if (this.handle.indexOf('l') >= 0) {
          if (this.elmW - dX < this.minw) {
            this.mouseOffX = (dX - (diffX = this.elmW - this.minw));
          } else if (this.elmW - dX > this.maxw) {
            this.mouseOffX = (dX - (diffX = this.elmW - this.maxw));
          } else if (this.elmX + dX < this.parentX) {
            this.mouseOffX = (dX - (diffX = this.parentX - this.elmX));
          }
          this.elmX += diffX;
          this.elmW -= diffX;
        }

        if (this.handle.indexOf('r') >= 0) {
          if (this.elmW + dX < this.minw) {
            this.mouseOffX = (dX - (diffX = this.minw - this.elmW));
          } else if (this.elmW + dX > this.maxw) {
            this.mouseOffX = (dX - (diffX = this.maxw - this.elmW));
          } else if (this.elmX + this.elmW + dX > this.parentW) {
            this.mouseOffX = (dX - (diffX = this.parentW - this.elmX - this.elmW));
          }
          this.elmW += diffX;
        }

        this.width = (Math.round(this.elmW / this.grid[0]) * this.grid[0]);
        this.left = (Math.round(this.elmX / this.grid[0]) * this.grid[0]);
        this.height = (Math.round(this.elmH / this.grid[1]) * this.grid[1]);
        this.top = (Math.round(this.elmY / this.grid[1]) * this.grid[1]);

        this.$emit('resizing', this.left, this.top, this.width, this.height, this.handle);
      } else if (this.dragging) {
        if (this.elmX + dX < this.parentX) {
          this.mouseOffX = (dX - (diffX = this.parentX - this.elmX));
        } else if (this.elmX + this.elmW + dX > this.parentW) {
          this.mouseOffX = (dX - (diffX = this.parentW - this.elmX - this.elmW));
        }

        if (this.elmY + dY < this.parentY) {
          this.mouseOffY = (dY - (diffY = this.parentY - this.elmY));
        } else if (this.elmY + this.elmH + dY > this.parentH) {
          this.mouseOffY = (dY - (diffY = this.parentH - this.elmY - this.elmH));
        }

        this.elmX += diffX;
        this.elmY += diffY;

        if (this.axis === 'x' || this.axis === 'both') {
          this.left = (Math.round(this.elmX / this.grid[0]) * this.grid[0]);
        }
        if (this.axis === 'y' || this.axis === 'both') {
          this.top = (Math.round(this.elmY / this.grid[1]) * this.grid[1]);
        }
        this.$emit('dragging', this.left, this.top, e);
      }
    },
    handleUp() {
      if (this.resizing) {
        this.resizing = false;
        this.$emit('resizestop', this.left, this.top, this.width, this.height, this.handle);
      }
      this.handle = null;
      if (this.dragging) {
        this.dragging = false;
        // Reset over distance
        this.overDistance = false;
        this.$emit('dragstop', this.left, this.top);
      }
      this.elmX = this.left;
      this.elmY = this.top;
    },
  },
};
</script>

<style lang="stylus" scoped>
    .vdr {
        position: absolute;
        box-sizing: border-box;
        -webkit-user-drag: none;

        &.active {
            .handle {
                display: block;
            }
        }
    }

    .draggable:hover {
        cursor: move;
    }

    .handle {
        box-sizing: border-box;
        display: none;
        position: absolute;
        width: 10px;
        height: 10px;
        font-size: 1px;
        background: #EEE;
        border: 1px solid #333;
    }

    .handle-tl {
        top: -10px;
        left: -10px;
        cursor: nw-resize;
    }

    .handle-tm {
        top: -10px;
        left: 50%;
        margin-left: -5px;
        cursor: n-resize;
    }

    .handle-tr {
        top: -10px;
        right: -10px;
        cursor: ne-resize;
    }

    .handle-ml {
        background: transparent;
        border    : none;
        cursor    : ew-resize;
        height    : 100%;
        left      : -5px;
        top       : 2px;
    }

    .handle-mr {
        background: transparent;
        border    : none;
        cursor    : ew-resize;
        height    : 100%;
        right     : -5px;
        top       : 2px;
    }

    .handle-bl {
        bottom: -10px;
        left: -10px;
        cursor: sw-resize;
    }

    .handle-bm {
        bottom: -10px;
        left: 50%;
        margin-left: -5px;
        cursor: s-resize;
    }

    .handle-br {
        bottom: -10px;
        right: -10px;
        cursor: se-resize;
    }
</style>
