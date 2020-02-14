<template>
  <div class="DonationsPageNavBar dp-nav-bar box">
    <div
      class="dp-nav-bar__item"
      @click="handleClick('TopSponsors')"
    >
      <div
        :class="[
          'dp-nav-bar__title',
          {'dp-nav-bar__title--active': activeComponent === 'TopSponsors'}
        ]"
      >
        {{ trans('donate.top_sponsors') }}
      </div>
    </div>
    <div
      class="dp-nav-bar__item"
      @click="handleClick('MySponsors')"
    >
      <div
        :class="[
          'dp-nav-bar__title',
          {'dp-nav-bar__title--active': activeComponent === 'MySponsors'}
        ]"
      >
        {{ trans('donate.my_sponsors') }}
      </div>
    </div>
    <div
      class="dp-nav-bar__item"
      @click="handleClick('MySponsorship')"
    >
      <div
        :class="[
          'dp-nav-bar__title',
          {'dp-nav-bar__title--active': activeComponent === 'MySponsorship'}
        ]"
      >
        {{ trans('donate.my_sponsorship') }}
      </div>
    </div>
    <div class="dp-nav-bar__item">
      <div
        class="dp-nav-bar__item"
        @click="handleClick('MyBalance')"
      >
        <div
          :class="[
            'dp-nav-bar__title',
            {'dp-nav-bar__title--active': activeComponent === 'MyBalance'}
          ]"
        >
          {{ trans('donate.my_balance') }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DonationsPageNavBar',
  props: {
    activeComponent: {
      type: String,
      default: '',
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

  .dp-nav-bar {
    fl-left();
    height: 72px;
    &__item {
      fl-center();
      cursor: pointer;
      height: 100%;
      width: 25%;
    }
    &__title {
      fnt($text-light, 16px, $weight-normal, center);
      text-transform: capitalize;
      white-space: nowrap;
      &--active {
        color: $primary;
      }
    }
  }
</style>
