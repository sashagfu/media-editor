<template>
  <div
    class="LeftSidebar l-sb"
    @click.stop=""
  >
    <transition
      name="component-translate"
      mode="out-in"
    >
      <LeftSidebarNarrow
        v-if="isNarrow"
        @toggle-components="toggleComponents"
      />
      <LeftSidebarFull
        v-else
        @toggle-components="toggleComponents"
      />
    </transition>
  </div>
</template>

<script>
import LeftSidebarFull from './LeftSidebarFull';
import LeftSidebarNarrow from './LeftSidebarNarrow';

export default {
  name: 'LeftSidebar',
  components: {
    LeftSidebarFull,
    LeftSidebarNarrow,
  },
  data: () => ({
    isNarrow: true,
  }),
  mounted() {
    window.addEventListener('click', this.collapseMenu, false);
  },
  destroyed() {
    window.removeEventListener('click', this.collapseMenu, false);
  },
  methods: {
    toggleComponents() {
      this.isNarrow = !this.isNarrow;
    },
    collapseMenu() {
      this.isNarrow = true;
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .l-sb {
    display         : flex;
    flex-direction  : column;
    height          : 100%;
    justify-content : space-between;
    position        : fixed;
    top             : 0;
    z-index         : 1;
  }

  .component-translate-enter-active {
    transition : transform .5s ease-out;
  }

  .component-translate-leave-active {
    transition : transform .3s ease-in;
  }

  .component-translate-enter,
  .component-translate-leave-to {
    transform : translateX(-100%);
  }
</style>
