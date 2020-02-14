<template>
  <div class="sb__filter SearchBar">
    <div class="filter__search-inp">
      <div class="filter__inp-box">
        <input
          v-model="searchText"
          :placeholder="trans('common.quick_search')"
          type="text"
          name="quick-search"
          class="filter__inp"
          @input="$emit('input', $event.target.value)"
          @keydown.esc="resetSearchText"
        >
      </div>
      <div
        class="filter__inp-btn"
        @click="resetSearchText"
      >
        <font-awesome-icon
          v-if="searchText"
          :icon="['fas', 'times']"
          class="fa-icon fa-icon--search"
        />
        <font-awesome-icon
          v-else
          :icon="['fas', 'search']"
          class="fa-icon fa-icon--search"
        />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SearchBar',
  props: {
    value: {
      type: String,
      default: () => '',
    },
  },
  data() {
    return {
      searchText: '',
    };
  },
  methods: {
    resetSearchText() {
      this.searchText = '';
      this.$emit('input', this.searchText);
    },
  },
};
</script>

<style lang="stylus" scoped>
  @import '../../../../sass/front/components/bulma-theme';
  .fa-icon {
    fnt($white-shadow, 14px, $weight-normal, left)
    transition all .3s
    cursor pointer
    &--spinner {
      color $text
      font-size 16px
      margin -2px
    }
    &--dark {
      color $text-lighter
    }
    &--invert {
      fnt($text-invert, 12px, $weight-normal, center)
    }
  }

  .filter {
    &__close-btn {
      fl-center()
      height 100%
      padding 0 8px
    }
    &__search-inp {
      fl-left()
      margin 5px 8px

    }
    &__inp-box {
      background-color $white
      border-radius $radius 0 0 $radius
      border 1px solid $border
      border-right none
      height 28px
      padding 0 14px
      width: 100%;
      text-align: left;
    }
    &__inp {
      fnt($text-light, 12px, $weight-normal, left);
      size(26, 104)
      border none
      outline none
      width 100%

      &::-webkit-input-placeholder {
        color $grey-light
        font-weight 300
      }
      &::-moz-placeholder {
        color $grey-light
        font-weight 300
      }
      &:-moz-placeholder {
        color $grey-light
        font-weight 300
      }
      &:-ms-input-placeholder {
        color $grey-light
        font-weight 300
      }
    }
    &__inp-btn {
      fl-center()
      size(28, 28)
      background-color $white
      border-radius 0 $radius $radius 0
      border 1px solid $border
      border-left none
    }
  }
</style>
