<template>
  <div class="HomeFeedFriends hf-friends">
    <div
      v-for="(friend, key) in friends"
      :key="key"
      class="hf-circles__item"
    >
      <HomeFriendBar
        :friend="friend"
      />
      <HomeFeedLine
        :clips="friend.clips"
        :users-data="usersData"
      />
    </div>
  </div>
</template>

<script>
import HomeFriendBar from './HomeFriendBar';
import HomeFeedLine from './HomeFeedLine';

export default {
  name: 'HomeFeedFriends',
  components: {
    HomeFriendBar,
    HomeFeedLine,
  },

  props: {
    clips: {
      type: Array,
      default: () => [],
    },
    usersData: {
      type: Boolean,
      default: true,
    },
  },

  data() {
    return {
      friends: [
        {
          id: 1,
          fullName: 'Mimi Dearl',
          email: 'mdearl0@example.com',
          avatar: 'https://robohash.org/praesentiumdoloremconsequuntur.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/suntsitaut.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/etiustoporro.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/quiavoluptas.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/nullasitnobis.png?size=50x50&set=set1',
                dataRegistration: '08/18/2017',
              },
            },
          ],
        },
        {
          id: 2,
          fullName: 'Annadiana Barnshaw',
          email: 'abarnshaw1@xinhuanet.com',
          avatar: 'https://robohash.org/beataeaccusamusarchitecto.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/suntsitaut.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/etiustoporro.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/quiavoluptas.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/nullasitnobis.png?size=50x50&set=set1',
                dataRegistration: '08/18/2017',
              },
            },
          ],
        },
        {
          id: 3,
          fullName: 'Xylia Figgs',
          email: 'xfiggs2@wordpress.org',
          avatar: 'https://robohash.org/autemvoluptatibusvoluptas.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/suntsitaut.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/etiustoporro.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/quiavoluptas.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/nullasitnobis.png?size=50x50&set=set1',
                dataRegistration: '08/18/2017',
              },
            },
          ],
        },
        {
          id: 4,
          fullName: 'Tim Lynnitt',
          email: 'tlynnitt3@disqus.com',
          avatar: 'https://robohash.org/blanditiisrepudiandaesunt.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/suntsitaut.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/etiustoporro.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/quiavoluptas.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/nullasitnobis.png?size=50x50&set=set1',
                dataRegistration: '08/18/2017',
              },
            },
          ],
        },
        {
          id: 5,
          fullName: 'Clarey Kilbey',
          email: 'ckilbey4@aol.com',
          avatar: 'https://robohash.org/excepturisuntdebitis.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/suntsitaut.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/etiustoporro.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/quiavoluptas.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/nullasitnobis.png?size=50x50&set=set1',
                dataRegistration: '08/18/2017',
              },
            },
          ],
        },
        {
          id: 6,
          fullName: 'Arlee Scuffham',
          email: 'ascuffham5@skyrock.com',
          avatar: 'https://robohash.org/autquimodi.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/suntsitaut.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/etiustoporro.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/quiavoluptas.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/nullasitnobis.png?size=50x50&set=set1',
                dataRegistration: '08/18/2017',
              },
            },
          ],
        },
        {
          id: 7,
          fullName: 'Starlene Tidcombe',
          email: 'stidcombe6@mediafire.com',
          avatar: 'https://robohash.org/culpatotamest.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/suntsitaut.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/etiustoporro.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/quiavoluptas.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/nullasitnobis.png?size=50x50&set=set1',
                dataRegistration: '08/18/2017',
              },
            },
          ],
        },
        {
          id: 8,
          fullName: 'Ynes Steeden',
          email: 'ysteeden7@ebay.co.uk',
          avatar: 'https://robohash.org/inciduntquisfugit.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/suntsitaut.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/etiustoporro.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/quiavoluptas.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/nullasitnobis.png?size=50x50&set=set1',
                dataRegistration: '08/18/2017',
              },
            },
          ],
        },
        {
          id: 9,
          fullName: 'Daffy Rangle',
          email: 'drangle8@kickstarter.com',
          avatar: 'https://robohash.org/inciduntmaioresmollitia.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/suntsitaut.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/etiustoporro.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/quiavoluptas.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/nullasitnobis.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/autemitaquequas.png?size=50x50&set=set1',
                dataRegistration: '02/09/2017',
              },
            },
          ],
        },
        {
          id: 10,
          fullName: 'Muhammad Newland',
          email: 'mnewland9@cbsnews.com',
          avatar: 'https://robohash.org/autemeligendinam.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/suntsitaut.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/etiustoporro.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/quiavoluptas.png?size=50x50&set=set1',
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
                avatar: 'https://robohash.org/nullasitnobis.png?size=50x50&set=set1',
                dataRegistration: '08/18/2017',
              },
            },
          ],
        },
      ],
    };
  },

};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .hf-friends
    display flex
    flex-direction column

</style>
