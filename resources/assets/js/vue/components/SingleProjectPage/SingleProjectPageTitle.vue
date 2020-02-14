<template>
  <div class="SingleProjectPageTitle box spt">
    <div class="spt__left">
      <div class="spt__title">
        {{ project.title }}
      </div>
      <div class="spt__sub-title">
        created at: {{ dateDecorate(project.updatedAt) }}
      </div>
      <Tags
        v-if="project.tags.length"
        :tags="project.tags"
      />
      <Credits
        v-if="asset.credits && asset.credits.length"
        :credits="asset.credits"
      />
      <div class="spt__descriptions">
        <!--{{ project.description }}-->
        <TruncateText
          :text="project.description"
          :length="100"
          more="... Read More"
        />
      </div>
    </div>
    <div class="spt__right">
      <div class="spt__item">
        <Likes v-bind="$props"/>
      </div>
      <div class="spt__item">
        <ShowComments :project="project"/>
      </div>
      <div class="spt__item">
        <Clip :project="project"/>
      </div>
      <div class="spt__item">
        <PostShares :project="project"/>
      </div>
      <div class="spt__item">
        <Playlists :project="project"/>
      </div>
      <div
        v-if="isStatisticsAllowed"
        class="spt__item"
      >
        <div class="spt__statistics">
          <font-awesome-icon
            :icon="['far', 'chart-bar']"
            class="fa-icon fa-icon--pointer"
            @click="goToProjectStatistics"
          />
        </div>
      </div>
      <template v-else>
        <div class="spt__item">
          <Flags
            :project="project"
          />
        </div>
      </template>
    </div>
  </div>
</template>

<script>
import moment from 'moment';

import * as constants from 'Helpers/constants';
import TruncateText from 'Helpers/TruncateText';


import Credits from 'Pages/ProjectsContent/Credits';
import Likes from 'Pages/ProjectsContent/footerComponents/Likes';
import PostShares from 'Pages/ProjectsContent/footerComponents/PostShares';
import Clip from 'Pages/ProjectsContent/footerComponents/Clip';
import Playlists from 'Pages/ProjectsContent/footerComponents/Playlists';
import ShowComments from 'Pages/ProjectsContent/footerComponents/ShowComments';
import Flags from 'Pages/ProjectsContent/footerComponents/Flags';
import Tags from 'Pages/ProjectsContent/Tags';

export default {
  name: 'SingleProjectPageTitle',
  components: {
    TruncateText,
    Credits,
    Likes,
    PostShares,
    Clip,
    Playlists,
    ShowComments,
    Flags,
    Tags,
  },
  props: {
    project: {
      type: Object,
      default: () => ({}),
    },
    user: {
      type: Object,
      default() {
        return {};
      },
    },
  },
  computed: {
    asset() {
      return this.project.assets.find(a => a.type === constants.FULL);
    },
    isMine() {
      return this.user.id === this.project.author.id;
    },
    isStatisticsAllowed() {
      // Check if this project has credits from auth user
      return !!this.project.credits.find(cr => cr.details.author.id === this.user.id);
    },
  },
  methods: {
    dateDecorate(date) {
      return moment(date)
        .format('MM/DD/YYYY');
    },
    goToProjectStatistics() {
      this.$router.push({
        name: 'ProjectStatisticsPage',
        params: {
          projectUuid: this.project.uuid,
        },
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
    color: $text-light;
    transition: all .3s;
    font-size: 1rem;

    &:hover {
      color: $text-lighter;
    }

    &--saved {
      color: $primary;
    }

    &--info {
      color: $info;
      margin: -2px 0;
    }

    &--saving {
      color: $primary;
      margin: -2px 0;
    }

    &--pointer {
      cursor: pointer;
    }
  }

  .spt // single project title
    fl-between()
    align-items flex-start
    padding 16px 26px

    &__left
      display flex
      flex-direction column

    &__right
      display flex
      justify-content flex-end

    &__title
      fnt($text, 16px, $weight-semibold, left)
      margin-bottom 8px

    &__sub-title
      fnt($primary, 12px, $weight-light, left)
      margin-bottom 4px

    &__descriptions
      fnt($text, 12px, $weight-light, left)

    &__item + &__item
      margin-left 8px

    &__statistics
      margin-left 10px

</style>
