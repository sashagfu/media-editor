<template>
  <div class="NetworkingPageNavBar np-nav-bar box">
    <div
      class="np-nav-bar__item"
      @click="handleClick('Following')"
    >
      <div
        :class="[
          'np-nav-bar__title',
          {'np-nav-bar__title--active': activeComponent === 'Following'}
        ]"
      >
        {{ trans('network.following') }}
        <span class="np-nav-bar__follow-count">({{ user.totalFollowing }})</span>
      </div>
    </div>
    <div
      class="np-nav-bar__item"
      @click="handleClick('Followers')"
    >
      <div
        :class="[
          'np-nav-bar__title',
          {'np-nav-bar__title--active': activeComponent === 'Followers'}
        ]"
      >
        {{ trans('network.followers') }}
        <span class="np-nav-bar__follow-count">({{ user.totalFollowers }})</span>
      </div>
    </div>
    <div class="np-nav-bar__item"/>
    <div class="np-nav-bar__item"/>
  </div>
</template>

<script>

export default {
  name: 'NetworkingPageNavBar',
  props: {
    activeComponent: {
      type: String,
      default: 'Following',
    },
    user: {
      type: Object,
      default: () => {},
    },
  },
  methods: {
    handleClick(item) {
      this.$emit('change-component', item);
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .np-nav-bar {
    fl-left();
    height: 72px;
    &__item {
      fl-center();
      width: 25%;
      height: 100%;
      cursor: pointer;
    }
    &__title {
      fnt($text-light, 16px, $weight-normal, center);
      text-transform: capitalize;
      white-space: nowrap;
      &--active {
        color: $primary;
      }
    }
    &__follow-count {
      color: #7a7a7a;
    }
  }
</style>
