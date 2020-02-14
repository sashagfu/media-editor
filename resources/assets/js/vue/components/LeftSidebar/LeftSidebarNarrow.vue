<template>
  <div class="LeftSidebarNarrow left-sidebar">
    <div class="left-sidebar__item left-sidebar-main">
      <div class="left-sidebar__logo"/>
      <div
        class="sidebar-box sidebar-box--first"
        @click="toggleComponents"
      >
        <div class="sidebar-box__pic main-pic">
          <font-awesome-icon
            :icon="['fas', 'bars']"
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
    </div>
    <div class="left-sidebar__item"/>
  </div>

</template>

<script>
import { mapGetters } from 'vuex';

export default {
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
    color      : $text-light;
    font-size  : 20px;
    transition : all .2s;
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
    &--disabled {
      color  : $grey-lighter;
      cursor : auto;
      &:hover {
        color : $grey-lighter;
      }
    }
    &__box {
      padding : 0 4px 0;
    }
  }

  .left-sidebar {
    background-color : $white;
    border-right     : 1px solid $border;
    display          : flex;
    flex-direction   : column;
    flex-grow        : 1;
    justify-content  : space-between;
    position         : relative;
    &__logo {
      background center center/70% no-repeat $white;
      background-image url('../../../../img/logo/actionime-logo.png')
      height 73px
      width 100%
    }
  }

  .sidebar-box {
    fl-left();
    cursor   : pointer;
    height   : 52px;
    position : relative;
    & a {
      fl-left();
      height : 100%;
      width  : 100%;
    }
    &:hover &__txt {
      display : flex;
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
      deg45()
      background-color : $white;
      border           : 1px solid $border;
      border-right     : none;
      border-top       : none;
      height           : 8px;
      left             : -4px;
      position         : absolute;
      width            : 8px;
    }

    &__txt {
      fnt($text-light, 14px, $weight-normal, left);
      align-items      : center;
      background-color : $white;
      border           : 1px solid $border;
      border-radius    : 3px;
      display          : none;
      left             : 56px;
      padding          : 4px 8px;
      position         : absolute;
      text-transform   : capitalize;
      transition       : all .2s;
      white-space      : nowrap;
      z-index          : 1;

    }
    &__wide-txt {
      fnt($text-light, 14px, $weight-normal, left);
      align-items      : center;
      background-color : $white;
      cursor           : pointer;
      display          : none;
      text-transform   : capitalize;
      transition       : all .2s;
      white-space      : nowrap;
      width            : 205px;
    }
    &__pic {
      fl-center();
      background : center center no-repeat;
      height     : 100%;
      transition : all .3s;
      width      : 72px;

      /* &--news {
        background-image : url('../../../../img/newsfeed-icon--light.png');
        &:hover {
          background-image : url('../../../../img/newsfeed-icon--dark.png');
        }
      }
      &--music {
        background-image : url('../../../../img/headphones-icon--light.png');
        &:hover {
          background-image : url('../../../../img/headphones-icon--dark.png');
        }
      }
      &--calendar {
        background-image : url('../../../../img/month-calendar-icon--light.png');
        &:hover {
          background-image : url('../../../../img/month-calendar-icon--dark.png');
        }
      }
      &--stat {
        background-image : url('../../../../img/stats-icon--light.png');
        &:hover {
          background-image : url('../../../../img/stats-icon--dark.png');
        }
      }
      &--birthday {
        background-image : url('../../../../img/cupcake-icon--light.png');
        &:hover {
          background-image : url('../../../../img/cupcake-icon--dark.png');
        }
      }
      &--mng {
        background-image : url('../../../../img/manage-widgets-icon--light.png');
        &:hover {
          background-image : url('../../../../img/manage-widgets-icon--dark.png');
        }
      } */
    }
  }
</style>
