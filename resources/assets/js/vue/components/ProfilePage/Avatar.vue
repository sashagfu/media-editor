<template>
  <div
    :class="customClass"
    :style="style"
  >
    <!--suppress JSUnresolvedVariable -->
    <span
      v-if="!src"
    >
      {{ userInitial }}
    </span>
  </div>
</template>

<!--suppress ReservedWordAsName -->
<script type="text/babel">
const Avatar = {
  props: {
    username: {
      type: String,
      required: true,
    },
    initials: {
      type: String,
    },
    backgroundColor: {
      type: String,
    },
    color: {
      type: String,
    },
    size: {
      type: Number,
      default: 50,
    },
    src: {
      type: String,
    },
    customClass: {
      type: String,
    },
    rounded: {
      type: Boolean,
      default: true,
    },
    lighten: {
      type: Number,
      default: 80,
    },
  },

  data() {
    return {
      backgroundColors: [
        '#F44336', '#FF4081', '#9C27B0', '#673AB7',
        '#3F51B5', '#2196F3', '#03A9F4', '#00BCD4', '#009688',
        '#4CAF50', '#8BC34A', '#CDDC39', '#FFC107',
        '#FF9800', '#FF5722', '#795548', '#9E9E9E', '#607D8B'],
    };
  },

  mounted() {
    this.$emit('avatar-initials', this.username, this.userInitial);
  },

  computed: {
    background() {
      return this.backgroundColor ||
        this.randomBackgroundColor(this.username.length, this.backgroundColors);
    },

    fontColor() {
      return this.color || this.lightenColor(this.background, this.lighten);
    },

    isImage() {
      return this.src !== undefined;
    },

    style() {
      const imgBackgroundAndFontStyle = {
        background: `url(${this.src}) center center no-repeat ${this.background}`,
        backgroundSize: `${this.size}px ${this.size}px`,
      };

      const initialBackgroundAndFontStyle = {
        backgroundColor: this.background,
        font: `${Math.floor(this.size / 2.5)}px/100px Helvetica, Arial, sans-serif`,
        fontWeight: 'bold',
        color: this.fontColor,
        lineHeight: 1, // `${(this.size + Math.floor(this.size / 20))}px`,
      };

      const backgroundAndFontStyle = (this.isImage)
        ? imgBackgroundAndFontStyle
        : initialBackgroundAndFontStyle;

      return Object.assign({}, backgroundAndFontStyle);
    },

    userInitial() {
      return this.initials || this.initial(this.username);
    },
  },

  methods: {
    initial(username) {
      const parts = username.split(/[ -]/);
      let initials = '';

      for (let i = 0; i < parts.length; i += 1) {
        initials += parts[i].charAt(0);
      }

      if (initials.length > 3 && initials.search(/[A-Z]/) !== -1) {
        initials = initials.replace(/[a-z]+/g, '');
      }

      initials = initials.substr(0, 3).toUpperCase();

      return initials;
    },

    randomBackgroundColor(seed, colors) {
      return colors[seed % (colors.length)];
    },

    lightenColor(hashhex, amt) {
      // From https://css-tricks.com/snippets/javascript/lighten-darken-color/
      let usePound = false;
      let hex = hashhex;

      if (hashhex[0] === '#') {
        hex = hashhex.slice(1);
        usePound = true;
      }

      const num = parseInt(hex, 16);
      let r = (num >> 16) + amt; // eslint-disable-line no-bitwise

      if (r > 255) r = 255;
      else if (r < 0) r = 0;

      let b = ((num >> 8) & 0x00FF) + amt; // eslint-disable-line no-bitwise

      if (b > 255) {
        b = 255;
      } else if (b < 0) {
        b = 0;
      }

      let g = (num & 0x0000FF) + amt; // eslint-disable-line no-bitwise

      if (g > 255) g = 255;
      else if (g < 0) g = 0;

      return (usePound ? '#' : '') + (g | (b << 8) | (r << 16)).toString(16); // eslint-disable-line no-bitwise
    },
  },
};
export default Avatar;
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .profile-avatar {
    background center center/cover no-repeat $grey-dark
    border 6px solid $background-light
    border-radius 50%
    display flex
    flex-direction column
    font-size 40px !important
    height 126px
    justify-content center
    position absolute
    top -100px
    width 126px
  }

</style>
