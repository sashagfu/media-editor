<template>
  <div class="psp__main">
    <ProjectStatisticsPageHeader
      v-if="fetchProject"
      :project="fetchProject"
    />
    <div class="psp__main-row">
      <div class="psp__column psp__column--left box psp__clips clips">
        <template v-if="$apollo.queries.fetchProject.loading">
          <div class="psp__loading">
            <font-awesome-icon
              :icon="['fas', 'spinner']"
              spin
              class="fa-icon"
            />
          </div>
        </template>
        <template v-else>
          <div class="clips__head">
            <template
              v-if="fetchProject.credits"
            >
              {{ fetchProject.credits.length }}
              {{ trans('statistics_page.clips_count') }}
            </template>
            <template v-else>
              {{ trans('statistics_page.no_clips') }}
            </template>
          </div>
          <div
            v-if="fetchProject.credits"
            class="clips__list"
          >
            <ProjectStatisticsClipItem
              v-for="(credit, index) in fetchProject.credits"
              :key="index"
              :credit="credit"
              :project="credit.details.project"
              class="clips__item item"
            />
          </div>
        </template>
      </div>
      <div class="psp__column psp__column--right box">
        <template v-if="$apollo.queries.fetchProject.loading">
          <div class="psp__loading">
            <font-awesome-icon
              :icon="['fas', 'spinner']"
              spin
              class="fa-icon"
            />
          </div>
        </template>
        <template v-else>
          <div class="clips__head">
            <template
              v-if="fetchProject.foreignCredits.length"
            >
              {{ fetchProject.foreignCredits.length }}
              {{ trans('statistics_page.uses_count') }}
            </template>
            <template v-else>
              {{ trans('statistics_page.no_uses') }}
            </template>
          </div>
          <div
            v-if="fetchProject.foreignCredits.length"
            class="clips__list"
          >
            <template
              v-for="foreignCredits in fetchProject.foreignCredits"
            >
              <ProjectStatisticsClipItem
                v-for="(credit, index) in foreignCredits.credits"
                :key="index"
                :credit="credit"
                :project="foreignCredits.project"
                class="clips__item item"
              />
            </template>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

import FETCH_PROJECT from 'Gql/projects/queries/fetchProject.graphql';

import TopBar from '../TopBar/TopBar';
import LeftSidebar from '../LeftSidebar/LeftSidebar';
import RightSidebar from '../RightSidebar/RightSidebar';
import ProjectStatisticsPageHeader from './ProjectStatisticsPageHeader';
import ProjectStatisticsClipItem from './ProjectStatisticsClipItem';

export default {
  name: 'ProjectStatisticsPage',
  components: {
    TopBar,
    LeftSidebar,
    RightSidebar,
    ProjectStatisticsPageHeader,
    ProjectStatisticsClipItem,
  },
  computed: {
    ...mapGetters('general', [
      'activeUser',
    ]),
  },
  apollo: {
    fetchProject: {
      query: FETCH_PROJECT,
      variables() {
        return {
          uuid: this.$route.params.projectUuid,
        };
      },
    },
  },
};
</script>

<style lang="stylus">
  .el-progress
    width 175px;
</style>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .fa-icon
    color $text-light
    transition all .3s
    font-size 1rem
    &:hover
      color $text-lighter
    &--pointer
      cursor pointer
    &--liked
      color $secondary
      &:hover
        color $coral-hover
    &--starred
      color $primary
      &:hover
        color $lime-hover
    &__box
      padding 0 4px 0

  .psp
    display flex
    flex-direction column
    min-height 100vh
    position relative
    &__body
      background-color $white-bis
      display flex
      flex-grow 1
      padding-top 73px
      position  relative

    &__main
      display flex
      margin-left 72px
      flex-direction column
      margin-bottom 1.5rem
      width 100%
    &__main-row
      display flex
      padding 32px 32px 0 28px
      margin-bottom 1.5rem
      width 100%
    &__column
      display flex
      flex-direction column
      min-height 100px
      &:first-child
        padding-left 0
        margin-right: 28px;
      &:last-child
        padding-right 0
      &--left
        width 50%
        margin-bottom: 0;
      &--right
        width: 50%
    &__loading
      fl-center()
      height 120px

  .clips
    &__head
      border-bottom 1px solid #e6e6e6
      padding 10px 0
      width 100%
      text-align left
      padding-left 20px
    &__list
      display flex
      flex-direction column
      align-items start

</style>
