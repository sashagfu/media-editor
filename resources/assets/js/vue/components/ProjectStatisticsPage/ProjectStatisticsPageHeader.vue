<template>
  <div class="ProjectStatisticsPageHeader psp__head head box">
    <div class="head__title"><span>{{ project.title }}</span></div>
    <div class="head__row">
      <div class="head__date">created at: 01/05/2019</div>
      <div class="head__duration">duration: {{ duration }}</div>
    </div>
    <div class="head__row">
      <div class="head__views">
        <div>
          <font-awesome-icon
            :icon="['fas', 'eye']"
            class="fa-icon"
          />
        </div>
        <div
        >
          {{ project.viewsCount }}
        </div>
      </div>
      <div class="head__stars">
        <div>
          <font-awesome-icon
            :icon="['far', 'star']"
            class="fa-icon"
          />
        </div>
        <div
        >
          {{ project.stars.length }}
        </div>
      </div>
      <div class="head__comments">
        <div>
          <font-awesome-icon
            :icon="['far', 'comments']"
            class="fa-icon"
          />
        </div>
        <div
        >
          {{ project.comments.length }}
        </div>
      </div>
      <div class="head__clips">
        <div>
          <font-awesome-icon
            :icon="['fas', 'paperclip']"
            class="fa-icon"
          />
        </div>
        <div
        >
          {{ project.clipsCount }}
        </div>
      </div>
    </div>
    <div class="head__credits">
      You own {{ owningData.percentages }}% of this project: {{ owningData.time }} of media uploaded
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import * as constants from 'Helpers/constants';
import moment from 'moment';

export default {
  name: 'ProjectStatisticsPageHeader',
  props: {
    project: {
      type: Object,
      default: () => {},
    },
  },
  computed: {
    ...mapGetters('general', ['activeUser']),
    asset() {
      return this.project.assets.find(a => a.type === constants.FULL);
    },
    duration() {
      if (moment.duration(this.asset.time).asHours() > 1) {
        return moment.utc(moment.duration(this.asset.time).asMilliseconds()).format('HH:mm:ss');
      }
      return moment.utc(moment.duration(this.asset.time).asMilliseconds()).format('mm:ss');
    },
    owningData() {
      // If auth user is not author of the project
      if (this.project.author.id !== this.activeUser.id) {
        return this.getUserOwnership();
      }
      return this.getAuthorOwnership();
    },
  },
  methods: {
    getUserOwnership() {
      let percentages = 0;
      let time = 0;
      if (!this.project.credits) {
        return percentages;
      }
      this.project.credits.map((cr) => {
        if (cr.details.author.id === this.activeUser.id) {
          percentages += cr.details.percentages;
          time += (cr.details.to - cr.details.from);
          return cr;
        }
        return cr;
      });

      time = moment.utc(time).format('mm:ss');
      return {
        percentages,
        time,
      };
    },
    getAuthorOwnership() {
      let percentages = 100;
      let time = this.project.duration;
      if (!this.project.credits) {
        return percentages;
      }
      this.project.credits.map((cr) => {
        if (cr.details.author.id === this.project.author.id) {
          return cr;
        }
        percentages -= cr.details.percentages;
        time -= (cr.details.to - cr.details.from);
        return cr;
      });

      time = moment.utc(time).format('mm:ss');
      return {
        percentages,
        time,
      };
    },
  },
};
</script>

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

  .head
    display flex
    margin-bottom 1.5rem
    flex-direction column
    line-height 30px
    align-items center
    padding 20px

    div
      margin 0 5px
    &__title
      color: $grey-light
      font-size 30px
      font-weight bold
      span
        color: $primary
    &__row
      display flex
      justify-content center
      fnt($text-light, 14px, $weight-normal, left)
    &__date
      fnt($blue, 12px, $weight-light, left)
      padding 5px
    &__duration
      fnt($text, 12px, $weight-light, left)
      padding 5px
    &__comments
      display flex
    &__views
      display flex
    &__stars
      display flex
    &__clips
      display flex
    &__credits
      fnt($grey-light, 14px, $weight-light, center)
</style>
