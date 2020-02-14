<template>
  <div class="ProfilePageProjectsSearchBar pr-search-bar">
    <div class="pr-search-bar__left">
      <!-- <div class="pr-search-bar__feature">
        <font-awesome-icon
          :icon="['fas', 'trophy']"
          class="fa-icon"
        />
      </div>
      <div class="pr-search-bar__title">
        {{ trans('projects.most_popular') }}
      </div> -->
    </div>
    <div class="pr-search-bar__right">
      <template v-if="activeUser.id == user.id">
        <el-checkbox
          :value="unpublished"
          @input="toggleUnpublished"
        >
          {{ trans('profile_page.unpublished') }}
        </el-checkbox>
        <el-checkbox
          :value="rendered"
          @input="toggleRendered"
        >
          {{ trans('profile_page.rendered') }}
        </el-checkbox>
        <el-checkbox
          :value="published"
          @input="togglePublished"
        >
          {{ trans('profile_page.published') }}
        </el-checkbox>
        <el-checkbox
          :value="processing"
          @input="toggleProcessing"
        >
          {{ trans('profile_page.processing') }}
        </el-checkbox>
        <el-checkbox
          :value="failed"
          @input="toggleFailed"
        >
          {{ trans('profile_page.failed') }}
        </el-checkbox>
      </template>
      <el-input
        :value="searchText"
        size="mini"
        placeholder="Please input"
        class="input-with-select pr-search-bar__search-inp"
        @input="debounceInput"
      >
        <el-button
          v-if="!searchText"
          slot="append"
          class="pr-search-bar__search-btn"
        >
          <font-awesome-icon
            :icon="['fas', 'search']"
            class="fa-icon"
          />
        </el-button>
        <el-button
          v-if="searchText"
          slot="append"
          class="pr-search-bar__search-btn"
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
import { mapGetters } from 'vuex';

export default {
  name: 'ProfilePageProjectsSearchBar',
  props: {
    user: {
      type: Object,
      default: () => ({}),
    },
    unpublished: {
      type: Boolean,
      default: true,
    },
    rendered: {
      type: Boolean,
      default: true,
    },
    published: {
      type: Boolean,
      default: true,
    },
    failed: {
      type: Boolean,
      default: true,
    },
    processing: {
      type: Boolean,
      default: true,
    },
    searchText: {
      type: String,
      default: '',
    },
  },
  computed: {
    ...mapGetters('general', ['activeUser']),
  },
  methods: {
    toggleUnpublished() {
      this.$emit('toggle-unpublished');
    },
    toggleRendered() {
      this.$emit('toggle-rendered');
    },
    togglePublished() {
      this.$emit('toggle-published');
    },
    toggleFailed() {
      this.$emit('toggle-failed');
    },
    toggleProcessing() {
      this.$emit('toggle-processing');
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

  .pr-search-bar {
    fl-between();
    margin-bottom: 1.5rem;

    &__left {
      fl-left();
    }
    &__right {
      fl-right();
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

  .pr-search-bar {
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
