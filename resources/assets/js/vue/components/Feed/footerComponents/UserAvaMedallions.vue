<template>
  <div class="UserAvaMedallions user-ava-medallions">
    <div
      v-for="(user, key) in actions"
      v-if="key <= 3"
      :key="key"
      :class="`medallion medallion__${key+1}`"
      :style="{backgroundImage: `url(${user.avatar})`}"
      @click="goToProfile(user)"
    />
    <div
      v-if="actions.length === 1"
      class="user-ava-medallions__text"
      @click.prevent="showActions(post.id)"
    >
      <span
        v-for="(user, key) in actions"
        :key="key"
      >
        <span class="user-ava-medallions__text--strong">
          {{ user.displayName }}
        </span>
        <br>
        {{ post.isPerformance ? trans('stars.starred') : trans('likes.liked') }}
      </span>
    </div>
    <div
      v-if="actions.length === 2"
      class="user-ava-medallions__text"
      @click.prevent="showActions(post.id)"
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
      {{ post.isPerformance ? trans('stars.starred') : trans('likes.liked') }}
    </div>
    <div
      v-if="actions.length > 2"
      class="user-ava-medallions__text"
      @click.prevent="showActions(post.id)"
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
        `${trans('common.and')} ${post.stars.length - 2}`
      }} {{
        post.isPerformance ? trans('stars.starred') : trans('likes.liked')
      }}
    </div>
    <div
      :class="{'is-active': post.id === selectedPostId}"
      class="modal"
    >
      <div class="modal-background"/>
      <div class="modal-content">
        <div class="post-stars-box box">
          <div class="columns is-gapless is-multiline">
            <div
              v-for="(user, key) in postUserActions"
              :key="key"
              class="post-stars-user column is-4"
            >
              <div class="columns is-gapless">
                <div class="column is-3">
                  <div
                    :style="{backgroundImage: `url(${user.avatar})`}"
                    class="post-stars-user__ava"
                  />
                </div>
                <div class="column is-9">
                  <div class="post-stars-user__names">
                    <div class="post-stars-user__display_name">
                      {{ user.displayName }}
                    </div>
                    <div class="post-stars-user__username">
                      {{ user.username }}
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

export default {
  props: {
    post: {
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
      postUserActions: [],
      selectedPostId: '',
      isActive: true,
    };
  },
  methods: {
    showActions(postId) {
      this.postUserStars = [];
      const link = this.post.isPerformance ? '/api/post/stars' : '/api/post/likes';
      this.$http.post(link, {
        _token: window.Laravel.csrfToken,
        post_id: postId,
      })
        .then((response) => {
          this.postUserActions = response.data.users;
          this.selectedPostId = postId;
        });
    },
    goToProfile(user) {
      window.open(`/profile/${user.username}`, '_blank');
    },
    closeModal() {
      this.selectedPostId = '';
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
    align-items     : center;
    display         : flex;
    justify-content : center;
    position        : relative;

    &__text {
      cursor      : pointer;
      color       : $text-lighter;
      font-size   : 12px;
      font-weight : $weight-normal;
      margin-left : 8px;
      text-align  : left;
      &--strong {
        color       : $text;
        font-weight : $weight-normal+100;
      }
    }
  }

  .medallion {
    background    : center center/cover no-repeat $grey-light;
    border        : 2px solid $background-light;
    border-radius : 100%;
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

  .post-stars-box {
    min-height : 200px;
    padding    : 10px;
  }

  .post-stars-user {
    display : flex;
    &__ava {
      background      : center center no-repeat #37ccf1;
      background-size : cover;
      border-radius   : 3px;
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
      color       : $text;
      font-size   : 13px;
      font-weight : $weight-semibold;
      text-align  : left;
    }
    &__username {
      color       : $text-light;
      font-size   : 11px;
      font-weight : $weight-normal;
      text-align  : left;
    }
  }

</style>
