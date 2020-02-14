<template>
  <div class="SearchResultTags sr-t">
    <SearchResultBar v-if="$apollo.queries.searchProjects.loading">
      <template slot="header">
        <div class="srb-h__title">
          Searching projects related to
          <span
            v-for="tag in searchQuery.split(' ')"
            :key="tag"
            class="srb-h__search"
          >
            "{{ tag }}"
          </span> {{ (searchQuery.split(' ').length > 1) ? 'tags...' : 'tag...' }}
        </div>
      </template>
    </SearchResultBar>
    <!--Show it if no matches found-->
    <SearchResultBar v-if="!$apollo.queries.searchProjects.loading && !searchProjects.tags.length">
      <template slot="header">
        <div class="srb-h__title">
          No matches found for
          <span
            v-for="tag in searchQuery.split(' ')"
            :key="tag"
            class="srb-h__search"
          >
            "{{ tag }}"
          </span> {{ (searchQuery.split(' ').length > 1) ? 'tags...' : 'tag...' }}
        </div>
      </template>
    </SearchResultBar>
    <div
      v-if="$apollo.queries.searchProjects.loading"
      class="sr-t__spinner"
    >
      <font-awesome-icon
        :icon="['fas', 'spinner']"
        spin
        class="fa-icon"
      />
    </div>
    <template v-if="searchProjects.tags.length">
      <template
        v-for="tagsItem in searchProjects.tags"
      >
        <SearchResultBar
          :key="`tib-${tagsItem.id}`"
        >
          <template slot="header">
            <div class="srb-h__title">
              Show {{ tagsItem.projects.length }} Projects results of for tag:
              <span class="srb-h__search">
                "{{ tagsItem.name }}"
              </span>
            </div>
          </template>
        </SearchResultBar>
        <div
          :key="`tim-${tagsItem.id}`"
          class="sr-t__main"
        >
          <div
            v-for="item in tagsItem.projects"
            :key="`pi-${item.id}`"
            class="sr-t__item"
          >
            <HomeFeedItem
              :item="item"
              :users-data="showUsersData"
            />
          </div>
        </div>
      </template>
    </template>
    <!-- <div
      v-if="showBtnShowMore"
      class="sr-t__show-more"
    >
      <ShowMoreButton
        @show-more="showMore"
      />
    </div> -->
  </div>
</template>

<script>
import SEARCH_PROJECTS from 'Gql/projects/queries/searchProjects.graphql';

import HomeFeedItem from 'Pages/MediaItem/HomeFeedItemMini';

import ShowMoreButton from 'Pages/Common/ShowMoreButton';
import SearchResultBar from './SearchResultBar';

export default {
  name: 'SearchResultTags',
  components: {
    ShowMoreButton,
    SearchResultBar,
    HomeFeedItem,
  },
  data() {
    return {
      searchQuery: '',
      qtyItemShow: 5,
      searchProjects: {
        tags: [],
      },
      showUsersData: true,
      fkTags: [
        {
          id: 0,
          name: 'live',
          projects: [
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
          ],
        },
        {
          id: 1,
          name: 'concert',
          projects: [
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
        },
      ],
    };
  },
  created() {
    this.searchQuery = this.$route.query.q;
  },
  apollo: {
    searchProjects: {
      query: SEARCH_PROJECTS,
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
      fnt($danger, 14px, $weight-semibold, left);
      text-transform: capitalize;
    }
  }

  .sr-t {
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
    }
    &__main {
      fl-left();
      flex-wrap: wrap;
      margin-right: -18px;
    }
    &__item {
      width: 20%;
      height: 292px;
      padding: 0 18px 26px 0;
    }
    &__show-more {
      fl-center();
    }
  }

</style>
