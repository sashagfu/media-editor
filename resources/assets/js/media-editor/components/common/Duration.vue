<template>
  <time :dateTime="`P${seconds}S`">
    {{ time }}
  </time>
</template>

<script>
export default {
  name: 'Duration',
  props: {
    s: {
      type: Number,
      validator(value) {
        return !Number.isNaN(value);
      },
      default: 0,
    },
    ms: {
      type: [Number, String],
      validator(value) {
        return !Number.isNaN(value);
      },
      default: 0,
    },
    frameRate: {
      type: Number,
      default: 25,
      validator: val => val > 0,
    },
    showFrame: {
      type: Boolean,
      default: false,
    },
    mZero: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    seconds() {
      return Math.round(this.s ? this.s : this.ms / 1000);
    },
    milliseconds() {
      return this.ms ? this.ms : this.s * 1000;
    },
    isNegative() {
      return this.milliseconds < 0;
    },
    dateObject() {
      return new Date(Math.abs(this.milliseconds));
    },
    hh() {
      const hh = this.dateObject.getUTCHours();
      return hh ? `${hh}:` : '';
    },
    mm() {
      const mm = this.dateObject.getMinutes();
      return this.mZero || this.hh ? this.addZero(mm) : mm;
    },
    sec() {
      return this.addZero(this.dateObject.getSeconds());
    },
    msec() {
      return this.dateObject.getMilliseconds();
    },
    fr() {
      return this.addZero(Math.floor(this.msec / (1000 / this.frameRate)));
    },
    frText() {
      return this.showFrame ? `.${this.fr}` : '';
    },
    time() {
      const {
        hh,
        mm,
        sec,
        isNegative,
        frText,
      } = this;
      return `${isNegative ? '-' : ''}${hh}${mm}:${sec}${frText}`;
    },
  },
  methods: {
    addZero(val) {
      return `0${val}`.slice(-2);
    },
  },
};
</script>
