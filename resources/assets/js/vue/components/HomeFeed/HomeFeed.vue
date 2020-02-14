<template>
  <div class="HomeFeed column home-feed__column">
    <HomePageNavBar
      :active-component="activeComponent"
      @change-component="setComponent"
    />
    <component
      :is="activeComponent"
      :recommended-clips="clips"
    />
  </div>
</template>

<script>

import HomePageRecommended from './Recommended/HomePageRecommended';
import HomePageMyFeed from './MyFeed/HomePageMyFeed';
import HomePageNavBar from './HomePageNavBar';

export default {
  name: 'HomeFeed',
  components: {
    HomePageNavBar,
    HomePageRecommended,
    HomePageMyFeed,
  },
  props: {
    flagReasons: {
      type: Array,
      default: () => [],
    },
    user: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      activeComponent: 'HomePageMyFeed',
      clips: [
        {
          assets: [],
          title: 'in ante',
          thumbPath: 'https://picsum.photos/id/51/1280/720',
          spritePath: 'https://s3.amazonaws.com/actionlime-development/users/test+sprite.png',
          views: 100,
          clipped: 34,
          starred: 456,
          comment: 23,
          duration: '5:43',
          author: {
            id: 1,
            displayName: 'Magdalene Jonah',
            avatar: 'http://i.pravatar.cc/50?img=70',
            dataRegistration: '02/03/2017',
          },
        },
        {
          assets: [],
          title: 'sed tincidunt',
          thumbPath: 'https://picsum.photos/id/18/1280/720',
          spritePath: 'https://s3.amazonaws.com/actionlime-development/users/test+sprite.png',
          viewsCount: 100,
          clipped: 34,
          starred: 456,
          comment: 23,
          duration: '2:00',
          author: {
            id: 2,
            displayName: 'Colline Debnam',
            avatar: 'http://i.pravatar.cc/50?img=65',
            dataRegistration: '05/25/2017',
          },
        },
        {
          assets: [],
          title: 'in magna',
          thumbPath: 'https://picsum.photos/id/30/1280/720',
          spritePath: 'https://s3.amazonaws.com/actionlime-development/users/test+sprite.png',
          viewsCount: 234,
          clipped: 34,
          starred: 456,
          comment: 23,
          duration: '2:43',
          author: {
            id: 3,
            displayName: 'Jon Balhatchet',
            avatar: 'http://i.pravatar.cc/50?img=64',
            dataRegistration: '02/22/2017',
          },
        },
        {
          assets: [],
          title: 'morbi non quam',
          thumbPath: 'https://picsum.photos/id/31/1280/720',
          spritePath: 'https://s3.amazonaws.com/actionlime-development/users/test+sprite.png',
          viewsCount: 523,
          clipped: 34,
          starred: 456,
          comment: 23,
          duration: '1:05',
          author: {
            id: 4,
            displayName: 'Constantino Slot',
            avatar: 'http://i.pravatar.cc/50?img=59',
            dataRegistration: '08/18/2017',
          },
        },
        {
          assets: [],
          title: 'aliquam lacus',
          thumbPath: 'https://picsum.photos/id/32/1280/720',
          spritePath: 'https://s3.amazonaws.com/actionlime-development/users/test+sprite.png',
          viewsCount: 15,
          clipped: 34,
          starred: 456,
          comment: 23,
          duration: '0:45',
          author: {
            id: 5,
            displayName: 'Parnell Wilkins',
            avatar: 'http://i.pravatar.cc/50?img=60',
            dataRegistration: '02/09/2017',
          },
        },
        {
          assets: [],
          title: 'quam',
          thumbPath: 'https://picsum.photos/id/33/1280/720',
          spritePath: 'https://s3.amazonaws.com/actionlime-development/users/test+sprite.png',
          viewsCount: 436,
          clipped: 34,
          starred: 456,
          comment: 23,
          duration: '1:16',
          author: {
            id: 6,
            displayName: 'Anatola Traite',
            avatar: 'http://i.pravatar.cc/50?img=56',
            dataRegistration: '10/17/2016',
          },
        },
        {
          assets: [],
          title: 'luctus et',
          thumbPath: 'https://picsum.photos/id/34/1280/720',
          spritePath: 'https://s3.amazonaws.com/actionlime-development/users/test+sprite.png',
          viewsCount: 145,
          clipped: 34,
          starred: 456,
          comment: 23,
          duration: '2:26',
          author: {
            id: 7,
            displayName: 'Johnny Sackes',
            avatar: 'http://i.pravatar.cc/50?img=52',
            dataRegistration: '12/01/2016',
          },
        },
        {
          assets: [],
          title: 'primis in',
          thumbPath: 'https://picsum.photos/id/35/1280/720',
          spritePath: 'https://s3.amazonaws.com/actionlime-development/users/test+sprite.png',
          viewsCount: 234,
          clipped: 34,
          starred: 456,
          comment: 23,
          duration: '2:32',
          author: {
            id: 8,
            displayName: 'Eli Lemon',
            avatar: 'http://i.pravatar.cc/50?img=47',
            dataRegistration: '08/08/2017',
          },
        },
        {
          assets: [],
          title: 'habitasse platea',
          thumbPath: 'https://picsum.photos/id/36/1280/720',
          spritePath: 'https://s3.amazonaws.com/actionlime-development/users/test+sprite.png',
          viewsCount: 543,
          clipped: 34,
          starred: 456,
          comment: 23,
          duration: '1:30',
          author: {
            id: 9,
            displayName: 'Michal Fibbens',
            avatar: 'http://i.pravatar.cc/50?img=44',
            dataRegistration: '01/13/2017',
          },
        },
        {
          assets: [],
          title: 'gravida sem praesent',
          thumbPath: 'https://picsum.photos/id/37/1280/720',
          spritePath: 'https://s3.amazonaws.com/actionlime-development/users/test+sprite.png',
          viewsCount: 423,
          clipped: 34,
          starred: 456,
          comment: 23,
          duration: '0:30',
          author: {
            id: 10,
            displayName: 'Una Meecher',
            avatar: 'http://i.pravatar.cc/50?img=43',
            dataRegistration: '09/22/2017',
          },
        },
        {
          assets: [],
          title: 'primis in',
          thumbPath: 'https://picsum.photos/id/38/1280/720',
          spritePath: 'https://s3.amazonaws.com/actionlime-development/users/test+sprite.png',
          viewsCount: 234,
          clipped: 34,
          starred: 456,
          comment: 23,
          duration: '0:15',
          author: {
            id: 8,
            displayName: 'Eli Lemon',
            avatar: 'http://i.pravatar.cc/50?img=39',
            dataRegistration: '08/08/2017',
          },
        },
        {
          assets: [],
          title: 'habitasse platea',
          thumbPath: 'https://picsum.photos/id/39/1280/720',
          spritePath: 'https://s3.amazonaws.com/actionlime-development/users/test+sprite.png',
          viewsCount: 543,
          clipped: 34,
          starred: 456,
          comment: 23,
          duration: '4:45',
          author: {
            id: 9,
            displayName: 'Michal Fibbens',
            avatar: 'http://i.pravatar.cc/50?img=70',
            dataRegistration: '01/13/2017',
          },
        },
        {
          assets: [],
          title: 'gravida sem praesent',
          thumbPath: 'https://picsum.photos/id/40/1280/720',
          spritePath: 'https://s3.amazonaws.com/actionlime-development/users/test+sprite.png',
          viewsCount: 423,
          clipped: 34,
          starred: 456,
          comment: 23,
          duration: '2:55',
          author: {
            id: 10,
            displayName: 'Una Meecher',
            avatar: 'http://i.pravatar.cc/50?img=36',
            dataRegistration: '09/22/2017',
          },
        },
      ],
    };
  },
  mounted() {
    this.setComponentByHash();
    window.addEventListener('hashchange', this.setComponentByHash, false);
  },
  destroyed() {
    window.removeEventListener('hashchange', this.setComponentByHash, false);
  },
  methods: {
    setComponent(component) {
      switch (component) {
        case 'HomePageMyFeed':
          window.location.hash = 'my-feed';
          break;
        case 'HomePageRecommended':
          window.location.hash = 'recommended';
          break;
        default:
          window.location.hash = 'main';
      }
      this.activeComponent = component;
    },
    setComponentByHash() {
      const { hash } = window.location;
      switch (hash) {
        case '#feed':
          this.activeComponent = 'HomePageMyFeed';
          break;
        case '#recommended':
          this.activeComponent = 'HomePageRecommended';
          break;
        default:
          this.activeComponent = 'HomePageMyFeed';
      }
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .home-feed {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    position: relative;

    &__main {
      background-color: $white-bis;
      display: flex;
      flex-grow: 1;
      padding-top: 73px;
      position: relative;
    }

    &__column {
      margin: 0 0 0 72px;
      padding: 32px 32px 0 28px;
      width 100%
    }
  }

</style>
