<template>
  <div class="ProfilePagePlaylistsSearchBar ppl-search-bar">
    <div class="ppl-search-bar__left"/>
    <div class="ppl-search-bar__right">
      <div class="ppl-search-bar__title">
        {{ trans('profile_page.sorted') }}
      </div>

      <el-select
        v-model="value"
        size="mini"
        class="ppl-search-bar__sorted"
        placeholder="Select"
      >
        <el-option
          v-for="item in options"
          :key="item.value"
          :label="item.label"
          :value="item.value"
        />
      </el-select>

      <div class="ppl-search-bar__title">
        {{ trans('profile_page.show') }}
      </div>
      <!-- <el-checkbox
        :value="showFullClips"
        @input="toggleFullClips"
      >
        {{ trans('profile_page.full_clips') }}
      </el-checkbox>
      <el-checkbox
        :value="showVideoClips"
        @input="toggleVideoClips"
      >
        {{ trans('profile_page.video_clips') }}
      </el-checkbox>
      <el-checkbox
        :value="showAudioClips"
        @input="toggleAudioClips"
      >
        {{ trans('profile_page.audio_clips') }}
      </el-checkbox> -->
      <el-input
        :value="searchText"
        size="mini"
        placeholder="Please input"
        class="input-with-select ppl-search-bar__search-inp"
        @input="debounceInput"
      >
        <el-button
          v-if="!searchText"
          slot="append"
          class="ppl-search-bar__search-btn"
        >
          <font-awesome-icon
            :icon="['fas', 'search']"
            class="fa-icon"
          />
        </el-button>
        <el-button
          v-if="searchText"
          slot="append"
          class="ppl-search-bar__search-btn"
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
  name: 'ProfilePageClipsTopTile',
  props: {
    user: {
      type: Object,
      default: () => ({}),
    },
    searchText: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      options: [{
        value: 'Option1',
        label: 'Option1',
      }, {
        value: 'Option2',
        label: 'Option2',
      }, {
        value: 'Option3',
        label: 'Option3',
      }],
      value: '',
    };
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

  .ppl-search-bar {
    fl-between();
    margin-bottom: 1.5rem;
    &__left {
      fl-left();
    }
    &__right {
      fl-right();
    }
    &__title {
      fnt($text-light, 14px, $weight-normal, left);
      padding-left: 26px;
      white-space: nowrap;
    }
    &__sorted {
      width: 240px;
      padding-left: 26px;
      &:hover {
        border-color: $grey-lighter;
      }
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

  .ppl-search-bar {
    .el-input__inner {
      fnt($text-light, 12px, $weight-normal, left);
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
  }
</style>
