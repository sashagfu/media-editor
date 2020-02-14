<template>
  <div
    id="chat-page"
    class="ChatSinglePage chat-page"
  >
    <top-bar/>
    <div class="chat-page__main">
      <left-sidebar/>
      <div class="column chat-page__column">
        <chat-app/>
      </div>
    </div>
  </div>
</template>

<script>
/* @flow */
import TopBar from '../TopBar/TopBar';
import LeftSidebar from '../LeftSidebar/LeftSidebar';
import ChatApp from './chatComponents/ChatApp';

export default {
  components: {
    TopBar,
    LeftSidebar,
    ChatApp,
  },
};
</script>

<style
  lang="stylus"
  scoped
>
    @import '../../../../sass/front/components/bulma-theme';

    .chat-page {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        position: relative;
        &__main {
            background-color: $white-bis;
            display: flex;
            flex-grow: 1;
            padding-top: 73px;
            position: relative;
        }
        &__column {
            margin-left: 72px;
            padding: 32px 32px 0 28px;
        }
    }
</style>
