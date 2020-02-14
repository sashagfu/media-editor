<template>
  <div class="HomeFeedItem hf-item box is-marginless">
    <div
      v-if="usersData"
      class="hf-item__header">
      <div class="hf-item__left">
        <div class="hf-item__title">
          {{ item.title }}
        </div>
      </div>
      <div class="hf-item__right row">
        <div class="hf-item__views">
          {{ item.viewsCount }} views
        </div>
        <div
          v-if="activeUser.id === user.id"
          class="hf-item__actions actions"
        >
          <el-dropdown
            trigger="hover"
            size="small"
            @command="handleCommand"
          >
            <div class="actions__dropdown-button">
              <font-awesome-icon
                :icon="['fas', 'ellipsis-h']"
                class="fa-icon fa-icon--invert"
              />
            </div>
            <el-dropdown-menu slot="dropdown">
              <el-dropdown-item
                v-if="!item.pinned"
                command="pin"
              >
                {{ trans('profile_page.pin') }}
              </el-dropdown-item>
              <el-dropdown-item
                v-else
                command="unpin"
              >
                {{ trans('profile_page.unpin') }}
              </el-dropdown-item>
            </el-dropdown-menu>
          </el-dropdown>
        </div>
      </div>
    </div>
    <div
      :style="{'background-image': `url(${item.thumbPath})`}"
      class="hf-item__main"
      @click="goToProject"
    >
      <div class="hf-item__play-btn">
        <font-awesome-icon
          :icon="['fas', 'play']"
          class="fa-icon fa-icon--play"
        />
      </div>
    </div>
    <div
      v-if="withFooter"
      class="hf-item__footer">
      <div class="hf-item__left hf-item__left--top">
        <div
          v-if="usersData"
          :style="{'background-image': `url(${item.author.avatar})`}"
          class="hf-item__avatar"
          @click="goToUser"
        />
        <div class="hf-item__name-box">
          <div
            v-if="usersData"
            class="hf-item__name"
            @click="goToUser"
          >
            {{ item.author.displayName }}
          </div>
          <div
            v-else
            class="hf-item__title"
          >
            {{ item.title }}
          </div>
          <div class="hf-item__sub-name">
            <template v-if="usersData">
              {{ trans('home_projects.on_actionlime') }}
              {{ formatData(item.author.createdAt) }}
            </template>
            <template v-else>
              {{ item.views }} views
            </template>
          </div>
        </div>
      </div>
      <div class="hf-item__right hf-item__right--top">
        <div class="hf-item__action-box">
          <div class="hf-item__action-item">
            <font-awesome-icon
              :icon="['far', 'comments']"
              class="fa-icon "
            />
            <div class="hf-item__action-qty">
              {{ item.comments.length }}
            </div>
          </div>
          <div class="hf-item__action-item">
            <font-awesome-icon
              :icon="['far', 'star']"
              class="fa-icon "
            />
            <div class="hf-item__action-qty">
              {{ item.stars.length }}
            </div>
          </div>
          <div class="hf-item__action-item">
            <font-awesome-icon
              :icon="['fas', 'paperclip']"
              class="fa-icon "
            />
            <div class="hf-item__action-qty">
              {{ item.clipsCount }}
            </div>
          </div>
        </div>
        <div
          v-if="item.pinned"
          class="hf-item__pinned"
        >
          <font-awesome-icon
            :icon="['fas', 'thumbtack']"
            class="fa-icon fa-icon--light"
            @click="handleCommand('unpin')"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment';
import { mapGetters } from 'vuex';

import TOGGLE_PIN_PROJECT from 'Gql/projects/mutations/togglePinProject.graphql';
import FETCH_PROJECTS from 'Gql/projects/queries/fetchProjects.graphql';

export default {
  name: 'HomeFeedItem',
  props: {
    item: {
      type: Object,
      default: () => ({}),
    },
    user: {
      type: Object,
      default: () => ({}),
    },
    usersData: {
      type: Boolean,
      default: true,
    },
    withFooter: {
      type: Boolean,
      default: true,
    },
  },
  computed: {
    ...mapGetters('general', ['activeUser']),
  },
  methods: {
    goToProject() {
      this.$router.push({
        name: 'SingleProjectPage',
        params: {
          projectUuid: this.item.uuid,
        },
      });
    },
    goToUser() {
      this.$router.push({
        name: 'ProfilePage',
        params: {
          username: this.item.author.username,
        },
      });
    },
    formatData(dt) {
      return moment(dt)
        .format('MMMM YYYY');
    },
    handleCommand(command) {
      if (command === 'pin' || command === 'unpin') {
        this.$apollo.mutate({
          mutation: TOGGLE_PIN_PROJECT,
          variables: {
            id: this.item.id,
          },
          update: (store, { data: { togglePinProject } }) => {
            const fetchProjects = {
              query: FETCH_PROJECTS,
              variables: {
                userId: this.user.id,
                status: 'published',
              },
            };
            const { pinned } = togglePinProject;
            const data = store.readQuery(fetchProjects);
            // Find needed project to pin
            const index = data.fetchProjects.findIndex(p => p.id === this.item.id);

            // Find if there is already pinned project and unpin
            const pinnedIndex = data.fetchProjects.findIndex(p => p.pinned);

            if (pinnedIndex !== -1) {
              data.fetchProjects[pinnedIndex] = Object.assign(data.fetchProjects[pinnedIndex], {
                pinned: false,
              });
            }

            // Pin new project
            const project = Object.assign(data.fetchProjects[index], {
              pinned,
            });
            data.fetchProjects.splice(index, 1);

            // Add new project at the start of projects array
            data.fetchProjects.unshift(project);
            store.writeQuery({
              ...fetchProjects,
              data,
            });
          },
        });
      }
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
    fnt($text-light, 14px, $weight-light, left);
    transition: all .3s;
    cursor: pointer;

    &:hover {
      clolor: $primary;
    }

    &--play {
      font-size: 22px;
      color: $white;
    }

    &--light {
      color: $text-lighter;
    }
  }

  .hf-item {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;

    &__header {
      fl-between();
      height: 36px;
      flex: 0 0 auto;
      padding: 0 16px;
    }

    &__main {
      fl-center();
      display: flex;
      flex: 1 1 auto;
      background: center center / cover no-repeat $white-ter;
    }

    &__play-btn {
      fl-center();
      cursor: pointer;
      height: 62px;
      width: 62px;
      border-radius: 50%;
      border: 3px solid $white;
      padding-left: 4px;
      transition: all .3s;
      opacity: 0;
    }

    &:hover &__play-btn {
      opacity: 1;
    }

    &__footer {
      fl-between();
      height: 72px;
      flex: 0 0 auto;
      padding: 12px 16px;
    }

    &__left {
      fl-left();

      &--top {
        align-items: flex-start;
      }
    }

    &__right {
      fl-right();
      display: flex;
      flex-direction: column;
      justify-content: space-between;

      &--top {
        align-items: flex-start;
        height: 100%;
      }
    }

    &__right.row {
      flex-direction row;
    }

    .actions {
      margin-left: 10px;

      &__dropdown-button {
        cursor: pointer;
        height: 12px;
        width: 26px;
      }
    }

    &__pinned {
      fnt($grey-lighter, 14px, $weight-normal, left);
      width: 100%;
      display: flex;
      justify-content: flex-end;
      align-items: center;
      font-style: italic;

      span {
        margin-left: 5px;
      }
    }

    &__title {
      fnt($text, 14px, $weight-normal, left);
      text-transform: capitalize;
    }

    &__views {
      fnt($text, 12px, $weight-normal, right);
      text-transform: capitalize;
    }

    &__avatar {
      display: flex;
      flex: 0 0 auto;
      height: 32px;
      width: 32px;
      border-radius: 50%;
      background: center center / cover no-repeat $grey-lighter;
      cursor: pointer;
    }

    &__name-box {
      padding-left: 8px;
      min-height: 48px;
      cursor: pointer;
    }

    &__name {
      fnt($text, 12px, $weight-normal, left);
    }

    &__sub-name {
      fnt($primary, 10px, $weight-normal, left);
    }

    &__action-box {
      display: flex;
      align-items: flex-start;
      justify-content: flex-end;

      height: 32px;
    }

    &__action-item {
      fl-right();
      padding: 0 4px;
    }

    &__action-qty {
      fnt($text-light, 12px, $weight-normal, left);
      padding-left: 2px;
    }
  }

</style>
