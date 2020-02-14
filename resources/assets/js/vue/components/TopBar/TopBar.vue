<template>
  <div
    ref="topBar"
    :class="{'top-bar--transitional': transitional}"
    class="TopBar top-bar"
  >
    <div class="top-bar__left">
      <TopBarLogo/>
      <!--<top-bar-title/>-->
    </div>
    <div class="top-bar__center">
      <TopBarSearch/>
    </div>
    <div class="top-bar__right">
      <div class="tablet">
        <TopBarSearch/>
      </div>

      <!--<TopBarPlayer/>-->
      <!--<FriendRequest/>-->
      <Upload v-show="processing || uploading"/>
      <Balance v-if="activeUser.balance"/>
      <ChatMessage v-if="!chatPage"/>
      <Notification/>
      <Projects/>
      <TopBarUser/>
    </div>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';

import TopBarLogo from './TopBarLogo';
import TopBarTitle from './TopBarTitle';
import TopBarSearch from './Search/TopBarSearch';
//  import FriendRequest from './FriendRequest/FriendRequest';
import Projects from './Projects/Projects';
import Balance from './Balance/Balance';
import Upload from './Upload/TopBarUpload';
import ChatMessage from './ChatMessage/ChatMessage';
import Notification from './Notification/Notification';
import TopBarUser from './User/TopBarUser';
//  import TopBarPlayer from './TopPlayer/TopBarPlayer';

const TopBar = {
  name: 'top-bar',
  components: {
    TopBarLogo,
    TopBarTitle,
    TopBarSearch,
    TopBarUser,
    // TopBarPlayer,
    // FriendRequest,
    ChatMessage,
    Notification,
    Projects,
    Balance,
    Upload,
  },
  data() {
    return {
      topHeight: 0,
      transitional: false,
      oldScrollTop: 0,
    };
  },
  computed: {
    ...mapGetters('general', [
      'activeUser',
    ]),
    ...mapGetters('upload', [
      'processing',
      'uploading',
    ]),
    chatPage() {
      return 0;
    },
  },
  created() {
    if (localStorage.getItem('uploadingFiles')) this.setProcessing(true);
  },
  methods: {
    ...mapActions('upload', [
      'setProcessing',
    ]),
    getCurrentScroll() {
      const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

      if (scrollTop > this.topHeight) {
        this.transitional = true;
        if (scrollTop >= this.oldScrollTop) {
          this.$refs.topBar.style.top = `${this.topHeight * -1}px`;
        } else {
          this.$refs.topBar.style.top = '0';
        }
      } else {
        this.transitional = false;
        if (scrollTop >= this.oldScrollTop) {
          this.$refs.topBar.style.top = `${scrollTop * -1}px`;
        } else {
          this.$refs.topBar.style.top = '0';
        }
      }
      this.oldScrollTop = scrollTop;
    },
  },
  mounted() {
    window.addEventListener('scroll', this.getCurrentScroll, false);
    const { height } = this.$refs.topBar.getBoundingClientRect();
    this.topHeight = height;
  },
  destroyed() {
    window.removeEventListener('scroll', this.getCurrentScroll, false);
  },
};

export default TopBar;
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .top-bar {
    fl-between();
    background-color: $white;
    border-bottom: 1px solid $border;
    position: fixed;
    width: 100%;
    z-index: 1000;

    &--transitional {
      transition: all .5s;
    }

    +tablet() {
      display: flex;
      flex-direction: column;
    }

    &__left {
      fl-left();
      height: 100%;
      width: 33.3%;

      +tablet() {
        height: 72px;
        width: 100%;
      }

    }

    &__center {
      fl-center();
      height: 100%;
      position: relative;
      width: 33.3%;
      +tablet() {
        display: none;
      }
    }

    &__right {
      fl-right()
      height 100%
      padding-right 42px
      position relative
      width 33.3%
      +tablet() {
        fl-between()
        height 72px
        width 100%
        padding 0 32px
      }
    }
  }

  .tablet {
    display none
    +tablet() {
      display flex
    }
  }
</style>
