<template>
  <div class="UserAvaMedallions user-ava-medallions">
    <div
      v-for="(user, key) in actions"
      v-if="key <= 3"
      :key="key"
      @click="goToProfile(user)"
    >
      <Avatar
        v-if="user.avatar.includes('/profile/default/avatar')"
        :username="user.displayName"
        :size="28"
        custom-class="medallion"
      />
      <div
        v-else
        :style="{backgroundImage: `url(${user.avatar})`}"
        class="medallion"
      />
    </div>
    <div
      v-if="actions.length === 1"
      class="user-ava-medallions__text"
      @click.prevent="showActions(project.id)"
    >
      <span
        v-for="(user, key) in actions"
        :key="key"
      >
        <span class="user-ava-medallions__text--strong">
          {{ user.displayName }}
        </span>
        <br>
        {{ project.isPerformance ? trans('stars.starred') : trans('likes.liked') }}
      </span>
    </div>
    <div
      v-if="actions.length === 2"
      class="user-ava-medallions__text"
      @click.prevent="showActions(project.id)"
    >
      <span
        v-for="(user, key) in actions"
        :key="key"
      >
        <span
          v-if="key < 1"
          class="user-ava-medallions__text--strong"
        >
          {{ user.displayName }},
        </span>
        <span
          v-else-if="key === 1"
          class="user-ava-medallions__text--strong"
        >
          {{ user.displayName }}
        </span>
      </span>
      <br>
      {{ project.isPerformance ? trans('stars.starred') : trans('likes.liked') }}
    </div>
    <div
      v-if="actions.length > 2"
      class="user-ava-medallions__text"
      @click.prevent="showActions(project.id)"
    >
      <span
        v-for="(user, key) in actions"
        :key="key"
      >
        <span
          v-if="key < 1"
          class="user-ava-medallions__text--strong"
        >
          {{ user.displayName }},
        </span>
        <span
          v-else-if="key === 1"
          class="user-ava-medallions__text--strong"
        >
          {{ user.displayName }}
        </span>
      </span>
      <br>
      {{
        `${trans('common.and')} ${project.stars.length - 2}`
      }} {{
        project.isPerformance ? trans('stars.starred') : trans('likes.liked')
      }}
    </div>
    <div
      :class="{'is-active': project.id === selectedProjectId}"
      class="modal"
    >
      <div class="modal-background"/>
      <div class="modal-content">
        <div class="project-stars-box box">
          <div class="columns is-gapless is-multiline">
            <div
              v-for="(user, key) in actions"
              :key="key"
              class="project-stars-user column is-4"
            >
              <div class="columns is-gapless">
                <div class="column is-3">
                  <div
                    :style="{backgroundImage: `url(${user.avatar})`}"
                    class="project-stars-user__ava"
                  />
                </div>
                <div class="column is-9">
                  <div class="project-stars-user__names">
                    <div class="project-stars-user__display_name">
                      {{ user.displayName }}
                    </div>
                    <div class="project-stars-user__username">
                      @{{ user.username }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <button
        class="modal-close"
        @click="closeModal()"
      />
    </div>
  </div>
</template>

<script>
import Avatar from 'Pages/ProfilePage/Avatar';

export default {
  name: 'UserAvaMedallions',
  components: {
    Avatar,
  },
  props: {
    project: {
      type: Object,
      default: () => ({}),
    },
    actions: {
      type: Array,
      default: () => ([]),
    },
  },
  data() {
    return {
      selectedProjectId: null,
    };
  },
  methods: {
    showActions(projectId) {
      this.selectedProjectId = projectId;
    },
    goToProfile(user) {
      window.location = `/profile/${user.username}`;
    },
    closeModal() {
      this.selectedProjectId = null;
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .user-ava-medallions {
    fl-center();
    position        : relative;
    &__text {
      fnt($text-lighter, 12px, $weight-normal, left);
      cursor      : pointer;
      margin-left : 8px;
      &--strong {
        color       : $text;
        font-weight : $weight-normal+100;
      }
    }
  }

  .medallion {
   fl-center();
    background    : center center/cover no-repeat $grey-light;
    border        : 2px solid $background-light;
    border-radius : 50%;
    cursor        : pointer;
    flex          : 0 0 auto;
    height        : 28px;
    margin-left   : -10px;
    transition    : all .3s;
    width         : 28px;
    &:first-child {
      margin-left : 0;
    }
  }

  .project-stars-box {
    min-height : 200px;
    padding    : 10px;
  }

  .project-stars-user {
    display : flex;
    &__ava {
      background      : center center no-repeat #37ccf1;
      background-size : cover;
      border-radius   : $radius;
      height          : 40px;
      width           : 40px;
    }
    &__names {
      display         : flex;
      flex-direction  : column;
      justify-content : center;
      padding         : 0 8px;
    }
    &__display_name {
      fnt($text, 13px, $weight-semibold, left);
    }
    &__username {
      fnt($text-light, 11px, $weight-normal, left);
    }
  }

</style>
