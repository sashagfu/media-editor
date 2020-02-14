<template>
  <div class="SingleProjectPageUser box spu">
    <div
      class="spu__left"
      @click="goToProfile"
    >
      <Avatar
        v-if="author.avatar.includes('/profile/default/avatar')"
        :username="author.displayName || author.display_name"
        :size="40"
        custom-class="spu__avatar"
      />
      <div
        v-else
        :style="{backgroundImage: `url(${author.avatar})`}"
        class="spu__avatar"
      />
      <div class="spu__title-box">
        <div class="spu__title">
          {{ author.displayName || author.display_name }}
        </div>
        <div class="spu__sub-title">
          On Actionlime from {{ formatData(project.author.createdAt) }}
        </div>
      </div>
    </div>
    <div class="fl-right">
      <div class="spu__btn-box">
        <FollowButton
          :following="author"
          :active-user="user"
          :round="true"
        />
      </div>
      <div class="spu__btn-box">
        <MessageButton
          :user="author"
          :round="true"
        />
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment';

import Avatar from 'Pages/ProfilePage/Avatar';
import FollowButton from 'Pages/Common/FollowButton';
import MessageButton from 'Pages/Common/MessageButton';// resources/assets/js/vue/components/Common/MessageButton.vue


export default {
  name: 'SingleProjectPageUser',
  components: {
    Avatar,
    FollowButton,
    MessageButton,
  },
  props: {
    project: {
      type: Object,
      default: () => ({}),
    },
    user: {
      type: Object,
      default: () => ({}),
    },
  },
  computed: {
    author() {
      return this.project.author;
    },
  },
  methods: {
    formatData(dt) {
      return moment(dt).format('MMMM YYYY');
    },
    goToProfile() {
      this.$router.push({
        name: 'ProfilePage',
        params: {
          username: this.author.username,
        },
      });
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .spu // Single Project User
    fl-between()
    padding 16px 26px
    &__left
      fl-left()
      cursor pointer
    &__right
      fl-right()
    &__avatar
      circle(40)
      background center center/cover no-repeat $grey-light
    &__title-box
      display flex
      flex-direction column
      padding-left 8px
    &__title
      fnt($text, 16px, $weight-normal, left);
    &__sub-title
      fnt($primary, 12px, $weight-normal, left);
    &__btn-box + &__btn-box
      margin-left 8px


</style>
