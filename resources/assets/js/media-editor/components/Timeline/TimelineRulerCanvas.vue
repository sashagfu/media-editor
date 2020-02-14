<template>
  <canvas
    ref="canvas"
    class="TimelineRulerCanvas me-tl-ruler__canvas"
  />
</template>

<script>
import { mapGetters } from 'vuex';
import {
  TIMELINE_RULER,
  ONE_FRAME_LENGTH,
} from '../../config/settings';
import { timeFormat } from '../../utils/time';

export default {
  name: 'TimelineRulerCanvas',
  computed: {
    ...mapGetters('coordinates', [
      'timelineRulerContainer',
    ]),
    ...mapGetters('timeline', [
      'zoomedPxPerSec',
      'scrollHTime',
    ]),
    canvas() {
      return this.$refs.canvas;
    },
    ctx() {
      return this.canvas.getContext('2d');
    },
    /**
     * Positions values which won't change during drawing loop
     * @returns {{ lineYEnd: *, lineYStart: number, textY: number }}
     */
    staticPositions() {
      const { height } = this.canvas;
      const { lineHeight, textMarginBottom } = TIMELINE_RULER;
      return {
        lineYEnd: height,
        lineYStart: height - lineHeight,
        textY: height - textMarginBottom,
      };
    },
    // Time between lines, means line per seconds
    timeBtwLine() {
      const { pxBtwLine } = TIMELINE_RULER;
      let val = pxBtwLine / this.zoomedPxPerSec;

      // If more then one second
      if (val > 1000) {
        // Round to seconds
        val = Math.round(val / 1000) * 1000;
      } else {
        // otherwise round to frame
        val -= (val % ONE_FRAME_LENGTH);
      }
      return val;
    },
    // Convert canvas length into time
    canvasLengthNeeded() {
      return (this.canvas.width / this.zoomedPxPerSec) + this.timeBtwLine;
    },
    // Time need to shift to make notches draw on round position
    shiftTime() {
      return this.scrollHTime % this.timeBtwLine;
    },
  },
  watch: {
    zoomedPxPerSec() {
      this.drawNotches();
    },
    timelineRulerContainer() {
      this.drawNotches();
    },
    scrollHTime() {
      this.drawNotches();
    },
  },
  methods: {
    /**
     * Reset canvas and set dimensions from store
     */
    resetCanvas() {
      const { height, width } = this.timelineRulerContainer;
      this.canvas.height = height;
      this.canvas.width = width;
    },
    /**
     * Set context parameters
     */
    setCtxParams() {
      const { lineWidth, lineColor, textColor } = TIMELINE_RULER;
      const { ctx } = this;
      ctx.lineWidth = lineWidth;
      ctx.strokeStyle = lineColor;
      ctx.fillStyle = textColor;
    },
    /**
     * Draw notches and time on ruler
     */
    drawNotches() {
      // Before draw, clear canvas
      this.resetCanvas();
      this.setCtxParams();

      const { textMarginLeft } = TIMELINE_RULER;
      // const { width } = this.canvas;
      const { lineYEnd, lineYStart, textY } = this.staticPositions;
      const {
        zoomedPxPerSec,
        // scrollH,
        ctx,
        timeBtwLine,
        canvasLengthNeeded,
        scrollHTime,
        shiftTime,
      } = this;

      ctx.beginPath();
      for (let i = 0; i < canvasLengthNeeded; i += timeBtwLine) {
        const pos = Math.round((i - shiftTime) * zoomedPxPerSec) + 0.5;
        ctx.moveTo(pos, lineYStart);
        ctx.lineTo(pos, lineYEnd);

        const time = Math.round((i + scrollHTime) - shiftTime);
        ctx.fillText(timeFormat(time), pos + textMarginLeft, textY);
      }
      ctx.stroke();
    },
  },
};
</script>
