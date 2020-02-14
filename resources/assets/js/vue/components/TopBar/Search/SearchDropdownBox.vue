<template>
  <div
    :class="[
      'content-box',
      {'content-box--pointer': item.__typename === 'Tag'}
    ]"
    @click="handleTag(item)"
  >
    <div
      v-if="item.__typename"
      class="content-box__item"
    >
      <div
        v-if="item.__typename === 'Project'"
        class="content-box__asset-box">
        <div
          v-if="hasAudioAsset || hasFullAsset"
          class="content-box__asset-item">
          <font-awesome-icon
            :icon="['fas', 'music']"
            class="fa-icon fa-icon--asset"
          />
        </div>
        <div
          v-if="hasVideoAsset || hasFullAsset"
          class="content-box__asset-item">
          <font-awesome-icon
            :icon="['fas', 'film']"
            class="fa-icon fa-icon--asset"
          />
        </div>
      </div>
      <div
        v-if="item.__typename === 'Project'"
        class="content-box__avatar-box"
      >
        <div
          :style="{
            'backgroundImage': `url(${asset.thumbPath})`,
            'background-color': randomBackgroundColor
          }"
          class="content-box__cover"
          @click="goToProject"
        >
          <font-awesome-icon
            :icon="['fas', 'play']"
            class="fa-icon"
          />
        </div>
      </div>
      <div
        v-else-if="item.__typename === 'User'"
        class="content-box__avatar-box"
        @click="goToProfile"
      >
        <Avatar
          v-if="item.avatar.includes('/profile/default/avatar')"
          :username="item.displayName"
          :size="40"
          custom-class="content-box__avatar"
        />
        <div
          v-else
          :style="{backgroundImage: `url(${item.avatar})`}"
          class="content-box__avatar"
        />
      </div>
      <div class="content-box__title-box">
        <div
          v-if="item.__typename === 'Tag'"
          class="content-box__tag"
        >
          #{{ item.name }}
        </div>
        <div
          v-if="item.__typename === 'Project'"
          class="content-box__title"
        >
          {{ item.title }}
          <span
            v-for="(tag, key) in item.tags"
            :key="`tag-${key}`"
            class="content-box__title-tag"
            @click="handleTag(tag)"
          >
            #{{ tag.name }}
          </span>
        </div>
        <div
          v-else-if="item.__typename === 'User'"
          class="content-box__title"
        >
          {{ item.displayName }}
        </div>
        <div
          v-if="item.__typename === 'Project'"
          class="content-box__hint"
        >
          {{ item.description }}
        </div>
        <div
          v-if="item.__typename === 'User'"
          class="content-box__hint"
        >
          @{{ item.username }}
        </div>
      </div>
    </div>
    <div
      v-if="item.__typename === 'Project'"
      class="content-box__item content-box__item--right"
    >
      <!-- <div class="content-box__asset-box">
        <div class="content-box__asset-item">
          <font-awesome-icon
            :icon="['fas', 'music']"
            class="fa-icon fa-icon--asset"
          />
        </div>
        <div class="content-box__asset-item">
          <font-awesome-icon
            :icon="['fas', 'film']"
            class="fa-icon fa-icon--asset"
          />
        </div>
      </div> -->
      <div class="content-box__action-box">
        <div
          v-if="item.comments.length"
          class="content-box__action-item">
          <font-awesome-icon
            :icon="['far', 'comments']"
            class="fa-icon fa-icon--action"
          />
          <div class="content-box__action-qty">
            {{ item.comments.length }}
          </div>
        </div>
        <div
          v-if="item.stars.length || item.likes.length"
          class="content-box__action-item">
          <font-awesome-icon
            :icon="['far', 'star']"
            class="fa-icon fa-icon--action"
          />
          <div class="content-box__action-qty">
            {{ item.stars.length + item.likes.length }}
          </div>
        </div>
        <div
          v-if="item.clipsCount"
          class="content-box__action-item">
          <font-awesome-icon
            :icon="['fas', 'paperclip']"
            class="fa-icon fa-icon--action"
          />
          <div class="content-box__action-qty">
            {{ item.clipsCount }}
          </div>
        </div>
      </div>
    </div>
    <div
      v-if="!item.__typename"
      class="content-box__item-error"
    >
      {{ item.label }}
    </div>
    <PlayVideoBox
      v-if="isPlayVideoDialogOpen && playVideoDialogIdOpen === item.id && asset"
      :project="item"
      :clip="asset"
      :user="activeUser"
    />
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import * as constants from 'Helpers/constants';
import Avatar from 'Pages/ProfilePage/Avatar';
import PlayVideoBox from 'Pages/MediaItem/PlayVideoBox';

export default {
  components: {
    Avatar,
    PlayVideoBox,
  },
  props: {
    item: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      backgroundColors: [
        '#F44336', '#FF4081', '#9C27B0', '#673AB7',
        '#3F51B5', '#2196F3', '#03A9F4', '#00BCD4', '#009688',
        '#4CAF50', '#8BC34A', '#CDDC39', '#FFC107',
        '#FF9800', '#FF5722', '#795548', '#9E9E9E', '#607D8B'],
    };
  },
  computed: {
    ...mapGetters('mediaEditorProjects', [
      'isPlayVideoDialogOpen',
      'playVideoDialogIdOpen',
    ]),
    ...mapGetters('general', [
      'activeUser',
    ]),
    randomBackgroundColor() {
      return this.backgroundColors[Math.floor(Math.random() * this.backgroundColors.length)];
    },
    asset() {
      if (this.item.assets.length) {
        return this.item.assets.find(a => a.type === constants.FULL);
      }
      return { thumbPath: '' };
    },
    hasAudioAsset() {
      return this.item.assets.some(a => a.type === constants.AUDIO);
    },
    hasVideoAsset() {
      return this.item.assets.some(a => a.type === constants.VIDEO);
    },
    hasFullAsset() {
      return this.item.assets.some(a => a.type === constants.FULL);
    },
  },
  methods: {
    handleTag(tag) {
      if (tag.__typename === 'Tag') {
        this.$emit('handle-tag', tag);
      }
      this.$emit('reset');
    },
    goToProject() {
      if (this.$route.path !== 'media-editor') {
        this.$emit('reset');
        this.$router.push({
          name: 'SingleProjectPage',
          params: {
            projectUuid: this.item.uuid,
          },
        });
      } else {
        window.location = `/projects/${this.item.uuid}`;
      }
    },
    goToProfile() {
      if (this.$route.path !== 'media-editor') {
        this.$emit('reset');
        this.$router.push({
          name: 'ProfilePage',
          params: {
            username: this.item.username,
          },
        });
      } else {
        window.location = `/profile/${this.item.username}`;
      }
    },
    makeTerm(term) {
      return term.replace(/[^\w\s]/gi, '');
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
    color      : $white;
    font-size  : 16px;
    transition : all .2s;
    &:hover {
      opacity: 0.8;
    }
    &--action {
      fnt($text-light, 14px, $weight-light, left);
    }
    &--asset {
      fnt($text-lighter, 10px, $weight-light, left);
    }
  }

  .content-box {
    fl-between();
    background-color : $white;
    width            : 100%;
    padding: 12px 26px;
    &:not(:first-child) {
      border-top: 1px solid $border;
    }
    &:hover {
      background-color : $white-bis;
    }
    &--pointer {
      cursor: pointer;
    }

    &__item {
      fl-left();
      align-items: flex-start;
      height  : 100%;
      width   : auto;
      &--right {
        fl-right();
      }
    }
    &__item-error {
      fl-center();
      width           : 100%;
      color           : $text-light;
    }
    &__avatar-box {
      fl-center();
      height          : 100%;
    }
    &__avatar {
      fl-center();
      cursor: pointer;
      border-radius   : 50%;
      background      : center center/cover no-repeat $info;
      height          : 40px;
      width           : 40px;
      padding-top     : 1px;
    }
    &__cover {
      fl-center();
      background   : center center/cover no-repeat $info;
      border-radius: 3px;
      cursor       : pointer;
      height       : 40px;
      width        : 40px;
    }
    &__title-box {
      align-items    : flex-start;
      display        : flex;
      flex-direction : column;
      height         : 100%;
      justify-content: center;
      padding-left   : 10px;
      text-align     : left;
      width          : auto;
    }
    &__title {
      fnt($text-light, 14px, $weight-semibold, left);
    }
    &__title-tag {
      fnt($primary, 12px, $weight-light, left);
      cursor: pointer;
      &:before {
        content: ' ';
      }
    }
    &__tag {
      fnt($primary, 14px, $weight-normal, left);
    }
    &__hint {
      fnt($text-lighter, 12px, $weight-normal, left);
      padding-left : 1px;
      padding-top  : 2px;
    }
    &__asset-box {
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
      margin-left: -18px;
      width: 18px;
    }
    &__asset-item {
      fl-center();
      padding: 0 4px;
    }
    &__asset-item + &__asset-item {
      margin-top: 2px;
    }
    &__action-box {
      fl-right();
    }
    &__action-item {
      fl-right();
      padding: 0 4px;
    }
    &__action-qty {
      fnt($text-light, 12px, $weight-normal, left);
      padding-left: 2px;
    }
  }
</style>
