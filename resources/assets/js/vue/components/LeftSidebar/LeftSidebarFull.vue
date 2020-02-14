<template>
  <div class="LeftSidebarFull left-sidebar">
    <div class="left-sidebar__item left-sidebar-main">
      <div class="left-sidebar__logo"/>
      <div
        class="sidebar-box sidebar-box--first"
        @click="toggleComponents"
      >
        <div class="sidebar-box__pic main-pic">
          <font-awesome-icon
            :icon="['fas', 'times']"
            class="fa-icon"
          />
        </div>
        <div class="sidebar-box__txt">
          <div class="sidebar-box__diamond"/>
          {{ trans('sidebar.collapse_menu') }}
        </div>
        <div class="sidebar-box__wide-txt">
          {{ trans('sidebar.collapse_menu') }}
        </div>
      </div>
      <div class="sidebar-box">
        <a @click="goToPage('FeedPage', '/feed')">
          <div class="sidebar-box__pic main-pic">
            <font-awesome-icon
              :icon="['fas', 'home']"
              class="fa-icon"
            />
          </div>
          <div class="sidebar-box__txt">
            <div class="sidebar-box__diamond"/>
            {{ trans('sidebar.home_page') }}
          </div>
          <div class="sidebar-box__wide-txt">
            {{ trans('sidebar.home_page') }}
          </div>
        </a>
      </div>
      <div class="sidebar-box">
        <a
          @click="goToPage(
            'ProfilePage',
            `/profile/${activeUser.username}`,
            {username: activeUser.username})"
        >
          <div class="sidebar-box__pic main-pic">
            <font-awesome-icon
              :icon="['fas', 'user']"
              class="fa-icon"
            />
          </div>
          <div class="sidebar-box__txt">
            <div class="sidebar-box__diamond"/>
            {{ trans('sidebar.profile_page') }}
          </div>
          <div class="sidebar-box__wide-txt">
            {{ trans('sidebar.profile_page') }}
          </div>
        </a>
      </div>
      <div class="sidebar-box">
        <a @click="goToPage('NetworkingPage', '/network')">
          <div class="sidebar-box__pic main-pic">
            <font-awesome-icon
              :icon="['fas', 'users']"
              class="fa-icon"
            />
          </div>
          <div class="sidebar-box__txt">
            <div class="sidebar-box__diamond"/>
            {{ trans('sidebar.my_network') }}
          </div>
          <div class="sidebar-box__wide-txt">
            {{ trans('sidebar.my_network') }}
          </div>
        </a>
      </div>
      <!-- <div class="sidebar-box">
          <div class="sidebar-box__pic sidebar-box__pic--news main-pic"/>
          <div class="sidebar-box__txt">
              <div class="sidebar-box__diamond"/>
              {{ trans('sidebar.newsfeed') }}
          </div>
          <div class="sidebar-box__wide-txt">
              {{ trans('sidebar.newsfeed') }}
          </div>
      </div> -->
      <!-- <div class="sidebar-box">
        <a href="/performances">
          <div class="sidebar-box__pic main-pic">
            <font-awesome-icon
              :icon="['fas', 'star']"
              class="fa-icon"
            />
          </div>
          <div class="sidebar-box__txt">
            <div class="sidebar-box__diamond"/>
            {{ trans('sidebar.pages_feed') }}
          </div>
          <div class="sidebar-box__wide-txt">
            {{ trans('sidebar.pages_feed') }}
          </div>
        </a>
      </div> -->
      <!-- <div class="sidebar-box">
        <a href="/feed">
          <div class="sidebar-box__pic main-pic">
            <font-awesome-icon
              :icon="['fas', 'users']"
              class="fa-icon"
            />
          </div>
          <div class="sidebar-box__txt">
            <div class="sidebar-box__diamond"/>
            {{ trans('sidebar.friend_groups') }}
          </div>
          <div class="sidebar-box__wide-txt">
            {{ trans('sidebar.friend_groups') }}
          </div>
        </a>
      </div> -->
      <!-- <div class="sidebar-box">
          <div class="sidebar-box__pic sidebar-box__pic--music main-pic"/>
          <div class="sidebar-box__txt">
              <div class="sidebar-box__diamond"/>
              {{ trans('sidebar.music_playlists') }}
          </div>
          <div class="sidebar-box__wide-txt">
              {{ trans('sidebar.music_playlists') }}
          </div>
      </div> -->
      <!-- <div class="sidebar-box">
          <div class="sidebar-box__pic sidebar-box__pic--calendar main-pic"/>
          <div class="sidebar-box__txt">
              <div class="sidebar-box__diamond"/>
              {{ trans('sidebar.calendar_events') }}
          </div>
          <div class="sidebar-box__wide-txt">
              {{ trans('sidebar.calendar_events') }}
          </div>
      </div> -->
      <!-- <div class="sidebar-box">
        <a href="/circles">
          <div class="sidebar-box__pic main-pic">
            <font-awesome-icon
              :icon="['fas', 'bullseye']"
              class="fa-icon"
            />
          </div>
          <div class="sidebar-box__txt">
            <div class="sidebar-box__diamond"/>
            {{ trans('sidebar.circles') }}
          </div>
          <div class="sidebar-box__wide-txt">
            {{ trans('sidebar.circles') }}
          </div>
        </a>
      </div> -->
      <!-- <div class="sidebar-box">
        <a href="/chat">
          <div class="sidebar-box__pic main-pic">
            <font-awesome-icon
              :icon="['fas', 'comments']"
              class="fa-icon"
            />
          </div>
          <div class="sidebar-box__txt">
            <div class="sidebar-box__diamond"/>
            {{ trans('sidebar.chat') }}
          </div>
          <div class="sidebar-box__wide-txt">
            {{ trans('sidebar.chat') }}
          </div>
        </a>
      </div> -->
      <!-- <div class="sidebar-box">
        <a href="/profile#projects">
          <div class="sidebar-box__pic main-pic">
            <font-awesome-icon
              :icon="['fas', 'video']"
              class="fa-icon"
            />
          </div>
          <div class="sidebar-box__txt">
            <div class="sidebar-box__diamond"/>
            {{ trans('sidebar.projects') }}
          </div>
          <div class="sidebar-box__wide-txt">
            {{ trans('sidebar.projects') }}
          </div>
        </a>
      </div> -->
      <!-- <div class="sidebar-box">
        <a href="/media-editor-projects">
          <div class="sidebar-box__pic main-pic">
            <font-awesome-icon
              :icon="['fas', 'film']"
              class="fa-icon"
            />
          </div>
          <div class="sidebar-box__txt">
            <div class="sidebar-box__diamond"/>
            {{ trans('sidebar.media_editor') }}
          </div>
          <div class="sidebar-box__wide-txt">
            {{ trans('sidebar.media_editor') }}
          </div>
        </a>
      </div> -->
      <!-- <div class="sidebar-box">
          <div class="sidebar-box__pic sidebar-box__pic--birthday main-pic"/>
          <div class="sidebar-box__txt">
              <div class="sidebar-box__diamond"/>
              {{ trans('sidebar.friends_birthdays') }}
          </div>
          <div class="sidebar-box__wide-txt">
              {{ trans('sidebar.friends_birthdays') }}
          </div>
      </div> -->
      <!-- <div class="sidebar-box">
          <div class="sidebar-box__pic sidebar-box__pic--stat main-pic"/>
          <div class="sidebar-box__txt">
              <div class="sidebar-box__diamond"/>
              {{ trans('sidebar.account_stats') }}
          </div>
          <div class="sidebar-box__wide-txt">
              {{ trans('sidebar.account_stats') }}
          </div>
      </div> -->
      <!-- <div class="sidebar-box">
          <div class="sidebar-box__pic sidebar-box__pic--mng main-pic"/>
          <div class="sidebar-box__txt">
              <div class="sidebar-box__diamond"/>
              {{ trans('sidebar.manage_widgets') }}
          </div>
          <div class="sidebar-box__wide-txt">
              {{ trans('sidebar.manage_widgets') }}
          </div>
      </div> -->
    </div>
    <div class="left-sidebar__item hero-foot"/>
  </div>

</template>

<script>
import { mapGetters } from 'vuex';

export default {
  name: 'LeftSidebarFull',
  computed: {
    ...mapGetters('general', [
      'activeUser',
    ]),
  },
  methods: {
    toggleComponents() {
      this.$emit('toggle-components');
    },
    goToPage(name, path, params = {}) {
      this.$router.push({
        name,
        params,
      });
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
    color $text-light
    font-size 20px
    transition all .2s
    &:hover {
      color $text-lighter
    }
    &--invert {
      color $white
      &:hover {
        color $white-bis
      }
    }
    &--pointer {
      cursor pointer
    }
    &--disabled {
      color $grey-lighter
      cursor auto
      &:hover {
        color $grey-lighter
      }
    }
    &__box {
      padding 0 4px 0
    }
  }

  .left-sidebar {
    background-color $white
    border-right 1px solid $border
    display flex
    flex-direction column
    flex-grow 1
    justify-content space-between
    position relative
    &__logo {
      background center center/82% no-repeat $white
      background-image url('../../../../img/logo/actionlime-logo--full.png')
      height 73px
      width 100%
    }
  }

  .sidebar-box {
    fl-left()
    cursor pointer
    height 52px
    position relative
    & a {
      fl-left()
      height 100%
      width 100%
    }
    &:hover &__txt {
      +tablet() {
        display flex
      }
      +desktop() {
        display flex
      }
    }
    &:hover &__txt,
    &:hover &__wide-txt,
    &:hover .fa-icon {
      color $text-lighter
    }

    &--first {
      height 78px
      padding-top 26px
    }
    &__diamond {
      deg45();
      background-color $white
      border 1px solid $border
      border-right none
      border-top  none
      height 8px
      left -4px
      position absolute
      width 8px
    }

    &__txt {
      fnt($text-light, 14px, $weight-normal, left)
      align-items center
      background-color $white
      border 1px solid $border
      border-radius 3px
      display none
      left 56px
      padding 4px 8px
      position absolute
      text-transform capitalize
      transition all .2s
      white-space nowrap
      z-index 1

    }
    &__wide-txt {
      fnt($text-light, 14px, $weight-normal, left)
      align-items center
      background-color $white
      cursor pointer
      text-transform capitalize
      transition all .2s
      white-space nowrap
      width 205px
      tablet() {
        display none
      }
      desktop() {
        display none
      }
    }
    &__pic {
      fl-center()
      background center center no-repeat
      height 100%
      transition all .3s
      width 72px
    }
  }
</style>
