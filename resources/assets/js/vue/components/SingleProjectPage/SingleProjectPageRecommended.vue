<template>
  <div class="SingleProjectPageRecommended box sppr">
    <div class="sppr__headline">
      <div class="left">
        <div class="sppr__header-title">
          {{ trans('feed.recommended') }}
        </div>
      </div>
      <div class="fl-right"/>
    </div>
    <div class="sppr__main">
      <div
        v-for="item in recommendedSlice"
        :key="`rc-${item.id}`"
        class="sppr__item"
      >
        <HomeFeedItemMini
          :item="item"
          :users-data="usersData"
        />
      </div>

      <ShowMoreButton
        v-if="showBtnShowMore"
        @show-more="showMore"
      />

    </div>
  </div>
</template>

<script>
import HomeFeedItemMini from 'Pages/MediaItem/HomeFeedItemMini';
import ShowMoreButton from 'Pages/Common/ShowMoreButton';

export default {
  name: 'SingleProjectPageRecommended',
  components: {
    HomeFeedItemMini,
    ShowMoreButton,
  },
  data() {
    return {
      usersData: true,
      showQty: 5,
      // TODO remove if add apollo query fetchRecommended
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
  computed: {
    showBtnShowMore() {
      // TODO change clips to fetchRecommended
      return this.showQty < this.clips.length;
    },
    recommendedSlice() {
      // TODO change clips to fetchRecommended
      return this.clips.slice(0, this.showQty);
    },
  },
  methods: {
    showMore() {
      this.showQty += 5;
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .sppr // Single Project Page Recommended

    &__headline
      fl-between()
      border-bottom 1px solid $border
      height 48px
      padding 0 26px

    &__header-title
      fnt($text, 14px, $weight-semibold, left)
      text-transform capitalize

    &__main
      display flex
      flex-direction column
      padding 26px
      padding-bottom 0

    &__item
      height 256px
      padding-bottom 26px

</style>
