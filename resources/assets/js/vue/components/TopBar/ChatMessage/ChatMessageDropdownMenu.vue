<template>
  <div class="ChatMessageDropdownMenu ch-mb">
    <div class="menu-diamond"/>
    <div class="ch-mb__title-box">
      <div class="ch-mb__left">
        <div class="ch-mb__title">
          {{ trans('chat.name') }}
        </div>
      </div>
      <div class="ch-mb__right"/>
    </div>
    <div
      v-if="loading"
      class="ch-mb__loading"
    >
      <font-awesome-icon
        :icon="['fas', 'spinner']"
        spin
        class="fa-icon"
      />
    </div>
    <div
      v-else-if="threadsIsEmpty"
      class="ch-mb__empty"
    >
      {{ trans('chat.have_not_threads') }}
    </div>
    <div
      v-else
      class="ch-mb__item"
    >
      <ChatMessageContentBox
        v-for="thread in threads"
        :key="thread.id"
        :thread="thread"
      />
    </div>
  </div>
</template>

<script>
import ChatMessageContentBox from './ChatMessageContentBox';

export default {
  name: 'ChatMessageDropdownMenu',
  components: {
    ChatMessageContentBox,
  },
  props: {
    threads: {
      type: Array,
      default: () => [],
    },
    loading: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      menuVisibility: true, // false,
    };
  },
  computed: {
    threadsIsEmpty() {
      return !this.threads.length;
    },
  },
  methods: {
    setActiveThread() {
    },
    setUnreadMessagesCount() {
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
    fnt($text-light, 1.5rem, $weight-light, center);
    transition : all .3s;
  }

  .menu-diamond {
    deg45();
    background-color : $white;
    border-left: 1px solid $border;
    border-top: 1px solid $border;
    height: 8px;
    left: 196px;
    position: absolute;
    top: -5px;
    width: 8px;
    z-index: 1;
  }

  .ch-mb {
    background-color: $white;
    border-radius: 3px;
    border: 1px solid $border;
    position: relative;
    width: 380px;

    &__title-box {
      fl-between();
      border-bottom: 1px solid $border;
      height: 36px;
      padding: 0 24px;
    }

    &__title {
      fnt($grey, 9px, $weight-normal, left);
      align-items: center;
      display: flex;
      height: 36px;
      line-height: 1%; // ?
      text-transform: uppercase;
      width: 100%;
      &--left {
        fl-left();
      }
      &--right {
        fl-right();
      }
      &--text {
        fnt($grey, 9px, $weight-normal, right);
      }
      &--menu {
        fnt($grey, 9px, $weight-bold, right);
      }
    }
    &__loading {
      fl-center();
      height: 80px;
      width: 100%;
    }
    &__empty {
      fnt($text-lighter, 18px, $weight-light, center);
      fl-center();
      text-transform: capitalize;
      height: 80px;
      width: 100%;
    }
    &__item {
      max-height: 400px;
      overflow-y: auto;
      &:not(:last-child) {
        border-bottom: 1px solid $border;
      }
    }
  }

  .view-all {
    fl-center();
    fnt($text-invert, 12px, $weight-semibold, center);
    background-color: $accented;
    border-radius: 0 0 $radius $radius;
    height: 52px;
    text-transform: capitalize;
    width: 100%;
  }
</style>
