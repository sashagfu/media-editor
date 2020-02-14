<template>
  <div class="SearchResultUsers sr-p">
    <SearchResultBar
      :show-more="showBtnShowMore"
      @show-more="showMore"
    >
      <template slot="header">
        <div class="srb-h__title">
          Show {{ fetchProjects.length }} Projects results of for:
          <span class="srb-h__search">
            "{{ searchQuery }}"
          </span>
        </div>
      </template>
    </SearchResultBar>
    <div
      v-if="$apollo.queries.fetchProjects.loading"
      class="sr-p__spinner"
    >
      <font-awesome-icon
        :icon="['fas', 'spinner']"
        spin
        class="fa-icon"
      />
    </div>
    <div
      v-if="sliceShow.length"
      class="sr-p__main"
    >
      <div
        v-for="item in sliceShow"
        :key="`pi-${item.id}`"
        class="sr-p__item"
      >
        <HomeFeedItem
          :item="item"
          :users-data="showUsersData"
        />
      </div>
    </div>
    <div
      v-if="showBtnShowMore"
      class="sr-p__show-more"
    >
      <ShowMoreButton
        @show-more="showMore"
      />
    </div>
  </div>
</template>

<script>
import FETCH_PROJECT from 'Gql/projects/queries/fetchProjects.graphql';

import HomeFeedItem from 'Pages/MediaItem/HomeFeedItemMini';
import ShowMoreButton from 'Pages/Common/ShowMoreButton';

import SearchResultBar from './SearchResultBar';

export default {
  name: 'SearchResultProjects',
  components: {
    SearchResultBar,
    ShowMoreButton,
    HomeFeedItem,
  },
  data() {
    return {
      searchQuery: '',
      qtyItemShow: 5,
      fetchProjects: [],
      showUsersData: true,
      fkClips: [
        {
          id: 1,
          title: 'in ante',
          coverImg: 'https://picsum.photos/320/880/?image=1083',
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
          id: 2,
          title: 'sed tincidunt',
          coverImg: 'https://picsum.photos/320/880/?image=1084',
          views: 100,
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
          id: 3,
          title: 'in magna',
          coverImg: 'https://picsum.photos/320/880/?image=1080',
          views: 234,
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
          id: 4,
          title: 'morbi non quam',
          coverImg: 'https://picsum.photos/320/880/?image=1074',
          views: 523,
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
          id: 5,
          title: 'aliquam lacus',
          coverImg: 'https://picsum.photos/320/880/?image=1073',
          views: 15,
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
          id: 6,
          title: 'quam',
          coverImg: 'https://picsum.photos/320/880/?image=1069',
          views: 436,
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
          id: 7,
          title: 'luctus et',
          coverImg: 'https://picsum.photos/320/880/?image=1063',
          views: 145,
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
          id: 8,
          title: 'primis in',
          coverImg: 'https://picsum.photos/320/880/?image=1066',
          views: 234,
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
          id: 9,
          title: 'habitasse platea',
          coverImg: 'https://picsum.photos/320/880/?image=1065',
          views: 543,
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
          id: 10,
          title: 'gravida sem praesent',
          coverImg: 'https://picsum.photos/320/880/?image=1050',
          views: 423,
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
          id: 11,
          title: 'primis in',
          coverImg: 'https://picsum.photos/320/880/?image=1043',
          views: 234,
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
          id: 12,
          title: 'habitasse platea',
          coverImg: 'https://picsum.photos/320/880/?image=1039',
          views: 543,
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
          id: 0,
          title: 'gravida sem praesent',
          coverImg: 'https://picsum.photos/320/880/?image=1041',
          views: 423,
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
    sliceShow() {
      return (this.fetchProjects.length) ? this.fetchProjects.slice(0, this.qtyItemShow) : [];
    },
    showBtnShowMore() {
      return this.fetchProjects.length > this.qtyItemShow;
    },
  },
  created() {
    this.searchQuery = this.$route.query.q;
  },
  methods: {
    showMore() {
      if (this.showBtnShowMore) {
        this.qtyItemShow += 5;
      }
    },
  },
  apollo: {
    fetchProjects: {
      query: FETCH_PROJECT,
      variables() {
        return {
          term: this.searchQuery,
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
  @import '../../../../sass/front/components/bulma-theme';

  .fa-icon {
    fnt($grey, 18px, $weight-light, center);
  }

  .srb-h {
    &__title {
      fnt($text, 14px, $weight-semibold, left);
      text-transform: capitalize;
    }
    &__search {
      fnt($primary, 14px, $weight-semibold, left);
      text-transform: capitalize;
    }
  }

  .sr-p {
    display: flex;
    flex-direction: column;
    &__no-items {
      fl-center();
      fnt($grey-lighter, 28px, $weight-light, center);
      line-height: 1;
      padding: 56px 0;
    }
    &__spinner {
      fl-center();
      margin-bottom: 24px;
    }
    &__main {
      fl-left();
      flex-wrap: wrap;
      margin-right: -18px;
    }
    &__item {
      height: 256px;
      padding: 0 18px 26px 0;
      width: 20%;
    }
    &__show-more {
      fl-center();
    }
  }

</style>
