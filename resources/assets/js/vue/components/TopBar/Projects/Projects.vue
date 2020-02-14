<template>
  <div class="Projects projects">
    <a
      class="fa-box fa-icon--pointer"
      @click="goToProjects"
    >
      <!--      <font-awesome-icon-->
      <!--        :icon="['fas', 'video']"-->
      <!--        class="fa-icon"-->
      <!--      />-->
      <span>
        <svg
          viewBox="0 0 576 512"
          class="fa-icon svg-inline--fa fa-video fa-w-18">
          <path
            fill="currentColor"
            d="M543.9 96c-6.2 0-12.5 1.8-18.2 5.7L416
              170.1v-58.3c0-26.4-23.2-47.8-51.8-47.8H51.8C23.2
              64 0 85.4 0 111.8v288.4C0 426.6 23.2 448 51.8 448h312.4c28.6 0 51.8-21.4
              51.8-47.8v-58.3l109.7 68.3c5.7 4 12.1 5.7 18.2 5.7 16.6 0 32.1-13 32.1-31.5V127.5C576
              109 560.5 96 543.9 96zM368 200v198.9c-.6.4-1.8 1.1-3.8 1.1H51.8c-2
              0-3.2-.6-3.8-1.1V113.1c.6-.4 1.8-1.1 3.8-1.1h312.4c2 0 3.2.6 3.8 1.1V200zm160
              155.2l-112-69.8v-58.7l112-69.8v198.3z"
            class=""/>
        </svg>
      </span>
    </a>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  name: 'Projects',
  computed: {
    ...mapGetters('general', [
      'activeUser',
    ]),
  },
  methods: {
    goToProjects() {
      this.$router.push({
        name: 'ProfilePage',
        hash: '#projects',
        params: { username: this.activeUser.username },
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
    color: $text-light;
    font-size: 1.5rem;
    transition: all .3s;

    &:hover {
      color: $text-lighter;
    }

    &--invert {
      color: $white;

      &:hover {
        color: $white-bis;
      }
    }

    &--pointer {
      cursor: pointer;
    }

    &--disabled {
      color: $grey-lighter;
      cursor: auto;

      &:hover {
        color: $grey-lighter;
      }
    }

    &__box {
      padding: 0 4px 0;
    }
  }

  .projects {
    fl-center();
    height: 72px;
    width: 62px;
  }
</style>
