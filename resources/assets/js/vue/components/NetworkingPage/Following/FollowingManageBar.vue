<template>
  <div class="FollowingManageBar fi-manage-bar">
    <div class="fi-manage-bar__left"/>
    <div class="fi-manage-bar__right">
      <el-input
        :value="searchText"
        :placeholder="trans('common.quick_search')"
        size="mini"
        class="input-with-select fi-manage-bar__search-inp"
        @input="debounceInput"
      >
        <el-button
          v-if="!searchText"
          slot="append"
          class="fi-manage-bar__search-btn"
        >
          <font-awesome-icon
            :icon="['fas', 'search']"
            class="fa-icon"
          />
        </el-button>
        <el-button
          v-if="searchText"
          slot="append"
          class="fi-manage-bar__search-btn"
          @click="clickEsc"
        >
          <font-awesome-icon
            :icon="['fas', 'times']"
            class="fa-icon"
          />
        </el-button>
      </el-input>
    </div>
  </div>
</template>

<script>
import { debounce } from 'lodash';

export default {
  name: 'FollowingManageBar',
  props: {
    searchText: {
      type: String,
      default: '',
    },
  },
  methods: {
    clickEsc() {
      this.$emit('input-search-text', '');
    },
    debounceInput: debounce(function updateSearch(text) {
      this.$emit('input-search-text', text);
    }, 600),
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .fa-icon {
    color      : $grey-light;
    font-size  : 16px;
    transition : all .2s;
    &:hover {
      opacity: 0.8;
    }
  }

  .fi-manage-bar {
    fl-between();
    margin-bottom: 1.5rem;
    width: 100%;
    &__left {
      fl-left();
    }
    &__right {
      fl-right();
    }
    &__title {
      fnt($text-lighter, 18px, $weight-semibold, left);
      text-transform: uppercase;
      line-height: 1;
    }
    &__search-inp {
      margin-left: 24px;
    }
    &__search-btn {
      width: 58px;
      background-color: rgba($primary, 0.1);
    }
  }
</style>

<style lang="stylus">

  @import '../../../../../sass/front/components/bulma-theme';

  .fi-manage-bar {
    .el-input__inner {
      fnt($text-light, 12px, $weight-normal, left);
      &:hover {
        border-color: $primary;
      }
      &::-webkit-input-placeholder {
        color       : $grey-light;
        font-weight : 300;
      }
      &::-moz-placeholder {
        color       : $grey-light;
        font-weight : 300;
      }
      &:-moz-placeholder {
        color       : $grey-light;
        font-weight : 300;
      }
      &:-ms-input-placeholder {
        color       : $grey-light;
        font-weight : 300;
      }
      &:hover {
        border-color: $white-shadow !important;
      }
      &:focus {
        border-color: $grey-lighter !important;
      }
    }
  }
</style>
