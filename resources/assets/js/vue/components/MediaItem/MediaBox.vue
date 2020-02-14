<template>
  <div class="MediaBox media-box box is-marginless">
    <div
      v-if="usersData"
      class="media-box__header">
      <div class="media-box__left">
        <div class="media-box__title">
          {{ item.title }}
        </div>
      </div>
      <div class="media-box__right">
        <div
          v-if="item.views"
          class="media-box__views">
          {{ item.views }} views
        </div>
      </div>
    </div>
    <div
      :style="{'background-image': `url(${item.coverImg})`}"
      class="media-box__main"
    >
      <div
        class="media-box__play-btn"
        @click="openPlayVideo"
      >
        <font-awesome-icon
          :icon="['fas', 'play']"
          class="fa-icon fa-icon--play"
        />
      </div>
      <div class="media-box__media-type-box">
        <div
          v-if="item.type === constants.AUDIO || item.type === constants.FULL"
          class="media-box__media-type-item"
        >
          <font-awesome-icon
            :icon="['fas', 'music']"
            class="fa-icon fa-icon--media"
          />
        </div>
        <div
          v-if="item.type === constants.VIDEO || item.type === constants.FULL"
          class="media-box__media-type-item"
        >
          <font-awesome-icon
            :icon="['fas', 'film']"
            class="fa-icon fa-icon--media"
          />
        </div>
      </div>
    </div>
    <div
      v-if="withFooter"
      class="media-box__footer">
      <div class="media-box__left media-box__left--top">
        <div
          v-if="usersData & !item.author.avatar.includes('/profile/default/avatar')"
          :style="{'background-image': `url(${item.author.avatar})`}"
          class="media-box__avatar"
        />
        <Avatar
          v-if="usersData & item.author.avatar.includes('/profile/default/avatar')"
          :username="item.author.displayName"
          custom-class="media-box__avatar"
        />
        <div class="media-box__name-box">
          <div
            v-if="usersData"
            class="media-box__name"
          >
            {{ item.author.displayName }}
          </div>
          <div
            v-else
            class="media-box__title"
          >
            {{ item.title }}
          </div>
          <div class="media-box__sub-name">
            <template v-if="usersData">
              {{ trans('home_projects.on_actionlime') }} {{ formatData(item.author.createdAt) }}
            </template>
            <template v-else>
              {{ item.views }} views
            </template>
          </div>
        </div>
      </div>
      <div class="media-box__right media-box__right--top">
        <div class="media-box__action-box">
          <div class="media-box__action-item">
            <font-awesome-icon
              :icon="['far', 'comments']"
              class="fa-icon "
            />
            <div
              v-if="item.comment"
              class="media-box__action-qty">
              {{ item.comment }}
            </div>
          </div>
          <div class="media-box__action-item">
            <font-awesome-icon
              :icon="['far', 'star']"
              class="fa-icon "
            />
            <div
              v-if="item.starred"
              class="media-box__action-qty">
              {{ item.starred }}
            </div>
          </div>
          <div class="media-box__action-item">
            <font-awesome-icon
              :icon="['fas', 'paperclip']"
              class="fa-icon "
            />
            <div
              v-if="item.clipped"
              class="media-box__action-qty">
              {{ item.clipped }}
            </div>
          </div>
        </div>
      </div>
    </div>
    <PlayVideoBox
      v-if="isPlayVideoDialogOpen && playVideoDialogIdOpen === item.project.id"
      :project="item.project"
      :clip="item"
      :user="user"
    />
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import moment from 'moment';
import Avatar from 'Pages/ProfilePage/Avatar';
import * as constants from 'Helpers/constants';
import PlayVideoBox from './PlayVideoBox';

export default {
  name: 'MediaBox',
  components: {
    Avatar,
    PlayVideoBox,
  },
  props: {
    item: {
      type: Object,
      default: () => ({}),
    },
    user: {
      type: Object,
      default: () => ({}),
    },
    usersData: {
      type: Boolean,
      default: true,
    },
    withFooter: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      constants,
    };
  },
  computed: {
    ...mapGetters('mediaEditorProjects', [
      'isPlayVideoDialogOpen',
      'playVideoDialogIdOpen',
    ]),
  },
  methods: {
    ...mapActions('mediaEditorProjects', [
      'openPlayVideoDialog',
      'openPlayVideoDialogId',
    ]),
    openPlayVideo() {
      this.openPlayVideoDialogId(this.item.project.id);
      this.openPlayVideoDialog();
    },
    formatData(dt) {
      return moment(dt).format('MM/DD/YYYY');
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';
  .fa-icon {
    fnt($text-light, 14px, $weight-light, left);
    transition : all .3s;
    cursor     : pointer;
    &:hover {
      clolor : $primary;
    }
    &--play {
      font-size: 22px;
      color: $white;
    }
    &--media {
      fnt($text-invert, 12px, $weight-light, center);
    }
  }

  .media-box {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    &__header {
      fl-between();
      height: 36px;
      flex: 0 0 auto;
      padding: 0 16px;
    }
    &__main {
      fl-center();
      background: center center/cover no-repeat $white-ter;
      display: flex;
      flex: 1 1 auto;
      position: relative;
    }
    &__play-btn {
      fl-center();
      border-radius: 50%;
      border: 3px solid $white;
      cursor: pointer;
      height: 62px;
      opacity: 0;
      padding-left: 4px;
      transition: all 0.3s;
      width: 62px;
    }
    &__main:hover &__play-btn {
      opacity: 1;
    }
    &__media-type-box {
      fl-left();
      flex-direction: column;

      position: absolute;
      right: 0;
      top: 0;
      padding: 4px 8px;
    }
    &__media-type-item {
    }
    &__footer {
      fl-between();
      height: 72px;
      flex: 0 0 auto;
      padding: 12px 16px;
    }
    &__left {
      fl-left();
      &--top {
        align-items: flex-start;
      }
    }
    &__right {
      fl-right();
      &--top {
        align-items: flex-start;
        height: 100%;
      }
    }
    &__title {
      fnt($text, 14px, $weight-normal, left);
      text-transform: capitalize;
    }
    &__views {
      fnt($text, 12px, $weight-normal, right);
      text-transform: capitalize;
    }
    &__avatar {
      fl-center();
      background: center center/cover no-repeat $grey-lighter;
      border-radius: 50%;
      flex: 0 0 auto;
      height: 32px;
      width: 32px;
    }
    &__name-box {
      padding-left: 8px;
      min-height: 48px;
    }
    &__name {
      fnt($text, 12px, $weight-normal, left);
    }
    &__sub-name {
      fnt($primary, 10px, $weight-normal, left);
    }
    &__action-box {
      display: flex;
      align-items: flex-start;
      justify-content: flex-end;

      height: 32px;
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
