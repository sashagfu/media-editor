<template>
  <div class="GalleryTabs gt">
    <div class="gt__empty"/>
    <div class="gt__left">
      <div
        v-for="(item, key) in tabs"
        :key="key"
        :title="item.title"
        :class="{
          'gt__item--active': item.value === activeComponent,
          'gt__item--enabled': item.enable
        }"
        class="gt__item"
        @click="onClickTab(item)"
      >
        <font-awesome-icon
          :icon="['fas', item.label]"
          :class="{'fa-icon--disabled': !item.enable}"
          class="fa-icon"
        />
      </div>
    </div>
    <div class="gt__empty gt__empty--btw"/>
    <div class="gt__right">
      <div
        v-for="(item, key) in tabsRight"
        :key="key"
        :title="item.title"
        :class="{
          'gt__item--active': item.value === activeComponent,
          'gt__item--enabled': item.enable
        }"
        class="gt__item"
        @click="onClickTab(item)"
      >
        <font-awesome-icon
          :icon="['fas', item.label]"
          :class="{'fa-icon--disabled': !item.enable}"
          class="fa-icon"
        />
      </div>
    </div>
    <div class="gt__empty"/>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';

export default {
  name: 'GalleryTabs',
  data() {
    return {
      tabs: [{
        value: 'me-media',
        title: 'Projects Gallery', // window.trans('media_editor.projects_gallery'),
        label: 'folder-open',
        enable: true,
      }, {
        //     value: 'me-library',
        //     label: 'archive',
        //     enable: false,
        // }, {
        value: 'me-text',
        title: 'Texts Gallery', // window.trans('media_editor.texts_gallery'),
        label: 'font',
        enable: true,
        // }, {
        //     value: 'me-video',
        //     label: 'film',
        //     enable: false,
        // }, {
        //     value: 'me-audio',
        //     label: 'headphones',
        //     enable: false,
        // }, {
        //     value: 'me-transitions',
        //     label: 'external-link-square-alt',
        //     enable: false,
      }],
      tabsRight: [{
        value: 'me-projects',
        title: 'Recent Projects', // this.trans('media_editor.recent_projects'),
        label: 'briefcase',
        enable: true,
      }, {
        value: 'me-clips',
        title: 'Clips', // this.$trans('media_editor.clips'),
        label: 'paperclip',
        enable: true,
      }],
    };
  },
  computed: {
    ...mapGetters('gallery', [
      'activeComponent',
    ]),
  },
  created() {
    this.tabs.forEach((item) => {
      if (!this.activeComponent && item.enable) {
        this.onClickTab(item);
      }
    });
  },
  methods: {
    ...mapActions('gallery', [
      'setComponent',
    ]),
    onClickTab(item) {
      if (item.enable) {
        this.setComponent(item.value);
      }
    },
  },
};
</script>

<style lang="stylus" scoped>
  @import '../../../../sass/front/components/bulma-theme';

  .fa-icon {
    color: $text-light;
    font-size: 1rem;
    transition: all .2s;
    &:hover {
      color: $text-lighter;
    }
    &--invert {
      color: $white;
      &:hover {
        color: $white-bis;
      }
    }
    &--pointer {
      cursor: pointer;
    }
    &--disabled {
      color: $grey-lighter;
      cursor: auto;
      &:hover {
        color: $grey-lighter;
      }
    }
    &__box {
      padding: 0 4px 0;
    }
  }

  .gt {
    fl-between();
    background-color: $white-ter;
    width: 100%;
    &__left {
      fl-left();
    }
    &__right {
      fl-right();
    }
    &__empty {
      border-bottom: 1px solid $border;
      flex: 0 0 auto;
      height: 24px;
      width: 24px;
      &--btw {
        flex: 1 1 auto;
        width: auto;
      }
    }
    &__item {
      fl-center();
      padding: 0 12px;
      height: 24px;
      border-bottom: 1px solid $border;
      flex: 0 0 auto;
      &--active {
        border: 1px solid $border;
        border-bottom: none;
        border-radius: 3px 3px 0 0;
        background-color: $white;
      }
      &--enabled {
        cursor: pointer;
      }
    }
  }

</style>
