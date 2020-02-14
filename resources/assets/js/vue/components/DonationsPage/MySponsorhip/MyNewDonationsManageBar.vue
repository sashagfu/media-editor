<template>
  <div class="NewDonationsManageBar nd-manage-bar">
    <div class="nd-manage-bar__left">
      <div class="nd-manage-bar__title">
        {{ trans('donate.my_new_donations') }}
      </div>
    </div>
    <div class="nd-manage-bar__right">
      <el-checkbox
        :value="pending"
        @input="togglePending"
      >
        {{ trans('donate.pending') }}
      </el-checkbox>
      <el-checkbox
        :value="accepted"
        @input="toggleAccepted"
      >
        {{ trans('donate.accepted') }}
      </el-checkbox>
      <el-checkbox
        :value="declined"
        @input="toggleDeclined"
      >
        {{ trans('donate.declined') }}
      </el-checkbox>
      <el-checkbox
        :value="expired"
        @input="toggleExpired"
      >
        {{ trans('donate.expired') }}
      </el-checkbox>
      <el-input
        :value="searchText"
        :placeholder="trans('common.quick_search')"
        size="mini"
        class="input-with-select nd-manage-bar__search-inp"
        @input="debounceInput"
      >
        <el-button
          v-if="!searchText"
          slot="append"
          class="nd-manage-bar__search-btn"
        >
          <font-awesome-icon
            :icon="['fas', 'search']"
            class="fa-icon"
          />
        </el-button>
        <el-button
          v-if="searchText"
          slot="append"
          class="nd-manage-bar__search-btn"
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
  name: 'NewDonationsManageBar',
  props: {
    searchText: {
      type: String,
      default: '',
    },
    pending: {
      type: Boolean,
      default: false,
    },
    accepted: {
      type: Boolean,
      default: false,
    },
    declined: {
      type: Boolean,
      default: false,
    },
    expired: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    togglePending() {
      this.$emit('toggle-pending');
    },
    toggleAccepted() {
      this.$emit('toggle-accepted');
    },
    toggleDeclined() {
      this.$emit('toggle-declined');
    },
    toggleExpired() {
      this.$emit('toggle-expired');
    },
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

  .nd-manage-bar {
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

<style
  lang="stylus"
>
  @import '../../../../../sass/front/components/bulma-theme';

  .nd-manage-bar {
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
