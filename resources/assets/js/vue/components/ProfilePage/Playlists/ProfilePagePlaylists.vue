<template>
  <div class="ProfilePagePlaylists playlists">
    <ProfilePagePlaylistsSearchBar
      :user="user"
      :search-text="searchText"
      @input-search-text="updateSearch"
    />
    <div>
      <font-awesome-icon
        v-if="$apollo.queries.fetchPlaylists.loading"
        :icon="['fas', 'spinner']"
        spin
        class="fa-icon"
      />
    </div>
    <template v-if="playlists.length">
      <ProfilePagePlaylistItem
        v-for="(playlist, index) in fetchPlaylists"
        :key="`pl-${index}`"
        :playlist="playlist"
      />
    </template>
  </div>
</template>

<script>
import FETCH_PLAYLISTS from 'Gql/playlists/queries/fetchPlaylists.graphql';
import ProfilePagePlaylistsSearchBar from './ProfilePagePlaylistsSearchBar';
import ProfilePagePlaylistItem from './ProfilePagePlaylistItem';

export default {
  name: 'ProfilePagePlaylists',
  components: {
    ProfilePagePlaylistsSearchBar,
    ProfilePagePlaylistItem,
  },
  props: {
    user: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      searchText: '',
      playlists: [
        {
          id: 1,
          name: 'Mimi Dearl',
          authorId: '1',
          email: 'mdearl0@example.com',
          avatar: 'http://i.pravatar.cc/100?img=1',
          clips: [
            {
              title: 'in ante',
              coverImg: 'https://picsum.photos/240/320/?image=1078',
              views: 100,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 1,
                displayName: 'Magdalene Jonah',
                avatar: 'http://i.pravatar.cc/100?img=2',
                dataRegistration: '02/03/2017',
              },
            },
            {
              title: 'sed tincidunt',
              coverImg: 'https://picsum.photos/240/320/?image=1077',
              views: 100,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 2,
                displayName: 'Colline Debnam',
                avatar: 'http://i.pravatar.cc/100?img=3',
                dataRegistration: '05/25/2017',
              },
            },
            {
              title: 'in magna',
              coverImg: 'https://picsum.photos/240/320/?image=1076',
              views: 234,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 3,
                displayName: 'Jon Balhatchet',
                avatar: 'http://i.pravatar.cc/100?img=4',
                dataRegistration: '02/22/2017',
              },
            },
            {
              title: 'morbi non quam',
              coverImg: 'https://picsum.photos/240/320/?image=1075',
              views: 523,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 4,
                displayName: 'Constantino Slot',
                avatar: 'http://i.pravatar.cc/100?img=5',
                dataRegistration: '08/18/2017',
              },
            },
          ],
        },
        {
          id: 2,
          name: 'Annadiana Barnshaw',
          authorId: '1',
          email: 'abarnshaw1@xinhuanet.com',
          avatar: 'http://i.pravatar.cc/100?img=6',
          clips: [
            {
              title: 'in ante',
              coverImg: 'https://picsum.photos/240/320/?image=1074',
              views: 100,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 1,
                displayName: 'Magdalene Jonah',
                avatar: 'http://i.pravatar.cc/100?img=7',
                dataRegistration: '02/03/2017',
              },
            },
            {
              title: 'sed tincidunt',
              coverImg: 'https://picsum.photos/240/320/?image=1073',
              views: 100,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 2,
                displayName: 'Colline Debnam',
                avatar: 'http://i.pravatar.cc/100?img=8',
                dataRegistration: '05/25/2017',
              },
            },
            {
              title: 'in magna',
              coverImg: 'https://picsum.photos/240/320/?image=1072',
              views: 234,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 3,
                displayName: 'Jon Balhatchet',
                avatar: 'http://i.pravatar.cc/100?img=9',
                dataRegistration: '02/22/2017',
              },
            },
            {
              title: 'morbi non quam',
              coverImg: 'https://picsum.photos/240/320/?image=1071',
              views: 523,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 4,
                displayName: 'Constantino Slot',
                avatar: 'http://i.pravatar.cc/100?img=10',
                dataRegistration: '08/18/2017',
              },
            },
          ],
        },
        {
          id: 3,
          name: 'Xylia Figgs',
          authorId: '1',
          email: 'xfiggs2@wordpress.org',
          avatar: 'http://i.pravatar.cc/100?img=43',
          clips: [
            {
              title: 'in ante',
              coverImg: 'https://picsum.photos/240/320/?image=1070',
              views: 100,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 1,
                displayName: 'Magdalene Jonah',
                avatar: 'http://i.pravatar.cc/100?img=11',
                dataRegistration: '02/03/2017',
              },
            },
            {
              title: 'sed tincidunt',
              coverImg: 'https://picsum.photos/240/320/?image=1069',
              views: 100,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 2,
                displayName: 'Colline Debnam',
                avatar: 'http://i.pravatar.cc/100?img=12',
                dataRegistration: '05/25/2017',
              },
            },
            {
              title: 'in magna',
              coverImg: 'https://picsum.photos/240/320/?image=1068',
              views: 234,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 3,
                displayName: 'Jon Balhatchet',
                avatar: 'http://i.pravatar.cc/100?img=13',
                dataRegistration: '02/22/2017',
              },
            },
            {
              title: 'morbi non quam',
              coverImg: 'https://picsum.photos/240/320/?image=1067',
              views: 523,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 4,
                displayName: 'Constantino Slot',
                avatar: 'http://i.pravatar.cc/100?img=14',
                dataRegistration: '08/18/2017',
              },
            },
          ],
        },
        {
          id: 4,
          name: 'Tim Lynnitt',
          authorId: '1',
          email: 'tlynnitt3@disqus.com',
          avatar: 'http://i.pravatar.cc/100?img=15',
          clips: [
            {
              title: 'in ante',
              coverImg: 'https://picsum.photos/240/320/?image=1066',
              views: 100,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 1,
                displayName: 'Magdalene Jonah',
                avatar: 'http://i.pravatar.cc/100?img=16',
                dataRegistration: '02/03/2017',
              },
            },
            {
              title: 'sed tincidunt',
              coverImg: 'https://picsum.photos/240/320/?image=1065',
              views: 100,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 2,
                displayName: 'Colline Debnam',
                avatar: 'http://i.pravatar.cc/100?img=17',
                dataRegistration: '05/25/2017',
              },
            },
            {
              title: 'in magna',
              coverImg: 'https://picsum.photos/240/320/?image=1064',
              views: 234,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 3,
                displayName: 'Jon Balhatchet',
                avatar: 'http://i.pravatar.cc/100?img=18',
                dataRegistration: '02/22/2017',
              },
            },
            {
              title: 'morbi non quam',
              coverImg: 'https://picsum.photos/240/320/?image=1063',
              views: 523,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 4,
                displayName: 'Constantino Slot',
                avatar: 'http://i.pravatar.cc/100?img=19',
                dataRegistration: '08/18/2017',
              },
            },
          ],
        },
        {
          id: 5,
          name: 'Clarey Kilbey',
          email: 'ckilbey4@aol.com',
          avatar: 'http://i.pravatar.cc/100?img=20',
          clips: [
            {
              title: 'in ante',
              coverImg: 'https://picsum.photos/240/320/?image=1062',
              views: 100,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 1,
                displayName: 'Magdalene Jonah',
                avatar: 'http://i.pravatar.cc/100?img=21',
                dataRegistration: '02/03/2017',
              },
            },
            {
              title: 'sed tincidunt',
              coverImg: 'https://picsum.photos/240/320/?image=1061',
              views: 100,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 2,
                displayName: 'Colline Debnam',
                avatar: 'http://i.pravatar.cc/100?img=22',
                dataRegistration: '05/25/2017',
              },
            },
            {
              title: 'in magna',
              coverImg: 'https://picsum.photos/240/320/?image=1060',
              views: 234,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 3,
                displayName: 'Jon Balhatchet',
                avatar: 'http://i.pravatar.cc/100?img=23',
                dataRegistration: '02/22/2017',
              },
            },
            {
              title: 'morbi non quam',
              coverImg: 'https://picsum.photos/240/320/?image=1059',
              views: 523,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 4,
                displayName: 'Constantino Slot',
                avatar: 'http://i.pravatar.cc/100?img=24',
                dataRegistration: '08/18/2017',
              },
            },
          ],
        },
        {
          id: 6,
          name: 'Arlee Scuffham',
          authorId: '1',
          email: 'ascuffham5@skyrock.com',
          avatar: 'http://i.pravatar.cc/100?img=25',
          clips: [
            {
              title: 'in ante',
              coverImg: 'https://picsum.photos/240/320/?image=1058',
              views: 100,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 1,
                displayName: 'Magdalene Jonah',
                avatar: 'http://i.pravatar.cc/100?img=26',
                dataRegistration: '02/03/2017',
              },
            },
            {
              title: 'sed tincidunt',
              coverImg: 'https://picsum.photos/240/320/?image=1057',
              views: 100,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 2,
                displayName: 'Colline Debnam',
                avatar: 'http://i.pravatar.cc/100?img=27',
                dataRegistration: '05/25/2017',
              },
            },
            {
              title: 'in magna',
              coverImg: 'https://picsum.photos/240/320/?image=1056',
              views: 234,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 3,
                displayName: 'Jon Balhatchet',
                avatar: 'http://i.pravatar.cc/100?img=28',
                dataRegistration: '02/22/2017',
              },
            },
            {
              title: 'morbi non quam',
              coverImg: 'https://picsum.photos/240/320/?image=1055',
              views: 523,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 4,
                displayName: 'Constantino Slot',
                avatar: 'http://i.pravatar.cc/100?img=29',
                dataRegistration: '08/18/2017',
              },
            },
          ],
        },
        {
          id: 7,
          name: 'Starlene Tidcombe',
          authorId: '1',
          email: 'stidcombe6@mediafire.com',
          avatar: 'http://i.pravatar.cc/100?img=30',
          clips: [
            {
              title: 'in ante',
              coverImg: 'https://picsum.photos/240/320/?image=1054',
              views: 100,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 1,
                displayName: 'Magdalene Jonah',
                avatar: 'http://i.pravatar.cc/100?img=31',
                dataRegistration: '02/03/2017',
              },
            },
            {
              title: 'sed tincidunt',
              coverImg: 'https://picsum.photos/240/320/?image=1053',
              views: 100,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 2,
                displayName: 'Colline Debnam',
                avatar: 'http://i.pravatar.cc/100?img=32',
                dataRegistration: '05/25/2017',
              },
            },
            {
              title: 'in magna',
              coverImg: 'https://picsum.photos/240/320/?image=1052',
              views: 234,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 3,
                displayName: 'Jon Balhatchet',
                avatar: 'http://i.pravatar.cc/100?img=33',
                dataRegistration: '02/22/2017',
              },
            },
            {
              title: 'morbi non quam',
              coverImg: 'https://picsum.photos/240/320/?image=1051',
              views: 523,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 4,
                displayName: 'Constantino Slot',
                avatar: 'http://i.pravatar.cc/100?img=34',
                dataRegistration: '08/18/2017',
              },
            },
          ],
        },
        {
          id: 8,
          name: 'Ynes Steeden',
          authorId: '1',
          email: 'ysteeden7@ebay.co.uk',
          avatar: 'http://i.pravatar.cc/100?img=35',
          clips: [
            {
              title: 'in ante',
              coverImg: 'https://picsum.photos/240/320/?image=1050',
              views: 100,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 1,
                displayName: 'Magdalene Jonah',
                avatar: 'http://i.pravatar.cc/100?img=36',
                dataRegistration: '02/03/2017',
              },
            },
            {
              title: 'sed tincidunt',
              coverImg: 'https://picsum.photos/240/320/?image=1049',
              views: 100,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 2,
                displayName: 'Colline Debnam',
                avatar: 'http://i.pravatar.cc/100?img=37',
                dataRegistration: '05/25/2017',
              },
            },
            {
              title: 'in magna',
              coverImg: 'https://picsum.photos/240/320/?image=1048',
              views: 234,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 3,
                displayName: 'Jon Balhatchet',
                avatar: 'http://i.pravatar.cc/100?img=38',
                dataRegistration: '02/22/2017',
              },
            },
            {
              title: 'morbi non quam',
              coverImg: 'https://picsum.photos/240/320/?image=1047',
              views: 523,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 4,
                displayName: 'Constantino Slot',
                avatar: 'http://i.pravatar.cc/100?img=39',
                dataRegistration: '08/18/2017',
              },
            },
          ],
        },
        {
          id: 9,
          name: 'Daffy Rangle',
          authorId: '1',
          email: 'drangle8@kickstarter.com',
          avatar: 'http://i.pravatar.cc/100?img=40',
          clips: [
            {
              title: 'in ante',
              coverImg: 'https://picsum.photos/240/320/?image=1046',
              views: 100,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 1,
                displayName: 'Magdalene Jonah',
                avatar: 'http://i.pravatar.cc/100?img=41',
                dataRegistration: '02/03/2017',
              },
            },
            {
              title: 'sed tincidunt',
              coverImg: 'https://picsum.photos/240/320/?image=1045',
              views: 100,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 2,
                displayName: 'Colline Debnam',
                avatar: 'http://i.pravatar.cc/100?img=42',
                dataRegistration: '05/25/2017',
              },
            },
            {
              title: 'in magna',
              coverImg: 'https://picsum.photos/240/320/?image=1044',
              views: 234,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 3,
                displayName: 'Jon Balhatchet',
                avatar: 'http://i.pravatar.cc/100?img=43',
                dataRegistration: '02/22/2017',
              },
            },
            {
              title: 'morbi non quam',
              coverImg: 'https://picsum.photos/240/320/?image=1043',
              views: 523,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 4,
                displayName: 'Constantino Slot',
                avatar: 'http://i.pravatar.cc/100?img=44',
                dataRegistration: '08/18/2017',
              },
            },
            {
              title: 'aliquam lacus',
              coverImg: 'https://picsum.photos/240/320/?image=1042',
              views: 15,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 5,
                displayName: 'Parnell Wilkins',
                avatar: 'http://i.pravatar.cc/100?img=45',
                dataRegistration: '02/09/2017',
              },
            },
          ],
        },
        {
          id: 10,
          name: 'Muhammad Newland',
          authorId: '1',
          email: 'mnewland9@cbsnews.com',
          avatar: 'http://i.pravatar.cc/100?img=46',
          clips: [
            {
              title: 'in ante',
              coverImg: 'https://picsum.photos/240/320/?image=1041',
              views: 100,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 1,
                displayName: 'Magdalene Jonah',
                avatar: 'http://i.pravatar.cc/100?img=47',
                dataRegistration: '02/03/2017',
              },
            },
            {
              title: 'sed tincidunt',
              coverImg: 'https://picsum.photos/240/320/?image=1040',
              views: 100,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 2,
                displayName: 'Colline Debnam',
                avatar: 'http://i.pravatar.cc/100?img=48',
                dataRegistration: '05/25/2017',
              },
            },
            {
              title: 'in magna',
              coverImg: 'https://picsum.photos/240/320/?image=1039',
              views: 234,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 3,
                displayName: 'Jon Balhatchet',
                avatar: 'http://i.pravatar.cc/100?img=49',
                dataRegistration: '02/22/2017',
              },
            },
            {
              title: 'morbi non quam',
              coverImg: 'https://picsum.photos/240/320/?image=1038',
              views: 523,
              clipped: 34,
              starred: 456,
              comment: 23,
              author: {
                id: 4,
                displayName: 'Constantino Slot',
                avatar: 'http://i.pravatar.cc/100?img=50',
                dataRegistration: '08/18/2017',
              },
            },
          ],
        },
      ],
      fetchPlaylists: [],
    };
  },
  created() {
    if (this.fetchPlaylists.length) {
      this.$apollo.queries.fetchPlaylists.refetch();
    }
  },
  methods: {
    updateSearch(inputValue) {
      this.searchText = inputValue;
    },
  },
  apollo: {
    fetchPlaylists: {
      query: FETCH_PLAYLISTS,
      variables() {
        return {
          userId: this.user.id,
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
  .fa-icon {
    fnt($text-light, 1rem, $weight-normal, center)
  }
</style>
