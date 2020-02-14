<template>
  <div
    :class="[$route.name, toKebabCase($route.name)]"
  >
    <div
      class="chat-opener"
      @mouseover="openRightSidebar"
    />
    <TopBar
      v-if="user"
      :user="user"
    />
    <div
      :class="`${toKebabCase($route.name)}__main`"
      class="main"
    >
      <LeftSidebar v-if="user"/>
      <router-view :key="`${$route.path}${$route.hash}`"/>
      <RightSidebar v-if="user"/>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import TopBar from '../TopBar/TopBar';
import LeftSidebar from '../LeftSidebar/LeftSidebar';
import RightSidebar from '../RightSidebar/RightSidebar';

export default {
  name: 'GenericLayout',
  components: {
    TopBar,
    LeftSidebar,
    RightSidebar,
  },
  computed: {
    ...mapGetters('general', {
      user: 'activeUser',
    }),
  },
  methods: {
    toKebabCase(str) {
      return str &&
        str
          .match(/[A-Z]{2,}(?=[A-Z][a-z]+[0-9]*|\b)|[A-Z]?[a-z]+[0-9]*|[A-Z]|[0-9]+/g)
          .map(x => x.toLowerCase())
          .join('-');
    },
    openRightSidebar() {
      this.$bus.$emit('toggle-right-sidebar');
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  .main
    padding-top 73px
    background-color $white-bis
    display flex
    flex-grow 1
    position relative

  .chat-opener
    position absolute
    top 60px
    bottom 0
    right 0
    width 5px
    z-index 2

  .home-page__main,
  .login-page__main,
  .register-page__main,
  .verify-page__main,
  .reset-password-page__main
    padding-top 0
</style>
