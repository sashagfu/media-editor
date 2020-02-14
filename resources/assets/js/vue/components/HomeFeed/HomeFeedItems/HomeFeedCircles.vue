<template>
  <div class="HomeFeedCircles hf-circles">
    <div
      v-for="(circle, key) in circles"
      :key="key"
      class="hf-circles__item"
    >
      <HomeCircleBar
        :circle="circle"
      />
      <HomeFeedLine
        :clips="circle.clips"
        :users-data="usersData"
      />
    </div>
  </div>
</template>

<script>
import HomeCircleBar from './HomeCircleBar';
import HomeFeedLine from './HomeFeedLine';

export default {
  name: 'HomeFeedCircles',
  components: {
    HomeCircleBar,
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
      circles: [
        {
          id: 1,
          title: 'sed justo pellentesque viverra pede',
          members: [
            {
              id: 1,
              displayName: 'Mimi Dearl',
              email: 'mdearl0@example.com',
              avatar: 'https://robohash.org/praesentiumdoloremconsequuntur.png?size=50x50&set=set1',
            },
            {
              id: 2,
              displayName: 'Annadiana Barnshaw',
              email: 'abarnshaw1@xinhuanet.com',
              avatar: 'https://robohash.org/beataeaccusamusarchitecto.png?size=50x50&set=set1',
            },
            {
              id: 3,
              displayName: 'Xylia Figgs',
              email: 'xfiggs2@wordpress.org',
              avatar: 'https://robohash.org/autemvoluptatibusvoluptas.png?size=50x50&set=set1',
            },
            {
              id: 4,
              displayName: 'Tim Lynnitt',
              email: 'tlynnitt3@disqus.com',
              avatar: 'https://robohash.org/blanditiisrepudiandaesunt.png?size=50x50&set=set1',
            },
            {
              id: 5,
              displayName: 'Clarey Kilbey',
              email: 'ckilbey4@aol.com',
              avatar: 'https://robohash.org/excepturisuntdebitis.png?size=50x50&set=set1',
            },
            {
              id: 6,
              displayName: 'Arlee Scuffham',
              email: 'ascuffham5@skyrock.com',
              avatar: 'https://robohash.org/autquimodi.png?size=50x50&set=set1',
            },
            {
              id: 7,
              displayName: 'Starlene Tidcombe',
              email: 'stidcombe6@mediafire.com',
              avatar: 'https://robohash.org/culpatotamest.png?size=50x50&set=set1',
            },
            {
              id: 8,
              displayName: 'Ynes Steeden',
              email: 'ysteeden7@ebay.co.uk',
              avatar: 'https://robohash.org/inciduntquisfugit.png?size=50x50&set=set1',
            },
            {
              id: 9,
              displayName: 'Daffy Rangle',
              email: 'drangle8@kickstarter.com',
              avatar: 'https://robohash.org/inciduntmaioresmollitia.png?size=50x50&set=set1',
            },
            {
              id: 10,
              displayName: 'Muhammad Newland',
              email: 'mnewland9@cbsnews.com',
              avatar: 'https://robohash.org/autemeligendinam.png?size=50x50&set=set1',
            },
          ],
          clips: [
            {
              title: 'in ante',
              coverImg: 'https://picsum.photos/240/320/?image=1084',
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
              coverImg: 'https://picsum.photos/240/320/?image=1083',
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
              coverImg: 'https://picsum.photos/240/320/?image=1081',
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
              coverImg: 'https://picsum.photos/240/320/?image=1079',
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
          title: 'massa id',
          members: [
            {
              id: 1,
              displayName: 'Mimi Dearl',
              email: 'mdearl0@example.com',
              avatar: 'https://robohash.org/praesentiumdoloremconsequuntur.png?size=50x50&set=set1',
            },
            {
              id: 2,
              displayName: 'Annadiana Barnshaw',
              email: 'abarnshaw1@xinhuanet.com',
              avatar: 'https://robohash.org/beataeaccusamusarchitecto.png?size=50x50&set=set1',
            },
            {
              id: 3,
              displayName: 'Xylia Figgs',
              email: 'xfiggs2@wordpress.org',
              avatar: 'https://robohash.org/autemvoluptatibusvoluptas.png?size=50x50&set=set1',
            },
            {
              id: 4,
              displayName: 'Tim Lynnitt',
              email: 'tlynnitt3@disqus.com',
              avatar: 'https://robohash.org/blanditiisrepudiandaesunt.png?size=50x50&set=set1',
            },
            {
              id: 5,
              displayName: 'Clarey Kilbey',
              email: 'ckilbey4@aol.com',
              avatar: 'https://robohash.org/excepturisuntdebitis.png?size=50x50&set=set1',
            },
            {
              id: 6,
              displayName: 'Arlee Scuffham',
              email: 'ascuffham5@skyrock.com',
              avatar: 'https://robohash.org/autquimodi.png?size=50x50&set=set1',
            },
            {
              id: 7,
              displayName: 'Starlene Tidcombe',
              email: 'stidcombe6@mediafire.com',
              avatar: 'https://robohash.org/culpatotamest.png?size=50x50&set=set1',
            },
            {
              id: 8,
              displayName: 'Ynes Steeden',
              email: 'ysteeden7@ebay.co.uk',
              avatar: 'https://robohash.org/inciduntquisfugit.png?size=50x50&set=set1',
            },
            {
              id: 9,
              displayName: 'Daffy Rangle',
              email: 'drangle8@kickstarter.com',
              avatar: 'https://robohash.org/inciduntmaioresmollitia.png?size=50x50&set=set1',
            },
            {
              id: 10,
              displayName: 'Muhammad Newland',
              email: 'mnewland9@cbsnews.com',
              avatar: 'https://robohash.org/autemeligendinam.png?size=50x50&set=set1',
            },
          ],
          clips: [
            {
              title: 'in ante',
              coverImg: 'https://picsum.photos/240/320/?image=1080',
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
              coverImg: 'https://picsum.photos/240/320/?image=1076',
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
              coverImg: 'https://picsum.photos/240/320/?image=1077',
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
              coverImg: 'https://picsum.photos/240/320/?image=1078',
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
        }, {
          id: 3,
          title: 'vel enim sit amet',
          members: [
            {
              id: 1,
              displayName: 'Mimi Dearl',
              email: 'mdearl0@example.com',
              avatar: 'https://robohash.org/praesentiumdoloremconsequuntur.png?size=50x50&set=set1',
            },
            {
              id: 2,
              displayName: 'Annadiana Barnshaw',
              email: 'abarnshaw1@xinhuanet.com',
              avatar: 'https://robohash.org/beataeaccusamusarchitecto.png?size=50x50&set=set1',
            },
            {
              id: 3,
              displayName: 'Xylia Figgs',
              email: 'xfiggs2@wordpress.org',
              avatar: 'https://robohash.org/autemvoluptatibusvoluptas.png?size=50x50&set=set1',
            },
            {
              id: 4,
              displayName: 'Tim Lynnitt',
              email: 'tlynnitt3@disqus.com',
              avatar: 'https://robohash.org/blanditiisrepudiandaesunt.png?size=50x50&set=set1',
            },
            {
              id: 5,
              displayName: 'Clarey Kilbey',
              email: 'ckilbey4@aol.com',
              avatar: 'https://robohash.org/excepturisuntdebitis.png?size=50x50&set=set1',
            },
            {
              id: 6,
              displayName: 'Arlee Scuffham',
              email: 'ascuffham5@skyrock.com',
              avatar: 'https://robohash.org/autquimodi.png?size=50x50&set=set1',
            },
            {
              id: 7,
              displayName: 'Starlene Tidcombe',
              email: 'stidcombe6@mediafire.com',
              avatar: 'https://robohash.org/culpatotamest.png?size=50x50&set=set1',
            },
            {
              id: 8,
              displayName: 'Ynes Steeden',
              email: 'ysteeden7@ebay.co.uk',
              avatar: 'https://robohash.org/inciduntquisfugit.png?size=50x50&set=set1',
            },
            {
              id: 9,
              displayName: 'Daffy Rangle',
              email: 'drangle8@kickstarter.com',
              avatar: 'https://robohash.org/inciduntmaioresmollitia.png?size=50x50&set=set1',
            },
            {
              id: 10,
              displayName: 'Muhammad Newland',
              email: 'mnewland9@cbsnews.com',
              avatar: 'https://robohash.org/autemeligendinam.png?size=50x50&set=set1',
            },
          ],
          clips: [
            {
              title: 'in ante',
              coverImg: 'https://picsum.photos/240/320/?image=1075',
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
              coverImg: 'https://picsum.photos/240/320/?image=1078',
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
              coverImg: 'https://picsum.photos/240/320/?image=1083',
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
              coverImg: 'https://picsum.photos/240/320/?image=1073',
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
        }, {
          id: 4,
          title: 'at nibh in hac',
          members: [
            {
              id: 1,
              displayName: 'Mimi Dearl',
              email: 'mdearl0@example.com',
              avatar: 'https://robohash.org/praesentiumdoloremconsequuntur.png?size=50x50&set=set1',
            },
            {
              id: 2,
              displayName: 'Annadiana Barnshaw',
              email: 'abarnshaw1@xinhuanet.com',
              avatar: 'https://robohash.org/beataeaccusamusarchitecto.png?size=50x50&set=set1',
            },
            {
              id: 3,
              displayName: 'Xylia Figgs',
              email: 'xfiggs2@wordpress.org',
              avatar: 'https://robohash.org/autemvoluptatibusvoluptas.png?size=50x50&set=set1',
            },
            {
              id: 4,
              displayName: 'Tim Lynnitt',
              email: 'tlynnitt3@disqus.com',
              avatar: 'https://robohash.org/blanditiisrepudiandaesunt.png?size=50x50&set=set1',
            },
            {
              id: 5,
              displayName: 'Clarey Kilbey',
              email: 'ckilbey4@aol.com',
              avatar: 'https://robohash.org/excepturisuntdebitis.png?size=50x50&set=set1',
            },
            {
              id: 6,
              displayName: 'Arlee Scuffham',
              email: 'ascuffham5@skyrock.com',
              avatar: 'https://robohash.org/autquimodi.png?size=50x50&set=set1',
            },
            {
              id: 7,
              displayName: 'Starlene Tidcombe',
              email: 'stidcombe6@mediafire.com',
              avatar: 'https://robohash.org/culpatotamest.png?size=50x50&set=set1',
            },
            {
              id: 8,
              displayName: 'Ynes Steeden',
              email: 'ysteeden7@ebay.co.uk',
              avatar: 'https://robohash.org/inciduntquisfugit.png?size=50x50&set=set1',
            },
            {
              id: 9,
              displayName: 'Daffy Rangle',
              email: 'drangle8@kickstarter.com',
              avatar: 'https://robohash.org/inciduntmaioresmollitia.png?size=50x50&set=set1',
            },
            {
              id: 10,
              displayName: 'Muhammad Newland',
              email: 'mnewland9@cbsnews.com',
              avatar: 'https://robohash.org/autemeligendinam.png?size=50x50&set=set1',
            },
          ],
          clips: [
            {
              title: 'in ante',
              coverImg: 'https://picsum.photos/240/320/?image=1064',
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
              coverImg: 'https://picsum.photos/240/320/?image=1063',
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
              coverImg: 'https://picsum.photos/240/320/?image=1066',
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
              coverImg: 'https://picsum.photos/240/320/?image=1065',
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
        }, {
          id: 5,
          title: 'libero quis orci',
          members: [
            {
              id: 1,
              displayName: 'Mimi Dearl',
              email: 'mdearl0@example.com',
              avatar: 'https://robohash.org/praesentiumdoloremconsequuntur.png?size=50x50&set=set1',
            },
            {
              id: 2,
              displayName: 'Annadiana Barnshaw',
              email: 'abarnshaw1@xinhuanet.com',
              avatar: 'https://robohash.org/beataeaccusamusarchitecto.png?size=50x50&set=set1',
            },
            {
              id: 3,
              displayName: 'Xylia Figgs',
              email: 'xfiggs2@wordpress.org',
              avatar: 'https://robohash.org/autemvoluptatibusvoluptas.png?size=50x50&set=set1',
            },
            {
              id: 4,
              displayName: 'Tim Lynnitt',
              email: 'tlynnitt3@disqus.com',
              avatar: 'https://robohash.org/blanditiisrepudiandaesunt.png?size=50x50&set=set1',
            },
            {
              id: 5,
              displayName: 'Clarey Kilbey',
              email: 'ckilbey4@aol.com',
              avatar: 'https://robohash.org/excepturisuntdebitis.png?size=50x50&set=set1',
            },
            {
              id: 6,
              displayName: 'Arlee Scuffham',
              email: 'ascuffham5@skyrock.com',
              avatar: 'https://robohash.org/autquimodi.png?size=50x50&set=set1',
            },
            {
              id: 7,
              displayName: 'Starlene Tidcombe',
              email: 'stidcombe6@mediafire.com',
              avatar: 'https://robohash.org/culpatotamest.png?size=50x50&set=set1',
            },
            {
              id: 8,
              displayName: 'Ynes Steeden',
              email: 'ysteeden7@ebay.co.uk',
              avatar: 'https://robohash.org/inciduntquisfugit.png?size=50x50&set=set1',
            },
            {
              id: 9,
              displayName: 'Daffy Rangle',
              email: 'drangle8@kickstarter.com',
              avatar: 'https://robohash.org/inciduntmaioresmollitia.png?size=50x50&set=set1',
            },
            {
              id: 10,
              displayName: 'Muhammad Newland',
              email: 'mnewland9@cbsnews.com',
              avatar: 'https://robohash.org/autemeligendinam.png?size=50x50&set=set1',
            },
          ],
          clips: [
            {
              title: 'in ante',
              coverImg: 'https://picsum.photos/240/320/?image=1061',
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
              coverImg: 'https://picsum.photos/240/320/?image=1060',
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
              coverImg: 'https://picsum.photos/240/320/?image=1059',
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
              coverImg: 'https://picsum.photos/240/320/?image=1058',
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
              coverImg: 'https://picsum.photos/240/320/?image=1057',
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

  .hf-circles
    display: flex
    flex-direction: column

</style>
