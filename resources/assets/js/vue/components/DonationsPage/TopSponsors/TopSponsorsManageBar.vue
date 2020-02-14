<template>
  <div class="TopSponsorsManageBar ts-manage-bar">
    <div class="ts-manage-bar__left"/>
    <div class="ts-manage-bar__right">
      <el-select
        v-model="value"
        placeholder="Select"
        size="mini"
        @change="changePeriod"
      >
        <el-option
          v-for="item in options"
          :key="item.value"
          :label="item.label"
          :value="item.value"
        />
      </el-select>

      <el-input
        :value="searchText"
        :placeholder="trans('common.quick_search')"
        size="mini"
        class="input-with-select ts-manage-bar__search-inp"
        @input="debounceInput"
      >
        <el-button
          v-if="!searchText"
          slot="append"
          class="ts-manage-bar__search-btn"
        >
          <font-awesome-icon
            :icon="['fas', 'search']"
            class="fa-icon"
          />
        </el-button>
        <el-button
          v-if="searchText"
          slot="append"
          class="ts-manage-bar__search-btn"
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
import * as constants from 'Helpers/constants';

export default {
  name: 'TopSponsorsManageBar',
  props: {
    searchText: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      constants,
      value: constants.LAST_MONTH,
      fetchTopSponsors: [],
      options: [{
        value: constants.ALL_TIME,
        label: this.trans('profile_page.all_time'),
      }, {
        value: constants.LAST_MONTH,
        label: this.trans('profile_page.last_month'),
      }, {
        value: constants.LAST_WEEK,
        label: this.trans('profile_page.last_week'),
      }],
    };
  },
  methods: {
    clickEsc() {
      this.$emit('input-search-text', '');
    },
    debounceInput: debounce(function updateSearch(text) {
      this.$emit('input-search-text', text);
    }, 600),
    changePeriod() {
      this.$emit('change-period', this.value);
    },
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

  .ts-manage-bar {
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

  .ts-manage-bar {
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
    .el-select .el-input .el-select__caret {
      color: $text-light;
    }
    .el-select:hover .el-input__inner {
      border-color: $border;
    }
  }
</style>
