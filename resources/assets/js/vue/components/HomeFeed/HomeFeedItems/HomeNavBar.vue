<template>
  <div class="HomeNavBar home-nav-bar box">
    <!-- <div
      ref="cover"
      class="home-nav-bar__cover"
    >
       <div class="home-nav-bar__user-box">
        <div class="home-nav-bar__avatar-box">
          <div class="home-nav-bar__avatar"
            :style="{'background-image': `url(${activeUser.avatar})`}"
          />
        </div>
        <div class="home-nav-bar__title-box">
          <div class="home-nav-bar__user-title">
            {{ activeUser.display_name }}
          </div>
          <div class="home-nav-bar__user-sub-title">
            @{{ activeUser.username }}
          </div>
        </div>
      </div>
    </div> -->
    <div class="home-nav-bar__manage-box">
      <div class="home-nav-bar__item">
        <div
          :class="[
            'home-nav-bar__title',
            {'home-nav-bar__title--active': currentComponent === 'HomeFeedRecommended'}
          ]"
          @click="activeComponent('HomeFeedRecommended')"
        >
          {{ trans('home_projects.recommended') }}
        </div>
      </div>
      <div class="home-nav-bar__item">
        <div
          :class="[
            'home-nav-bar__title',
            {'home-nav-bar__title--active': currentComponent === 'HomeFeedCircles'}
          ]"
          @click="activeComponent('HomeFeedCircles')"
        >
          {{ trans('home_projects.circles') }}
        </div>
      </div>
      <div class="home-nav-bar__item">
        <div
          :class="[
            'home-nav-bar__title',
            {'home-nav-bar__title--active': currentComponent === 'HomeFeedFriends'}
          ]"
          @click="activeComponent('HomeFeedFriends')"
        >
          {{ trans('home_projects.friends') }}
        </div>
      </div>
      <div class="home-nav-bar__item">
        <div
          :class="[
            'home-nav-bar__title',
            {'home-nav-bar__title--active': currentComponent === 'HomeFeedMyFeed'}
          ]"
          @click="activeComponent('HomeFeedMyFeed')"
        >
          {{ trans('home_projects.my_feed') }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  name: 'HomeNavBar',
  props: {
    currentComponent: {
      type: String,
      default: '',
    },
  },
  computed: {
    ...mapGetters('general', [
      'activeUser',
    ]),
  },
  methods: {
    activeComponent(activeComponent) {
      this.$emit('change-active-component', activeComponent);
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .home-nav-bar {
    width: 100%;

    &__cover {
      position: relative;
      width: 100%;
      height: 240px;
      border-radius : $radius $radius 0 0;
      background: center center/cover no-repeat $primary;
      box-shadow: inset 0px -48px 106px -24px rgba($black-ter, .6);
    }
    &__user-box {
      position: absolute;
      bottom: 0;
      left: 0;
      display: flex;
    }
    &__avatar-box {
      position: relative;
      padding-left: 64px;
      width: 190px;
      height: 80px;
    }
    &__avatar {
      height: 126px;
      width: 126px;
      background: center center no-repeat $grey-dark;
      border: 6px solid white;
      border-radius: 50%;
    }
    &__title-box {
      padding: 0 0 12px 26px;
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
    }
    &__user-title {
      fnt($text-invert, 26px, $weight-bold, left);
    }
    &__user-sub-title {
      fnt($text-invert, 14px, $weight-normal, left);
    }
    &__manage-box {
      fl-left();
      width: 100%;
      height: 68px;
    }
    &__left {
      fl-center();
      width: 50%;
    }
    &__right {
      fl-center();
      width: 50%;
    }
    &__item {
      fl-center();
      width: 25%;
    }
    &__title {
      fnt($text, 14px, $weight-semibold, center);
      cursor: pointer;
      transition: all .3s;
      &--active {
        color: $primary;
      }
      &:hover {
        opacity: 0.8;
      }
    }
  }
</style>
