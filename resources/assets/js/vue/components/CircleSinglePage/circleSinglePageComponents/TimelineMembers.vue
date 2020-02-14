<template>
  <div class="faved">
    <head-line :header="trans('circles.members')"/>
    <div class="faved__main-box">
      <div class="faved__row columns is-marginless is-multiline">
        <div
          v-for="(member, key) in members"
          :key="key"
          class="faved__column column is-3"
          @click="profileGo(member.username)"
        >
          <Avatar
            v-if="member.avatar.includes('/profile/default/avatar')"
            :username="member.display_name"
            :style="{paddingTop: 0, width: '66.88px', height: '66.88px',}"
            :class="'faved__item'"
            background-color="#b5b5b5"
          />
          <div
            v-if="!member.avatar.includes('/profile/default/avatar')"
            :style="{backgroundImage: `url(${member.avatar})`}"
            class="faved__item"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
/* @flow */
import HeadLine from '../../HeadLine/HeadLine';
import Avatar from '../../ProfilePage/Avatar';

export default {
  components: {
    HeadLine,
    Avatar,
  },
  props: {
    members: {
      type: Array,
      default: () => [],
    },
  },
  methods: {
    profileGo(username: String) {
      window.open(`/profile/${username}`, '_blank');
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .faved {
    &__main-box {
      padding : 20px;
    }
    &__row {
      padding : 6px 0;
    }
    &__column {
      padding : 0 6px;
    }
    &__item {
      //height: 40px;
      margin          : 6px 0;
      position        : relative;
      cursor          : pointer;
      padding-top     : 100%;
      border-radius   : 50%;
      background      : center center/cover no-repeat $grey-light;
      display         : flex;
      justify-content : center;
      align-items     : center;
    }
    &__cover {
      fnt($text-invert, 16px, $weight-semibold, center);
      background-color : $cover-turquoise;
      border-radius    : 50%;
      display          : flex;
      justify-content  : center;
      align-items      : center;
      position         : absolute;
      top              : 0;
      width            : 100%;
      height           : 100%;

    }
  }
</style>
