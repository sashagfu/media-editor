<template>
  <div class="favorite-page-main">
    <div class="top-header">
      <div class="top-header__box box">
        <div
          :style="{backgroundImage: `url(${circle.lastCover})`}"
          class="top-header__head-picture"
        />
        <div class="top-header__top-header-menu">
          <div class="top-header__title-box">
            <div class="top-header__name-box">
              <div class="top-header__title">
                {{ circle.title }}
              </div>
              <div class="top-header__sub-title">
                {{ trans('circles.type') }}: {{ circle.type }}
              </div>
              <div
                v-if="isRequestioning"
                class="top-header__sub-title"
              >
                {{ trans('circles.request-pending') }}
              </div>
            </div>
          </div>
          <div class="top-header__menu-box columns is-marginless">
            <div
              class="top-header__menu-item column"
              @click="componentBox = 'Timeline'"
            >
              Timeline
            </div>
            <div
              v-if="canUpdateSettings"
              class="top-header__menu-item column"
              @click="componentBox = 'CircleSettings'"
            >
              Settings
            </div>
            <div
              v-if="canSeeMembers"
              class="top-header__menu-item column"
              @click="componentBox = 'CircleMembers'"
            >
              Members
            </div>
            <div
              v-if="canUpdateSettings && circle.type === 'closed'"
              class="top-header__menu-item column"
              @click="componentBox = 'Requests'"
            >
              Requests
            </div>
          </div>
          <div class="top-header__button-box">
            <div
              v-if="canJoin"
              :class="{'top-header__button--loading': showLoading}"
              class="top-header__button top-header__button--request"
              @click="handleMembership()">
              <i
                v-if="showLoading"
                class="fa fa-spinner fa-spin fa-fw
                                      top-header__button--loading-spinner"
              />
              <span v-else>
                +
              </span>
            </div>
            <div
              v-if="isRequestioning"
              :class="{'top-header__button--loading': showLoading}"
              class="top-header__button top-header__button--request pending"
              @click="cancelSelfRequest()"
            >
              <i
                v-if="showLoading"
                class="fa fa-spinner fa-spin fa-fw
                                      top-header__button--loading-spinner"
              />
            </div>
            <div
              v-if="isMember"
              :class="{'top-header__button--loading': showLoading}"
              class="top-header__button top-header__button--request cancel"
              @click="handleMembership()">
              <i
                v-if="showLoading"
                class="fa fa-spinner fa-spin fa-fw
                                      top-header__button--loading-spinner"
              />
              <span v-else>
                -
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <component
      :is="componentBox"
      :user="user"
      :feed-items="feedItems"
      :can-see-members="canSeeMembers"
      :members="members"
      :circle="circle"
      :requests="requests"
      :form-type="formType"
      :show-form="showForm"
    />
  </div>
</template>

<script>
/* @flow */
import Timeline from './Timeline';
import CircleSettings from './CircleSettings';
import CircleMembers from './CircleMembersComponents/CircleMembers';
import Requests from './CircleMembersComponents/Requests';

export default {
  components: {
    Timeline,
    CircleSettings,
    CircleMembers,
    Requests,
  },
  props: {
    circle: {
      type: Object,
      default: () => ({}),
    },
    feedItems: {
      type: Object,
      default: () => ({}),
    },
    user: {
      type: Object,
      default: () => ({}),
    },
    members: {
      type: Array,
      default: () => [],
    },
    requests: {
      type: Array,
      default: () => [],
    },
    canUpdateSettings: {
      type: Boolean,
      default: false,
    },
    canSeeMembers: {
      type: Boolean,
      default: false,
    },
    canJoin: {
      type: Boolean,
      default: false,
    },
    isRequestioning: {
      type: Boolean,
      default: false,
    },
    isMember: {
      type: Boolean,
      default: false,
    },
    showForm: {
      type: Boolean,
      default: false,
    },
    formType: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      componentBox: 'Timeline',
      showLoading: false,
    };
  },
  methods: {
    handleMembership() {
      this.showLoading = !this.showLoading;
      this.$http.patch('/api/circle/handle_membership', {
        slug: this.circle.slug,
      })
        .then(() => {
          window.location.reload();
        });
    },
    cancelSelfRequest() {
      this.showLoading = !this.showLoading;
      this.$http.patch('/api/circle/cancel_self_request', {
        slug: this.circle.slug,
      })
        .then(() => {
          window.location.reload();
        });
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
    @import '../../../../../sass/front/components/bulma-theme';

    .top-header {
        padding: 40px 32px 0 32px;
        width: 100%;
        &__head-picture {
            width: 100%;
            height: 328px;
            background: center center/cover no-repeat;
            border-radius: 3px 3px 0 0;
            border-bottom: 1px solid $border;
        }
        &__top-header-menu {
            height: 72px;
            width: 100%;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        &__title-box {
            position: absolute;
            top: -88px;
            left: 68px;
            display: flex;
        }
        &__name-box {
            padding: 15px 0 0 32px;
        }
        &__title {
            fnt($text-invert, 22px, $weight-bold, left);
        }
        &__sub-title {
            fnt($text-invert, 12px, $weight-normal, left);
        }
        &__menu-box {
            height: 100%;
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 65%;
            z-index: 10;
        }
        &__menu-item {
            fnt($text-lighter, 16px, $weight-semibold, center);
            cursor: pointer;
            flex: 1 1 auto;
            &:hover {
                color: $grey;
            }
        }
        &__button-box {
            display: flex;
            position: absolute;
            right: 72px;
            top: -32px;
        }
        &__button {
            cursor: pointer;
            height: 48px;
            width: 48px;
            border-radius: 50%;
            margin-left: 22px;
            &--loading-spinner {
                font-size: 25px;
                margin-top: 10px;
                margin-left: 9px;
            }
            &--send-message {
                background: url('../../../../../img/chat-messages-icon--white.png')
                    center center no-repeat $accented;
            }
            &--add-fav-page {
                background: url('../../../../../img/star-icon--white.png')
                    center center no-repeat $primary;
            }
            &--loading {
                background-image: none !important;
            }
            &--request {
                text-align: end;
                font-size: 12px;
                font-weight: 900;
                color: $white;
                padding-right: 12px;
                background: url('../../../../../img/happyface-icon--white.png')
                    center center no-repeat $info;
            }
            &--request.cancel {
                background-color: $red;
            }
            &--request.pending {
                background-color: $grey-dark;
            }
        }
    }
</style>
