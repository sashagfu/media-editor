<template>
  <div class="ProfilePageClipsSearchBar pc-search-bar">
    <div class="pc-search-bar__left">
      <!-- <div class="pc-search-bar__feature">
        <font-awesome-icon
          :icon="['fas', 'trophy']"
          class="fa-icon"
        />
      </div>
      <div class="pc-search-bar__title">
        {{ trans('projects.most_popular') }}
      </div> -->
    </div>
    <div class="pc-search-bar__right">
      <div class="pc-search-bar__title">
        {{ trans('profile_page.show') }}
      </div>
      <el-checkbox
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
      </el-checkbox>
      <el-input
        :value="searchText"
        size="mini"
        placeholder="Please input"
        class="input-with-select pc-search-bar__search-inp"
        @input="debounceInput"
      >
        <el-button
          v-if="!searchText"
          slot="append"
          class="pc-search-bar__search-btn"
        >
          <font-awesome-icon
            :icon="['fas', 'search']"
            class="fa-icon"
          />
        </el-button>
        <el-button
          v-if="searchText"
          slot="append"
          class="pc-search-bar__search-btn"
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
    showFullClips: {
      type: Boolean,
      default: true,
    },
    showVideoClips: {
      type: Boolean,
      default: true,
    },
    showAudioClips: {
      type: Boolean,
      default: true,
    },
    searchText: {
      type: String,
      default: '',
    },
  },

  methods: {
    toggleFullClips() {
      this.$emit('toggle-full-clips');
    },
    toggleVideoClips() {
      this.$emit('toggle-video-clips');
    },
    toggleAudioClips() {
      this.$emit('toggle-audio-clips');
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

  .pc-search-bar {
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
      padding-right: 30px;
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

  .pc-search-bar {
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
