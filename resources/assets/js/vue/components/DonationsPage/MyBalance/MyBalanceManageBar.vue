<template>
  <div class="TopSponsorsManageBar mb-manage-bar">
    <div class="mb-manage-bar__left">
      <div class="mb-manage-bar__title">
        {{ trans('donate.balance') }} $ {{ user.balance }}
      </div>
      <div
        v-if="debit"
        class="mb-manage-bar__title">
        {{ trans('donate.received') }} $ {{ debit }}
      </div>
      <div
        v-if="credit"
        class="mb-manage-bar__title">
        {{ trans('donate.sent') }} $ {{ credit }}
      </div>
    </div>
    <div class="mb-manage-bar__right">
      <el-checkbox
        :value="filterDebit"
        @input="toggleDebit"
      >
        {{ trans('donate.received') }}
      </el-checkbox>
      <el-checkbox
        :value="filterCredit"
        @input="toggleCredit"
      >
        {{ trans('donate.sent') }}
      </el-checkbox>
      <el-date-picker
        v-model="value"
        :picker-options="pickerOptions"
        size="mini"
        type="daterange"
        range-separator="To"
        start-placeholder="Start date"
        end-placeholder="End date"
        class="mb-manage-bar__date-picker"
        @change="changePeriod"
      />

      <el-input
        :value="searchText"
        :placeholder="trans('common.quick_search')"
        size="mini"
        class="input-with-select mb-manage-bar__search-inp"
        @input="debounceInput"
      >
        <el-button
          v-if="!searchText"
          slot="append"
          class="mb-manage-bar__search-btn"
        >
          <font-awesome-icon
            :icon="['fas', 'search']"
            class="fa-icon"
          />
        </el-button>
        <el-button
          v-if="searchText"
          slot="append"
          class="mb-manage-bar__search-btn"
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
import moment from 'moment';

import * as constants from 'Helpers/constants';

export default {
  name: 'TopSponsorsManageBar',
  props: {
    credit: {
      type: Number,
      default: 0,
    },
    debit: {
      type: Number,
      default: 0,
    },
    searchText: {
      type: String,
      default: '',
    },
    filterDebit: {
      type: Boolean,
      default: false,
    },
    filterCredit: {
      type: Boolean,
      default: false,
    },
    valuePeriod: {
      type: Object,
      default: () => ({
        from: moment().subtract(1, 'month').startOf('month').format('YYYY-MM-DD HH:mm:ss'),
        to: moment().subtract(1, 'month').endOf('month').format('YYYY-MM-DD HH:mm:ss'),
      }),
    },
    user: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      constants,
      value: [],
      pickerOptions: {
        shortcuts: [{
          text: this.trans('donate.last_week'),
          onClick(picker) {
            const start = moment().subtract(1, 'week').startOf('week').format();
            const end = moment().subtract(1, 'week').endOf('week').format();
            picker.$emit('pick', [start, end]);
          },
        }, {
          text: this.trans('donate.last_month'),
          onClick(picker) {
            const start = moment().subtract(1, 'month').startOf('month').format();
            const end = moment().subtract(1, 'month').endOf('month').format();
            picker.$emit('pick', [start, end]);
          },
        }, {
          text: this.trans('donate.last_3_month'),
          onClick(picker) {
            const start = moment().subtract(3, 'month').startOf('month').format();
            const end = moment().subtract(1, 'month').endOf('month').format();
            picker.$emit('pick', [start, end]);
          },
        }],
      },
    };
  },
  watch: {
    valuePeriod: {
      immediate: true,
      handler() {
        this.value = [];
        const keys = Object.keys(this.valuePeriod);
        keys.forEach(k => this.value.push(this.valuePeriod[k]));
      },
    },
  },
  methods: {
    toggleDebit() {
      this.$emit('toggle-debit');
    },
    toggleCredit() {
      this.$emit('toggle-credit');
    },
    clickEsc() {
      this.$emit('input-search-text', '');
    },
    debounceInput: debounce(function updateSearch(text) {
      this.$emit('input-search-text', text);
    }, 600),
    changePeriod() {
      const newPeriod = {
        from: moment(this.value[0]).startOf('day').format('YYYY-MM-DD HH:mm:ss'),
        to: moment(this.value[1]).endOf('day').format('YYYY-MM-DD HH:mm:ss'),
      };
      this.$emit('change-period', newPeriod);
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

  .mb-manage-bar {
    fl-between();
    margin-bottom: 1.5rem;
    width: 100%;
    &__left {
      fl-left();
      width: 40%;
    }
    &__right {
      fl-right();
      width: 60%;
    }
    &__title {
      fnt($text-lighter, 18px, $weight-semibold, left);
      text-transform: uppercase;
      line-height: 1;
      &--primary {
        color: $primary;
      }
    }
    &__title + &__title {
      margin-left: 16px;
    }
    &__search-inp {
      margin-left: 24px;
      width: auto;
    }
    &__search-btn {
      // width: 58px;
      background-color: rgba($primary, 0.1);
      width: auto;
    }
    &__date-picker {
      margin-left: 24px;
    }
  }
</style>

<style
  lang="stylus"
>
  @import '../../../../../sass/front/components/bulma-theme';
  .mb-manage-bar {
    .el-range-editor--mini .el-range-separator {
      width: 28px;
    }
    .el-date-editor .el-range__icon {
      color: $text-lighter;
    }
    .el-input__icon.el-range__close-icon {
      display: none;
    }

    .el-input__inner, .el-range-input {
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
