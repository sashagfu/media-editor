<template>
  <div class="HomeFeedItemMini hf-item box is-marginless">
    <div
      :style="{
        'background-image': (showingPreview)
          ? `url(${item.spritePath})`
          : `url(${item.thumbPath})`,
        'background-position-y': (showingPreview) ? previewBackgroundPosition : 'center',
        'background-size': (showingPreview) ? '480px' : '',
      }"
      class="hf-item__main"
      @click="goToProject"
      @mouseover="showPreview"
      @mouseleave="stopPreview"
    >
      <font-awesome-icon
        v-if="previewLoading"
        :icon="['fas', 'spinner']"
        spin
        class="fa-icon"
      />
      <div class="hf-item__views hf-item__views--pos">
        {{ item.viewsCount }} {{ trans('feed.views') }}
      </div>
      <div
        class="hf-item__play-btn"
      >
        <font-awesome-icon
          :icon="['fas', 'play']"
          class="fa-icon fa-icon--play"
        />
      </div>
      <div class="hf-item__durations">
        {{ (asset) ? duration : '00:00' }}
      </div>
    </div>
    <div
      v-if="withFooter"
      class="hf-item__footer">
      <div class="hf-item__left">
        <div
          class="hf-item__title"
          @click="goToProject"
        >
          {{ item.title }}
        </div>
        <div class="hf-item__user-box">
          <div
            v-if="usersData"
            :style="{'background-image': `url(${item.author.avatar})`}"
            class="hf-item__avatar"
          />
          <div class="hf-item__name-box">
            <div
              v-if="usersData"
              class="hf-item__name"
            >
              {{ item.author.displayName }}
            </div>
            <div
              v-else
              class="hf-item__title"
            >
              {{ item.title }}
            </div>
            <div class="hf-item__sub-name">
              <template v-if="usersData">
                {{ trans('home_projects.on_actionlime') }}
                {{ formatData(item.author.createdAt) }}
              </template>
              <template v-else>
                {{ item.views }} views
              </template>
            </div>
          </div>
        </div>
      </div>
      <div class="hf-item__right hf-item__right--bottom">
        <div class="hf-item__action-box">
          <div class="hf-item__action-item">
            <font-awesome-icon
              :icon="['far', 'comments']"
              class="fa-icon "
            />
            <div class="hf-item__action-qty">
              {{ (item.comments) ? item.comments.length : item.comment }}
            </div>
          </div>
          <div class="hf-item__action-item">
            <font-awesome-icon
              :icon="['far', 'star']"
              class="fa-icon "
            />
            <div class="hf-item__action-qty">
              {{ (item.stars) ? item.stars.length : item.starred }}
            </div>
          </div>
          <div class="hf-item__action-item">
            <font-awesome-icon
              :icon="['fas', 'paperclip']"
              class="fa-icon "
            />
            <div class="hf-item__action-qty">
              {{ item.clipsCount }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment';
import * as constants from 'Helpers/constants';

export default {
  name: 'HomeFeedItem',
  props: {
    item: {
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
      showingPreview: false,
      previewBackgroundPosition: '0px',
      previewBackgroundHeight: 0,
      previewLoading: false,
      timer: 0,
      mouseOver: false,
    };
  },
  computed: {
    asset() {
      return this.item.assets.find(a => a.type === constants.FULL);
    },
    duration() {
      if (moment.duration(this.asset.time).asHours() > 1) {
        return moment.utc(moment.duration(this.asset.time).asMilliseconds()).format('HH:mm:ss');
      }
      return moment.utc(moment.duration(this.asset.time).asMilliseconds()).format('mm:ss');
    },
  },
  methods: {
    async showPreview() {
      this.mouseOver = true;
      // Do not start preview again, if preview still running
      if (this.showingPreview) return;

      // Start preview loading
      this.previewLoading = true;
      await this.loadSprite()
        .then((img) => {
          this.previewBackgroundHeight = img.height;
          this.previewLoading = false;

          // If mouse still over the box
          if (this.mouseOver) {
            this.showingPreview = true;
          }
        })
        .catch(() => {
          this.showingPreview = false;
          this.previewLoading = false;
        });

      // Break showing preview if image are not loaded
      if (!this.showingPreview || !this.mouseOver) return;

      this.previewBackgroundPosition = 0;
      // Start position -50px
      let position = 50;
      this.timer = setInterval(() => {
        if (position < this.previewBackgroundHeight) {
          this.previewBackgroundPosition = `-${position}px`;
          position += 270;
        } else {
          position = 50;
        }
      }, 120);
      this.previewBackgroundPosition = 0;
    },
    stopPreview() {
      this.showingPreview = false;
      this.previewLoading = false;
      this.mouseOver = false;
      clearInterval(this.timer);
    },
    async loadSprite() {
      return new Promise((resolve, reject) => {
        const img = new Image();
        img.src = this.item.spritePath;
        img.onload = () => {
          resolve(img);
        };

        img.onerror = () => {
          reject();
        };
      });
    },
    goToProject() {
      this.$router.push({
        name: 'SingleProjectPage',
        params: {
          projectUuid: this.item.uuid,
        },
      });
    },
    formatData(dt) {
      return moment(dt).format('MMMM YYYY');
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
    fnt($text-light, 12px, $weight-light, left)
    transition all .3s
    cursor pointer
    &:hover {
      clolor $primary
    }
    &--play {
      font-size 22px
      color $white
    }
  }

  .fa-spinner
    position absolute
    bottom 10px
    left 10px
    color white
    opacity 0.6

  .hf-item {
    display flex
    flex-direction column
    height 100%
    overflow hidden
    width 100%
    &__header {
      fl-between()
      flex 0 0 auto
      height 36px
      padding 0 16px
    }
    &__main {
      fl-center()
      background center center/cover no-repeat $white-ter
      display flex
      flex 1 1 auto
      position relative
      cursor pointer
    }
    &__play-btn {
      fl-center();
      border 3px solid $white
      border-radius 50%
      cursor pointer
      flex 0 0 auto
      height 62px
      opacity 0
      padding-left 4px
      transition all .3s
      width 62px
    }
    &:hover &__play-btn {
      opacity 1
    }
    &__durations {
      fnt($text-invert, 10px, $weight-semibold, center)
      background-color rgba($black-bis, 0.5)
      bottom 4px
      line-height 1
      padding 2px 4px
      position absolute
      right 8px
    }
    &__footer {
      fl-between()
      flex 0 0 auto
      height 72px
      padding 6px 16px 12px
    }
    &__left {
      align-items flex-start
      display flex
      flex-direction column
      height 100%
      justify-content space-between
    }
    &__right {
      fl-right()
      &--top {
        align-items flex-start
        height 100%
      }
      &--bottom {
        align-items flex-start
        height 100%
      }
    }
    &__user-box {
      fl-left()
      &--top {
        align-items flex-start
      }
      &--bottom {
        align-items flex-end
      }
    }
    &__title {
      fnt($text, 14px, $weight-normal, left)
      line-height 1
      text-transform capitalize
      cursor pointer
    }
    &__views {
      fnt($text, 10px, $weight-normal, right)
      text-transform capitalize
      &--pos {
        background-color rgba($black-bis, 0.5)
        color $text-invert
        line-height: 1
        padding 2px 4px
        position absolute
        right 8px
        top 8px
      }
    }
    &__avatar {
      background center center/cover no-repeat $grey-lighter
      border-radius 50%
      display flex
      flex 0 0 auto
      height 32px
      width 32px
    }
    &__name-box {
      padding-left 8px
    }
    &__name {
      fnt($text, 12px, $weight-normal, left)
      line-height 1
    }
    &__sub-name {
      fnt($primary, 10px, $weight-normal, left)
      line-height 1
    }
    &__action-box {
      display flex
      flex-direction column
      height 100%
    }
    &__action-item {
      fl-between()
    }
    &__action-item + &__action-item {
      margin-top 4px
    }
    &__action-qty {
      fnt($text-light, 10px, $weight-normal, left);
      padding-left 2px
    }
  }
</style>
