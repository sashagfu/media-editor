<template>
  <div class="ProfilePageAbout pp-about">
    <div class="pp-about__column">
      <div class="pp-about__feed-item">
        <ProfilePageUserBox
          v-bind="$props"
        />
      </div>
      <ProfilePageTopSponsorsBox
        v-bind="$props"
      />
      <ProfilePageMySponsorsBox
        v-if="showTopSponsors"
        v-bind="$props"
      />
      <ProfilePageSponsorshipBox
        v-if="showSponsorship"
        v-bind="$props"
      />
    </div>
    <div class="pp-about__column pp-about__column--center">
      <div>
        <font-awesome-icon
          v-if="$apollo.queries.fetchProjects.loading"
          :icon="['fas', 'spinner']"
          spin
          class="fa-icon"
        />
      </div>
      <template v-if="fetchProjects.length">
        <div
          v-for="(item, key) in fetchProjects.slice(0, 3)"
          :key="key"
          :class="{ 'pinned': item.pinned }"
          class="pp-about__feed-item"
        >
          <HomeFeedItem
            :item="item"
            :user="user"
            :users-data="true"
            class="pp-about__feed-item-box"
          />
        </div>
      </template>
      <div
        v-if="!$apollo.queries.fetchProjects.loading && !fetchProjects.length"
        class="pp-about__no-feed-message"
      >
        <span>
          There are no projects yet
        </span>
      </div>
    </div>
    <div class="pp-about__column"/>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import { has } from 'lodash';

import FETCH_PROJECTS from 'Gql/projects/queries/fetchProjects.graphql';

import HomeFeedItem from 'Pages/MediaItem/HomeFeedItem';

import ProfilePageUserBox from './ProfilePageUserBox';
import ProfilePageTopSponsorsBox from './ProfilePageTopSponsorsBox';
import ProfilePageSponsorshipBox from './ProfilePageSponsorshipBox';
import ProfilePageMySponsorsBox from './ProfilePageMySponsorsBox';

export default {
  name: 'ProfilePageAbout',
  components: {
    ProfilePageUserBox,
    ProfilePageTopSponsorsBox,
    ProfilePageSponsorshipBox,
    ProfilePageMySponsorsBox,

    HomeFeedItem,
  },
  props: {
    user: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      fetchProjects: [],
      clips: [
        {
          title: 'in ante',
          coverImg: 'https://picsum.photos/320/880/?image=1083',
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
          coverImg: 'https://picsum.photos/320/880/?image=1084',
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
          coverImg: 'https://picsum.photos/320/880/?image=1080',
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
          coverImg: 'https://picsum.photos/320/880/?image=1074',
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
          coverImg: 'https://picsum.photos/320/880/?image=1073',
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
        {
          title: 'quam',
          coverImg: 'https://picsum.photos/320/880/?image=1069',
          views: 436,
          clipped: 34,
          starred: 456,
          comment: 23,
          author: {
            id: 6,
            displayName: 'Anatola Traite',
            avatar: 'https://robohash.org/doloremaspernaturconsequatur.png?size=50x50&set=set1',
            dataRegistration: '10/17/2016',
          },
        },
        {
          title: 'luctus et',
          coverImg: 'https://picsum.photos/320/880/?image=1063',
          views: 145,
          clipped: 34,
          starred: 456,
          comment: 23,
          author: {
            id: 7,
            displayName: 'Johnny Sackes',
            avatar: 'https://robohash.org/autoditrepellat.png?size=50x50&set=set1',
            dataRegistration: '12/01/2016',
          },
        },
        {
          title: 'primis in',
          coverImg: 'https://picsum.photos/320/880/?image=1066',
          views: 234,
          clipped: 34,
          starred: 456,
          comment: 23,
          author: {
            id: 8,
            displayName: 'Eli Lemon',
            avatar: 'https://robohash.org/blanditiislaboreveniam.png?size=50x50&set=set1',
            dataRegistration: '08/08/2017',
          },
        },
        {
          title: 'habitasse platea',
          coverImg: 'https://picsum.photos/320/880/?image=1065',
          views: 543,
          clipped: 34,
          starred: 456,
          comment: 23,
          author: {
            id: 9,
            displayName: 'Michal Fibbens',
            avatar: 'https://robohash.org/nullapraesentiuma.png?size=50x50&set=set1',
            dataRegistration: '01/13/2017',
          },
        },
        {
          title: 'gravida sem praesent',
          coverImg: 'https://picsum.photos/320/880/?image=1050',
          views: 423,
          clipped: 34,
          starred: 456,
          comment: 23,
          author: {
            id: 10,
            displayName: 'Una Meecher',
            avatar: 'https://robohash.org/eumearumquisquam.png?size=50x50&set=set1',
            dataRegistration: '09/22/2017',
          },
        },
        {
          title: 'primis in',
          coverImg: 'https://picsum.photos/320/880/?image=1043',
          views: 234,
          clipped: 34,
          starred: 456,
          comment: 23,
          author: {
            id: 8,
            displayName: 'Eli Lemon',
            avatar: 'https://robohash.org/blanditiislaboreveniam.png?size=50x50&set=set1',
            dataRegistration: '08/08/2017',
          },
        },
        {
          title: 'habitasse platea',
          coverImg: 'https://picsum.photos/320/880/?image=1039',
          views: 543,
          clipped: 34,
          starred: 456,
          comment: 23,
          author: {
            id: 9,
            displayName: 'Michal Fibbens',
            avatar: 'https://robohash.org/nullapraesentiuma.png?size=50x50&set=set1',
            dataRegistration: '01/13/2017',
          },
        },
        {
          title: 'gravida sem praesent',
          coverImg: 'https://picsum.photos/320/880/?image=1041',
          views: 423,
          clipped: 34,
          starred: 456,
          comment: 23,
          author: {
            id: 10,
            displayName: 'Una Meecher',
            avatar: 'https://robohash.org/eumearumquisquam.png?size=50x50&set=set1',
            dataRegistration: '09/22/2017',
          },
        },
      ],
      fetchUser: {},
      userSettings: {
        privacy: {
          showTopSponsors: false,
          showSponsorship: false,
        },
      },
    };
  },
  computed: {
    ...mapGetters('general', [
      'activeUser',
    ]),
    isMine() {
      return this.user.uuid === this.activeUser.uuid;
    },
    showTopSponsors() {
      return this.userSettings.privacy.showTopSponsors || this.isMine;
    },
    showSponsorship() {
      return this.userSettings.privacy.showSponsorship || this.isMine;
    },
  },
  watch: {
    user: {
      handler() {
        if (has(this.user, 'settings')) {
          const privacyKeys = Object.keys(this.user.settings.privacy);
          privacyKeys.forEach((key) => {
            if (key !== '__typename') {
              this.userSettings.privacy[key] = this.user.settings.privacy[key];
            }
          });
        }
      },
    },
  },
  apollo: {
    fetchProjects: {
      query: FETCH_PROJECTS,
      variables() {
        return {
          userId: this.user.id,
          status: 'published',
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
  @import '../../../../../sass/front/components/bulma-theme';

  .fa-icon {
    fnt($text-light, 1rem, $weight-normal, center)
  }

  .pp-about {
    display: flex;

    &__column {
      display: flex;
      flex-direction: column;
      width: 25%;
      min-height: 100px;
      &:first-child {
        padding-right: 13px;
      }
      &:last-child {
        padding-left: 13px;
      }
      &--center {
        width: 50%;
        padding: 0 13px;
      }
    }
    &__feed-item {
      min-height: 400px;
      position: relative;
      &:not(:last-child) {
        margin-bottom: 26px;
      }
    }
    .pinned {
      box-shadow: 0 6px 6px -6px black;
    }
    &__feed-item-box {
      position: absolute;
    }
    &__feed-item {
      min-height: 400px;
      position: relative;
      &:not(:last-child) {
        margin-bottom: 26px;
      }
    }
    &__feed-item-box {
      position: absolute;
    }
    &__no-feed-message {
      display flex
      justify-content center
      color #dbdbdb
      height 80px
      align-items center
      font-weight bold
    }
  }

</style>
