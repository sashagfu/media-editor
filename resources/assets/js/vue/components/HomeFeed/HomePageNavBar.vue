<template>
  <div class="HomePageNavBar hp-nav-bar box">
    <div class="hp-nav-bar__item">
      <div
        :class="[
          'hp-nav-bar__title',
          { 'hp-nav-bar__title--active': activeComponent === 'HomePageMyFeed' },
        ]"
        @click="hangleClick('HomePageMyFeed')"
      >
        {{ trans('feed.my_feed') }}
      </div>
    </div>
    <div class="hp-nav-bar__item">
      <div
        :class="[
          'hp-nav-bar__title',
          { 'hp-nav-bar__title--active': activeComponent === 'HomePageRecommended' },
        ]"
        @click="hangleClick('HomePageRecommended')"
      >
        {{ trans('feed.recommended') }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'HomePageNavBar',
  props: {
    activeComponent: {
      type: String,
      default: '',
    },
    user: {
      type: Object,
      default: () => {
      },
    },
  },
  methods: {
    hangleClick(component) {
      this.$emit('change-component', component);
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .hp-nav-bar
    fl-left()
    height 72px
    justify-content space-around
    text-transform: capitalize;

    &__item
      fl-center()
      width 25%
      height 100%
      cursor pointer

    &__title
      fnt($text-light, 16px, $weight-normal, center)

      &--active
        color $primary
</style>
