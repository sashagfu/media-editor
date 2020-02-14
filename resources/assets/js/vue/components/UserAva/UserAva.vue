<template>
  <div
    :style="{
      'background-image':`url(${ava})`,
      'background-color': background
    }"
    class="user-ava"
  >
    <!--<UserChatStatus></UserChatStatus>-->
  </div>
</template>

<script>
//  import UserChatStatus from './UserChatStatus.vue';

export default {
  components: {
    // UserChatStatus
  },
  props: {
    ava: {
      type: String,
      default: '',
    },
    background: {
      type: String,
      default: '#a9acc1',
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .user-ava {
      background-size: cover;
      background: center center no-repeat ;
      border-radius: 50%;
      flex: 0 0 auto;
      height: 32px;
      padding-top: 1px;
      width: 32px;
  }

</style>
