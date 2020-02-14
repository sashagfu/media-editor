<template>
  <div
    ref="playlists"
    class="Playlists playlists"
    @click.stop="toggleSaveMenu"
    @mouseover="getPosition"
  >
    <div class="playlists__icon">
      <font-awesome-icon
        :icon="[(playlists.length) ? 'fas' : 'far', 'bookmark']"
        :class="{
          'fa-icon--saved': playlists.length
        }"
        class="fa-icon fa-icon--pointer"
      />
    </div>
    <div
      :class="[
        'playlists__diamond',
        `playlists__diamond--${positions}`,
        {'playlists__diamond--show': showSaveMenu}
      ]"
    />
    <div
      :class="[
        'box',
        'playlists__dropdown',
        `playlists__dropdown--${positions}`,
        {'playlists__dropdown--show': showSaveMenu}
      ]"
    >
      <div class="dropdown__header">
        <div class="playlists__header">
          Save To Playlist
        </div>
      </div>
      <div class="dropdown__main">
        <el-checkbox-group
          v-model="playlists"
          class="playlists__input-box input-box"
        >
          <el-checkbox
            :label="0"
            class="input-box--checkbox"
          >
            Watch Later
          </el-checkbox>
          <template
            v-for="playlist in fetchPlaylists"
          >
            <template v-if="playlist.name !== 'Watch Later'">
              <el-checkbox
                :label="playlist.id"
                :key="playlist.id"
                class="input-box--checkbox"
                @change="handleCheck"
              >
                {{ playlist.name }}
              </el-checkbox>
            </template>
          </template>
        </el-checkbox-group>
      </div>
      <div class="dropdown__footer">
        <template
          v-if="!showForm"
        >
          <font-awesome-icon
            v-if="creatingPlaylist"
            :icon="['fas', 'spinner']"
            spin
            class="fa-icon"
          />
          <font-awesome-icon
            v-else
            :icon="['fas', 'plus']"
            class="fa-icon"
            @click="showForm = true;"
          />
        </template>
        <div
          v-if="showForm"
          class="dropdown__footer--input el-input">
          <input
            ref="playlistsForm"
            v-model="playlist.name"
            autofocus
            class="el-input__inner"
            @keyup.esc="showForm = false"
            @keyup.enter="createPlaylist"
          >
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

import FETCH_PLAYLISTS from 'Gql/playlists/queries/fetchPlaylists.graphql';
import ATTACH_PROJECT_TO_PLAYLIST from 'Gql/playlists/mutations/attachProjectToPlaylist.graphql';
import CREATE_PLAYLIST from 'Gql/playlists/mutations/createPlaylist.graphql';

export default {
  name: 'Playlists',
  props: {
    project: {
      type: Object,
      default: () => {
      },
    },
  },
  data: () => ({
    savedContent: false,
    showSaveMenu: false,
    positions: '',
    playlists: [],
    allowAdding: true,
    creatingPlaylist: false,

    // show form with new playlist name input
    showForm: false,

    playlist: {
      name: '',
    },
  }),
  computed: {
    ...mapGetters('general', ['activeUser']),
  },
  watch: {
    fetchPlaylists() {
      this.fetchPlaylists.forEach((p) => {
        const project = p.projects.find(pr => pr.id === this.project.id);
        if (!project) return;
        if (p.name === 'Watch Later') {
          this.playlists.push(0);
          return;
        }
        this.playlists.push(p.id);
      });
    },
  },
  methods: {
    async createPlaylist() {
      this.creatingPlaylist = true;
      this.showForm = false;
      await this.$apollo.mutate({
        mutation: CREATE_PLAYLIST,
        variables: {
          playlist: this.playlist,
        },
      });
      await this.$apollo.queries.fetchPlaylists.refetch();
      this.creatingPlaylist = false;
      this.playlist.name = '';
    },

    handleCheck() {
      this.$apollo.mutate({
        mutation: ATTACH_PROJECT_TO_PLAYLIST,
        variables: {
          projectId: this.project.id,
          playlists: this.playlists,
        },
      });
    },

    getPosition() {
      const { top } = this.$refs.playlists.getBoundingClientRect();
      if (top < 200) {
        this.positions = 'bottom';
      } else {
        this.positions = 'top';
      }
    },
    toggleSaveMenu() {
      this.showSaveMenu = !this.showSaveMenu;
    },
  },
  apollo: {
    fetchPlaylists: {
      query: FETCH_PLAYLISTS,
      variables() {
        return {
          userId: this.activeUser.id,
        };
      },
      skip() {
        return !this.activeUser.id;
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
    color: $text-light;
    transition: all .3s;
    font-size: 1rem;
    cursor: pointer;

    &:hover {
      color: $text-lighter;
    }

    &--saved {
      color: $red;

      &:hover {
        color: $red-hover;
      }
    }

    &--info {
      color: $info;
      margin: -2px 0;
    }

    &--saving {
      color: $primary;
      margin: -2px 0;
    }
  }

  .playlists {
    position: relative;
    height: 100%;
    padding: 0 4px;

    &__header {
      fnt($text-light, 12px, $weight-normal, left);
    }

    &__icon {
      cursor: pointer;
      display: flex;
      align-items: center;
      height: 100%;
    }

    &__diamond {
      display: none;
      background-color: $background-light;
      height: 8px;
      position: absolute;
      right: 3px;
      transform: rotate(225deg);
      width: 8px;
      z-index: 6;

      &--top {
        bottom: 20px;
        border-top: 1px solid $border;
        border-left: 1px solid $border;
      }

      &--bottom {
        top: 20px;
        border-bottom: 1px solid $border;
        border-right: 1px solid $border;
      }

      &--show {
        display: flex;
      }
    }

    & >>> .el-checkbox-group {
      border: none;
      padding-left: 0;
      margin-top: 10px;
    }

    & >>> .el-checkbox {
      margin-left: 0;
    }

    &__dropdown {
      display: none;
      flex-direction: column;
      align-items: start;
      position: absolute;
      z-index: 5;
      +tablet() {
        left: -8px;
      }
      +desktop() {
        right: -8px;
      }
      +widescreen() {
        right: -8px;
      }

      &--top {
        bottom: 24px;
      }

      &--bottom {
        top: 24px;
      }

      &--show {
        display: flex;
      }
    }

    &:hover > &__dropdown, &:hover > &__diamond {
      display: flex;
    }


    .dropdown {
      &__header {
        padding: 12px 16px;
        padding-bottom: 0;
        width: 100%;
      }

      &__main {
        padding: 0 16px;
      }

      &__footer {
        padding: 3px;
        height: 25px;
        border-top: 1px solid #e6e6e6;
        width: 100%;
        margin-top: 10px;

        &--input {
          height: 20px;

          & >>> .el-input__inner {
            border: none;
            height: 20px;
          }
        }

        .fa-icon {
          color: $grey-light;

          &:hover {
            color: $grey-lighter
          }
        }
      }
    }

    &__btn {
      margin: 0;
      min-width: 92px;

      &:not(:first-child) {
        margin-bottom: 2px;
      }

      &:not(:last-child) {
        margin-top: 2px;
      }

      &:hover {
        & .fa-icon {
          color: $white;
        }
      }
    }
  }

  .input-box {
    align-items: flex-start;
    background-color: $background-light;
    border-radius: $radius;
    border: 1px solid $border;
    display: flex;
    flex-direction: column;
    padding: 0 18px;
    position: relative;
    margin-top: 20px;

    &__label {
      fnt($text-light, 10px, $weight-normal, left);
      display: flex;
      flex-direction: column;
      height: 20px;
      justify-content: flex-end;
    }

    &__input {
      fnt($text-light, 12px, $weight-normal, left);
      border: none;
      height: 32px;
      outline: none;
      width: 100%;
      resize: none;
    }
  }
</style>
