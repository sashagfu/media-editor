<template>
  <div class="ProfilePageMySponsorsBox pp-sp-box box">
    <div class="pp-sp-box__header">
      <div class="pp-sp-box__left">
        <div class="pp-sp-box__header-title">
          <template v-if="isMine">
            {{ trans('profile_page.my_sponsors') }}
          </template>
          <template v-else>
            {{ user.displayName || user.display_name }}{{ trans('profile_page.s_sponsors') }}
          </template>
        </div>
      </div>
      <div class="pp-sp-box__right"/>
    </div>
    <div class="pp-sp-box__main">
      <div
        ref="row"
        :style="{'height': `${size}px`}"
        class="pp-sp-box__row"
      >
        <div
          v-for="(pSponsors, index) in paginationSponsors"
          :key="`psp-${index}`"
          :class="['pp-sp-box__item-box', `pp-sp-box__item-box--${index}`]"
          :style="{'width': `${rowsWidth - (size * (1.2 * index))}px`}"
        >
          <div
            v-for="(sponsor, key) in pSponsors"
            :key="`sp-${key}`"
            class="pp-sp-box__item"
          >
            <el-tooltip
              :visible-arrow="false"
              class="item"
              effect="dark"
              placement="bottom"
              popper-class="pp-sp-box__tooltip"
            >
              <div slot="content">
                <div class="pp-sp-box__tooltip-title">
                  {{ sponsor.displayName }}
                </div>
                <div class="pp-sp-box__tooltip-sub-title">
                  {{ trans('profile_page.donated') }}: ${{ sponsor.donated }}
                </div>
              </div>
              <div
                :style="{
                  'background-image': `url(${sponsor.avatar})`,
                  'height': `${size + (10 * index)}px`,
                  'width': `${size + (10 * index)}px`,
                }"
                class="pp-sp-box__avatar"
                @click="goToSponsor(sponsor)"
              />
            </el-tooltip>
          </div>
        </div>
      </div>
    </div>
    <div
      v-if="isMine"
      class="pp-sp-box__footer"
    >
      <div
        class="pp-sp-box__btn"
        @click="showMore"
      >
        {{ trans('profile_page.show_more') }}
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  name: 'ProfilePageMySponsorsBox',
  components: {},
  props: {
    user: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      size: 0,
      rowsWidth: 0,
    };
  },
  computed: {
    ...mapGetters('general', [
      'activeUser',
    ]),
    isMine() {
      return this.user.uuid === this.activeUser.uuid;
      // return false;
    },
    mySponsors() {
      const sponsors = this.user.sponsors.concat();
      return sponsors.splice(0, 5)
        .reverse();
    },
    paginationSponsors() {
      const sponsors = [];
      for (let i = 0; i < this.mySponsors.length; i += 2) {
        sponsors.push(this.mySponsors.slice(i, i + 2));
      }
      return sponsors;
    },
  },
  mounted() {
    const { width } = this.$refs.row.getBoundingClientRect();
    this.size = width / 3.90625;
    this.rowsWidth = width;
  },
  methods: {
    showMore() {
      this.$router.push({
        name: 'DonationsPage',
        hash: '#my-sponsors',
      });
    },
    goToSponsor(sponsor) {
      this.$router.push({
        name: 'ProfilePage',
        params: {
          username: sponsor.username,
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
  @import '../../../../../sass/front/components/bulma-theme';

  .fa-icon {
    fnt($text-light, 14px, $weight-light, left);
    transition: all .3s;

    &--star {
      color: $yellow;
    }

    &--trophy {
      color: $white;
      font-size: 10px;
    }
  }

  .pp-sp-box {
    &__header {
      fl-between();
      height: 60px;
      padding: 0 26px;
      width: 100%;
    }

    &__left {
      @include fl-left();
    }

    &__right {
      fl-right();
    }

    &__header-title {
      fnt($text, 14px, $weight-semibold, left);
    }

    &__main {
      padding: 0 26px;

      &:last-child {
        padding-bottom: 26px;
      }
    }

    &__footer {
      fl-center();
      height: 60px;
      padding: 0 26px;
    }

    &__btn {
      fnt($primary, 14px, $weight-normal, right);
      cursor: pointer;
      transition: all .3s;
      width: 100%;

      &:hover {
        opacity: 0.8;
      }
    }

    &__row {
      fl-center();
      flex-direction: row-reverse;
      position: relative;
    }

    &__item {
      cursor: pointer;
      transition: all 0.3s;

      &:hover {
        margin-top: -20px;
        padding-bottom: 20px;
        z-index: 1;
      }
    }

    &__item-box {
      fl-between();
      position: absolute;

      &--2 {
        fl-center();
      }
    }

    &__avatar {
      background: center center / cover no-repeat $grey-lighter;
      border-radius: 50%;
      border: 2px solid $white;
      flex: 0 0 auto;
    }

    &__title-box {
      padding-left: 12px;
    }

    &__title {
      fnt($text, 14px, $weight-normal, left);
    }

    &__sub-title {
      fnt($text-light, 12px, $weight-normal, left);
    }

  }
</style>

<style lang="stylus">
  @import '../../../../../sass/front/components/bulma-theme';

  .pp-sp-box {
    &__tooltip {
      padding: 2px 8px !important;
      border: 1px solid $border !important;
      background-color: $white !important;
      border-radius: $radius !important;
    }

    &__tooltip-title {
      fnt($text-light, 14px, $weight-normal, center);
    }

    &__tooltip-sub-title {
      fnt($text-lighter, 12px, $weight-normal, center);
    }
  }
</style>
