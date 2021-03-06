<template>
  <div
    ref="share"
    class="PostShares blog-post-shares"
    @click.stop="toggleShareMenu()"
    @mouseover="getTopPosition"
  >
    <div class="blog-post-shares__icon">
      <font-awesome-icon
        :icon="['fas', 'share']"
        class="fa-icon fa-icon--pointer"
      />
    </div>
    <div
      :class="[
        'blog-post-shares__shares-diamond',
        `blog-post-shares__shares-diamond--${positions}`,
        {'blog-post-shares__shares-diamond--show':showShareMenu}
      ]"
    />
    <div
      :class="[
        'box',
        'blog-post-shares__dropdown-sharing',
        `blog-post-shares__dropdown-sharing--${positions}`,
        {'blog-post-shares__shares-diamond--show':showShareMenu}
      ]"
    >
      <div class="blog-post-shares__shares-item">
        <a
          class="blog-post-shares__shares-line post-container__email-share"
          @click="emailShare(post)"
        >
          <div
            class="blog-post-shares__shares-logo
                                        blog-post-shares__shares-logo--mail"
          >
            <font-awesome-icon
              icon="envelope"
              class="fa-icon fa-icon--sm fa-icon--pointer"
            />
          </div>
          <div
            class="blog-post-shares__shares-text
                                        blog-post-shares__shares-text--mail"
          >
            {{ trans('feed.share_by_email') }}
          </div>

        </a>
      </div>
      <div
        v-if="post.shareable"
        class="blog-post-shares__shares-item"
      >
        <a
          v-if="!post.shared"
          href="#"
          class="blog-post-shares__shares-line user-feed-share"
          @click.prevent="shareToUserFeed(post)"
        >
          <div
            class="blog-post-shares__shares-logo
                                        blog-post-shares__shares-logo--feed"
          >
            <font-awesome-icon
              icon="rss"
              class="fa-icon fa-icon--sm fa-icon--pointer"
            />
          </div>
          <div
            class="blog-post-shares__shares-text
                                        blog-post-shares__shares-text--feed"
          >
            {{ trans('feed.share_user_feed') }}
          </div>
        </a>

        <a
          v-if="post.shared"
          class="blog-post-shares__shares-line disabled"
        >
          <div
            class="blog-post-shares__shares-logo
                                        blog-post-shares__shares-logo--feed"
          >
            <font-awesome-icon
              icon="rss"
              class="fa-icon fa-icon--sm fa-icon--pointer"
            />
          </div>
          <div
            class="blog-post-shares__shares-text
                                        blog-post-shares__shares-text--feed"
          >
            {{ trans('feed.shared') }}
          </div>
        </a>
      </div>
      <div class="blog-post-shares__shares-item">
        <a
          href="#"
          class="blog-post-shares__shares-line chat-share"
          @click.prevent="openChatShareModal(post)"
        >
          <div
            class="blog-post-shares__shares-logo
                                        blog-post-shares__shares-logo--chat"
          >
            <font-awesome-icon
              icon="comments"
              class="fa-icon fa-icon--sm fa-icon--pointer"
            />
          </div>
          <div
            class="blog-post-shares__shares-text
                                        blog-post-shares__shares-text--chat"
          >
            {{ trans('feed.share_chat') }}
          </div>
        </a>
      </div>
      <div class="blog-post-shares__shares-item">
        <a
          href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fthemeforest.net%2Fitem%2Folympus-social-network-psd-template%2F18849736&amp;src=sdkpreparse"
          class="blog-post-shares__shares-line fc-share"
        >
          <div
            class="blog-post-shares__shares-logo
                                        blog-post-shares__shares-logo--facebook"
          >
            <font-awesome-icon
              :icon="['fab', 'facebook']"
              class="fa-icon fa-icon--sm fa-icon--pointer"
            />
          </div>
          <div
            class="blog-post-shares__shares-text
                                        blog-post-shares__shares-text--facebook"
          >
            {{ trans('feed.facebook') }}
          </div>
        </a>
      </div>
      <div class="blog-post-shares__shares-item">
        <a
          href="https://themeforest.net/item/olympus-social-network-psd-template/18849736"
          class="blog-post-shares__shares-line tw-share"
        >
          <div
            class="blog-post-shares__shares-logo
                                        blog-post-shares__shares-logo--twitter"
          >
            <font-awesome-icon
              :icon="['fab', 'twitter']"
              class="fa-icon fa-icon--sm fa-icon--pointer"
            />
          </div>
          <div
            class="blog-post-shares__shares-text
                                        blog-post-shares__shares-text--twitter"
          >
            {{ trans('feed.twitter') }}
          </div>
        </a>
      </div>
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex';

export default {
  name: 'PostShares',
  props: {
    post: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      showShareMenu: false,
      positions: '',
    };
  },
  mounted() {
    window.addEventListener('click', this.closeShareMenu, false);
  },
  destroyed() {
    window.removeEventListener('click', this.closeShareMenu, false);
  },
  methods: {
    ...mapActions('feed', [
      'setActivePost',
    ]),
    getTopPosition() {
      const { top } = this.$refs.share.getBoundingClientRect();
      if (top < 200) {
        this.positions = 'bottom';
      } else {
        this.positions = 'top';
      }
    },
    emailShare(post) {
      this.$set(post, 'emailShareVisibility', true);
      this.setActivePost(this.post);
    },
    openChatShareModal(post) {
      this.$set(post, 'chatShareVisibility', true);
      this.setActivePost(this.post);
    },
    shareToUserFeed(post) {
      this.$http.post('/api/post/share/user_feed', {
        post_id: post.id,
      }).then(() => {
        this.$set(post, 'shared', true);
      });
    },
    toggleShareMenu() {
      this.showShareMenu = !this.showShareMenu;
    },
    closeShareMenu() {
      this.showShareMenu = false;
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
    color      : $text-light;
    transition : all .3s;
    font-size  : 1rem;
    &:hover {
      color : $text-lighter;
    }
    &--invert {
      color : $white;
      &:hover {
        color : $white-bis;
      }
    }
    &--pointer {
      cursor : pointer;
    }
    &--liked {
      color : $secondary;
      &:hover {
        color : $coral-hover;
      }
    }
    &--starred {
      color : $primary;
      &:hover {
        color : $lime-hover;
      }
    }
    &--sm {
      font-size : 12px;
    }
    &__box {
      padding : 0 4px 0;
    }
  }

  .blog-post-shares {
    position     : relative;
    padding-left : 4px;
    height       : 100%;
    &__icon {
      cursor      : pointer;
      display     : flex;
      align-items : center;
      height      : 100%;
    }
    &__number {
      fnt($text-lighter, 1rem, $weight-normal, left);
      margin-left : 8px;
    }
    &__dropdown-sharing {
      display        : none;
      flex-direction : column;
      padding        : 12px 16px;
      position       : absolute;
      width          : 180px;
      z-index        : 5;
      +tablet() {
        left : -8px;
      }
      +desktop() {
        right : -8px;
      }
      +widescreen() {
        right : -8px;
      }
      &--top {
        bottom : 38px;
      }
      &--bottom {
        top : 28px;
      }
      &--show {
        display : flex;
      }
    }
    &:hover > &__dropdown-sharing, &:hover > &__shares-diamond {
      display : flex !important;
    }
    &__shares-item {
      fnt($text, 12px, $weight-semibold, left);
      cursor      : pointer;
      display     : flex;
      white-space : nowrap;
      &:hover {
        .blog-post-shares__shares-logo {
          &--mail {
            border-color : $green;
            & .fa-icon {
              color : $green;
            }
          }
          &--feed {
            border-color : $yellow;
            & .fa-icon {
              color : $yellow;
            }
          }
          &--chat {
            border-color : $turquoise;
            & .fa-icon {
              color : $turquoise;
            }
          }
          &--facebook {
            border-color : $facebook;
            & .fa-icon {
              color : $facebook;
            }
          }
          &--twitter {
            border-color : $twitter;
            & .fa-icon {
              color : $twitter;
            }
          }
        }
        .blog-post-shares__shares-text {
          &--mail {
            color : $green;
          }
          &--feed {
            color : $yellow;
          }
          &--chat {
            color : $turquoise;
          }
          &--facebook {
            color : $facebook;
          }
          &--twitter {
            color : $twitter;
          }
        }
      }
    }
    &__shares-item + &__shares-item {
      margin-top : 4px;
    }
    &__shares-line {
      display     : flex;
      align-items : center;
    }
    &__shares-logo {
      align-items     : center;
      border-radius   : 3px;
      border          : 2px solid $grey-light;
      display         : flex;
      height          : 22px;
      justify-content : center;
      transition      : all .3s;
      width           : 22px;
      & .fa-icon {
        transition : all .3s;
        color      : $grey-light;
      }
    }
    &__shares-text {
      fnt($grey-light, 12px, $weight-normal, left);
      margin-left : 8px;
      transition  : all .3s;
    }

    &__shares-diamond {
      display          : none;
      background-color : $background-light;
      height           : 8px;
      position         : absolute;
      right            : 7px;
      transform        : rotate(225deg);
      width            : 8px;
      z-index          : 6;
      &--top {
        bottom      : 34px;
        border-top  : 1px solid $border;
        border-left : 1px solid $border;
      }
      &--bottom {
        top           : 24px;
        border-bottom : 1px solid $border;
        border-right  : 1px solid $border;
      }
      &--show {
        display : flex;
      }
    }
  }

</style>
