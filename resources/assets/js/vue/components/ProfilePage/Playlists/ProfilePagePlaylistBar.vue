<template>
  <div class="ProfilePagePlaylistBar pl-bar box">
    <div class="pl-bar__left">
      <div class="pl-bar__name">
        {{ playlist.name }}
      </div>
      <div class="pl-bar__author-box author">
        <div
          :class="['author__ava']"
          :style="{'background-image': `url(${playlist.author.avatar})`}"
        />
        <div class="author__title-box">
          <div class="author__title">
            {{ playlist.author.displayName }}
          </div>
          <div class="author__sub-title">
            on actionlime from {{ author.dataRegistration }}
          </div>
        </div>
      </div>

    </div>
    <div class="pl-bar__right">
      <div
        class="pl-bar__likes"
        title="star this playlist"
        @click="onLike"
      >
        <font-awesome-icon
          v-if="likeLoading"
          :icon="['fas', 'spinner']"
          spin
          class="fa-icon"
        />
        <font-awesome-icon
          v-else
          :icon="['far', 'star']"
          :class="{
            'fa-icon--starred': liked,
          }"
          class="fa-icon fa-icon--pointer"
        />
        <div class="pl-bar__likes-number">
          {{ likes.length }}
        </div>
      </div>

      <div class="pl-bar__ava-box ava-box">
        <el-tooltip
          v-for="(user, index) in likes"
          v-if="index < 3"
          :key="index"
          :content="user.displayName"
          placement="top"
        >
          <div
            :style="{'background-image': `url(${user.avatar})`}"
            class="ava-box__avatar"/>
        </el-tooltip>
      </div>
      <div class="pl-bar__add-box">
        <el-button
          v-if="added"
          type="primary"
          size="small"
          plain
          class="pl-bar__add-btn"
          title="save this playlist"
          @click="onAdd"
        >
          <font-awesome-icon
            v-if="addLoading"
            :icon="['fas', 'spinner']"
            spin
            class="fa-icon fa-icon--add"
          />
          <font-awesome-icon
            v-else
            :icon="['fas', 'plus']"
            class="fa-icon fa-icon--add"
          />
        </el-button>
        <el-button
          v-else
          size="small"
          plain
          class="pl-bar__add-btn pl-bar__add-btn--times"
          title="delete this playlist"
          @click="onAdd"
        >
          <font-awesome-icon
            v-if="addLoading"
            :icon="['fas', 'spinner']"
            spin
            class="fa-icon fa-icon--add"
          />
          <font-awesome-icon
            v-else
            :icon="['fas', 'times']"
            class="fa-icon fa-icon--times"
          />
        </el-button>
      </div>
      <el-button
        v-if="showAll"
        type="primary"
        size="small"
        plain
        class="pl-bar__show-btn"
        @click="onShowAll"
      >
        {{ trans('home_projects.show_all') }}
      </el-button>
      <el-button
        v-else
        size="small"
        plain
        class="pl-bar__show-btn"
        @click="onShowAll"
      >
        {{ trans('home_projects.back') }}
      </el-button>
    </div>

  </div>
</template>

<script>
export default {
  name: 'ProfilePagePlaylistBar',
  props: {
    playlist: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      liked: false,
      likeLoading: false,
      added: false,
      addLoading: false,
      showAll: true,
      author: {
        id: 1,
        displayName: 'Magdalene Jonah',
        avatar: 'http://i.pravatar.cc/100?img=43',
        dataRegistration: '02/03/2017',
      },
      likes: [
        {
          id: 1,
          displayName: 'Mimi Dearl',
          email: 'mdearl0@example.com',
          avatar: 'http://i.pravatar.cc/100?img=44',
        },
        {
          id: 2,
          displayName: 'Annadiana Barnshaw',
          email: 'abarnshaw1@xinhuanet.com',
          avatar: 'http://i.pravatar.cc/100?img=45',
        },
        {
          id: 3,
          displayName: 'Xylia Figgs',
          email: 'xfiggs2@wordpress.org',
          avatar: 'http://i.pravatar.cc/100?img=46',
        },
        {
          id: 4,
          displayName: 'Tim Lynnitt',
          email: 'tlynnitt3@disqus.com',
          avatar: 'http://i.pravatar.cc/100?img=47',
        },
        {
          id: 5,
          displayName: 'Clarey Kilbey',
          email: 'ckilbey4@aol.com',
          avatar: 'http://i.pravatar.cc/100?img=48',
        },
        {
          id: 6,
          displayName: 'Arlee Scuffham',
          email: 'ascuffham5@skyrock.com',
          avatar: 'http://i.pravatar.cc/100?img=49',
        },
        {
          id: 7,
          displayName: 'Starlene Tidcombe',
          email: 'stidcombe6@mediafire.com',
          avatar: 'http://i.pravatar.cc/100?img=50',
        },
        {
          id: 8,
          displayName: 'Ynes Steeden',
          email: 'ysteeden7@ebay.co.uk',
          avatar: 'http://i.pravatar.cc/100?img=12',
        },
        {
          id: 9,
          displayName: 'Daffy Rangle',
          email: 'drangle8@kickstarter.com',
          avatar: 'http://i.pravatar.cc/100?img=39',
        },
        {
          id: 10,
          displayName: 'Muhammad Newland',
          email: 'mnewland9@cbsnews.com',
          avatar: 'http://i.pravatar.cc/100?img=40',
        },
      ],
    };
  },
  methods: {
    onLike() {
      this.likeLoading = true;
      setTimeout(() => {
        this.liked = !this.liked;
        this.likeLoading = false;
      }, 1000);
    },
    onAdd() {
      this.addLoading = true;
      setTimeout(() => {
        this.added = !this.added;
        this.addLoading = false;
      }, 1000);
    },
    onShowAll() {
      this.showAll = !this.showAll;
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
    &--add {
      color : $primary;
      font-size  : 16px;
    }
    &__box {
      padding : 0 4px 0;
    }
  }

  .pl-bar {
    fl-between();
    height: 56px;
    margin-bottom: 24px;
    padding: 0 24px;
    &__left {
      fl-left();
    }
    &__right {
      fl-right();
    }
    &__name {
      fnt($text, 14px, $weight-semibold, left);
      text-transform: capitalize;
      width: 200px;
    }
    &__add-box {
      fl-center();
      margin-right: 12px;
      width: 40px;
    }
    &__add-btn {
      padding: 9px 12px;
      &:hover .fa-icon--add,
      &:focus .fa-icon--add {
        color: $white;
      }
      &--times {
        padding: 9px 13px;
      }
    }
    &__ava-box {
      padding-left: 8px;
    }
    &__likes-box {
      padding: 0 12px;
    }
    &__likes {
      display     : flex;
      align-items : center;
      height      : 100%;
    }
    &__likes-number {
      fnt($text-light, 12px, $weight-semibold, left);
      padding     : 0 8px;
      height      : 100%;
      display     : flex;
      align-items : center;
    }
    &__show-btn {
      width: 76px;
    }
  }

  .author {
    fl-left();

    &__ava {
      background: center center/cover no-repeat $grey-lighter;
      border-radius: 50%;
      flex: 0 0 auto;
      height: 32px;
      width: 32px;
    }
    &__title-box {
      display: flex;
      flex-direction: column;
      padding-left: 12px;
    }
    &__title {
      fnt($text, 12px, $weight-semibold, left);
    }
    &__sub-title {
      fnt($primary, 12px, $weight-semibold, left);
    }
  }
  .ava-box {
    fl-left();
    &__avatar {
      height: 32px;
      width: 32px;
      border-radius: 50%;
      flex: 0 0 auto;
      background: center center/cover no-repeat $grey-lighter;
      border: 2px solid $white;
      &:not(:first-child) {
        margin-left: -12px;
      }
    }
  }
  .likes-box {
    &__title {
      fnt($text, 12px, $weight-semibold, left);
    }
    &__text {
      fnt($text-light, 12px, $weight-semibold, left);
    }
  }

</style>
