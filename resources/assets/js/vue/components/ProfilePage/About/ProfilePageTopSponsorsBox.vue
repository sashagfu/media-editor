<template>
  <div class="ProfilePageSponsorsBox pp-sp-box box">
    <div class="pp-sp-box__header">
      <div class="pp-sp-box__left">
        <div class="pp-sp-box__header-title">
          {{ trans('profile_page.top_sponsors') }}
        </div>
      </div>
      <div class="pp-sp-box__right">
        <el-select
          v-model="value"
          placeholder="Select"
          size="mini"
        >
          <el-option
            v-for="item in options"
            :key="item.value"
            :label="item.label"
            :value="item.value"
          />
        </el-select>
        <!-- <el-dropdown
          v-if="isMine"
        >
          <span class="el-dropdown-link">
            <font-awesome-icon
              :icon="['fas', 'ellipsis-v']"
              class="fa-icon"
            />
          </span>
          <el-dropdown-menu slot="dropdown">
            <el-dropdown-item>
              Action 1
            </el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown> -->
      </div>
    </div>
    <div
      :class="[
        'pp-sp-box__main',
      ]"
    >
      <div
        v-if="$apollo.queries.fetchGlobalTopSponsors.loading"
        class="pp-sp-box__loading-spinner"
      >
        <font-awesome-icon
          :icon="['fas', 'spinner']"
          spin
          class="fa-icon fa-icon--spinner"
        />
      </div>
      <div
        v-else-if="!fetchGlobalTopSponsors.length"
        class="pp-sp-box__list-empty"
      >
        {{ trans('profile_page.list_empty') }}
      </div>
      <template v-else>
        <div
          class="pp-sp-box__1st-box">
          <div class="pp-sp-box__ava-box">
            <Avatar
              v-if="fetchGlobalTopSponsors[0].avatar.includes('/profile/default/avatar')"
              :username="fetchGlobalTopSponsors[0].displayName"
              :size="72"
              custom-class="pp-sp-box__1st-avatar"
            />
            <div
              v-else
              :style="{'background-image': `url(${fetchGlobalTopSponsors[0].avatar})`}"
              class="pp-sp-box__1st-avatar"
            />
            <div class="pp-sp-box__1st-badge">
              <font-awesome-icon
                :icon="['fas', 'trophy']"
                class="fa-icon fa-icon--trophy"
              />
            </div>
          </div>
          <div class="pp-sp-box__1st-title-box">
            <div class="pp-sp-box__1st-title">
              {{ fetchGlobalTopSponsors[0].displayName }}
            </div>
            <div class="pp-sp-box__1st-sub-title">
              @{{ fetchGlobalTopSponsors[0].username }}
            </div>
          </div>
        </div>
        <div class="pp-sp-box__row">
          <div
            v-for="(sponsor, index) in fetchGlobalTopSponsors"
            v-if="index !== 0 && index < 3"
            :key="index"
            class="pp-sp-box__item">
            <Avatar
              v-if="sponsor.avatar.includes('/profile/default/avatar')"
              :username="sponsor.displayName"
              :size="72"
              custom-class="pp-sp-box__avatar"
            />
            <div
              v-else
              :style="{'background-image': `url(${sponsor.avatar})`}"
              class="pp-sp-box__avatar"
            />
            <div class="pp-sp-box__title-box">
              <div class="pp-sp-box__title">
                {{ sponsor.displayName }}
              </div>
              <div class="pp-sp-box__sub-title">
                @{{ sponsor.username }}
              </div>
            </div>
          </div>
        </div>
      </template>
    </div>
    <div
      v-if="isMine"
      class="pp-sp-box__footer">
      <div
        class="pp-sp-box__btn"
        @click="showMore"
      >
        {{ trans('profile_page.show_more') }}
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import * as constants from 'Helpers/constants';
import FETCH_TOP_SPONSORS from 'Gql/users/queries/fetchGlobalTopSponsors.graphql';

import Avatar from 'Pages/ProfilePage/Avatar';

export default {
  name: 'ProfilePageTopSponsorsBox',
  components: {
    Avatar,
  },
  props: {
    user: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      constants,
      value: constants.LAST_MONTH,
      fetchGlobalTopSponsors: [],
      options: [{
        value: constants.ALL_TIME,
        label: this.trans('profile_page.all_time'),
      }, {
        value: constants.LAST_MONTH,
        label: this.trans('profile_page.last_month'),
      }, {
        value: constants.LAST_WEEK,
        label: this.trans('profile_page.last_week'),
      }],
    };
  },
  computed: {
    ...mapGetters('general', [
      'activeUser',
    ]),
    isMine() {
      return this.user.uuid === this.activeUser.uuid;
      // return false;
    },
  },
  methods: {
    showMore() {
      window.location = '/profile/donations#top-sponsors';
    },
  },
  apollo: {
    fetchGlobalTopSponsors: {
      query: FETCH_TOP_SPONSORS,
      variables() {
        return {
          amount: 3,
          period: this.value,
        };
      },
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
    fnt($text-light, 14px, $weight-light, left);
    transition : all .3s;
    &--star {
      color: $yellow;
    }
    &--trophy {
      color: $white;
      font-size: 10px;
    }
    &--spinner {
      color: $text-light;
      font-size: 26px;
    }
  }

  .pp-sp-box {
    &__header {
      fl-between();
      height        : 60px;
      padding       : 0 26px;
      width         : 100%;
    }
    &__left {
      fl-left();
    }
    &__right {
      fl-right();
    }
    &__header-title {
      fnt($text, 14px, $weight-semibold, left);
    }
    &__main {
      padding: 0px 26px;
      &:last-child {
        padding-bottom: 26px;
      }
    }
    &__list-empty {
      fnt($text-lighter, 14px, $weight-normal, center);
      padding-top: 26px;
    }
    &__loading-spinner {
      padding-top: 26px;
    }
    &__1st-box {
      fl-left();
      padding-bottom: 8px;
    }
    &__ava-box {
      position: relative;
    }
    &__1st-avatar {
      fl-center();
      background: center center/cover no-repeat $grey-lighter;
      border-radius: 50%;
      flex: 0 0 auto;
      height: 72px;
      width: 72px;
    }
    &__1st-badge {
      fl-center();
      background-color: $yellow;
      border-radius: 50%;
      border: 2px solid $white;
      bottom: -6px;
      height: 24px;
      position: absolute;
      right: -6px;
      width: 24px;
    }
    &__1st-title-box {
      padding-left: 12px;
    }
    &__1st-title {
      fnt($text-strong, 14px, $weight-normal, left);
    }
    &__1st-sub-title {
      fnt($text-light, 12px, $weight-normal, left);
    }
    &__footer {
      fl-center();
      height: 60px;
      padding: 0 26px;
    }
    &__btn {
      fnt($primary, 14px, $weight-normal, right);
      cursor: pointer;
      transition: all .3s;
      width: 100%;
      &:hover {
        opacity: 0.8;
      }
    }
    &__item {
      fl-left();
      padding: 8px 0;
    }
    &__avatar{
      fl-center();
      background: center center/cover no-repeat $grey-lighter;
      border-radius: 50%;
      flex: 0 0 auto;
      height: 72px;
      width: 72px;
    }
    &__title-box {
      padding-left: 12px;
    }
    &__title {
      fnt($text, 14px, $weight-normal, left);
    }
    &__sub-title {
      fnt($text-light, 12px, $weight-normal, left);
    }

  }
</style>

<style lang="stylus">
  @import '../../../../../sass/front/components/bulma-theme';

  .pp-sp-box {
    .el-dropdown {
      fl-right();
      margin-left: 4px;
      width: 16px;
    }
    .el-select {
      width: 120px;
    }
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
    .el-select:hover .el-input__inner {
      border-color: $border;
    }
  }
</style>
