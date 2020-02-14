<template>
  <div class="friends">
    <p
      v-if="errors.error"
      class="help is-danger"
    >
      {{ errors.error }}
    </p>
    <div class="tile is-ancestor">
      <div class="tile is-parent search-invite">
        <div class="tile is-child box">
          <div class="level">
            <div class="level-left">
              <div class="level-item ">
                <div class="friends__title">
                  {{ trans('circles.members_invite_email') }}
                </div>
                <i
                  v-if="emailsLoading"
                  class="fa fa-spinner fa-spin fa-3x fa-fw has-text-centered"
                />
                <i
                  v-if="emailsSuccess"
                  class="fa fa-check-circle-o fa-3x has-text-centered"
                />
              </div>
            </div>
            <div class="level-right search-invite--level-right">
              <div class="level-item search-invite--level-right-item">
                <form
                  class="search-invite--form"
                  @submit.prevent="inviteByEmail()"
                >
                  <input
                    :placeholder="trans('common.type_emails')"
                    v-model="emails"
                    type="text"
                    class="search-invite__input"
                    pattern="/^([\w+-.%]+@[\w-.]+\.[A-Za-z]{2,4},*[\W]*)+$/"
                  >
                  <button class="search-invite__button"/>
                </form>
              </div>
            </div>
          </div>
          <p v-if="emailError">
            {{ trans('circles.enter_emails') }}
          </p>
        </div>
      </div>
      <div class="tile is-parent search-invite">
        <div class="tile is-child box">
          <div class="level">
            <div class="level-left">
              <div class="level-item ">
                <div class="friends__title">
                  {{ trans('circles.members_invite_existing') }}
                </div>
                <i
                  v-if="existingLoading"
                  class="fa fa-spinner fa-spin fa-3x fa-fw has-text-centered"
                />
                <i
                  v-if="existingSuccess"
                  class="fa fa-check-circle-o fa-3x has-text-centered"
                />
              </div>
            </div>
            <div class="level-right search-invite--level-right">
              <div class="level-item search-invite--level-right-item">
                <form
                  class="search-invite--form"
                  @submit.prevent="inviteExisting()"
                >
                  <multiselect
                    v-model="selectedToShare"
                    :options="usersToShare"
                    :multiple="true"
                    :internal-search="false"
                    :options-limit="20"
                    :loading="userSelectLoading"
                    :reset-after="false"
                    :clear-on-select="false"
                    :max="5"
                    :placeholder="trans('common.type_search')"
                    label="display_name"
                    track-by="id"
                    @search-change="getUsersToShare"
                  />
                  <button class="search-invite__button"/>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <CircleMembersMain
      :circle="circle"
      :members="members"
    />
  </div>
</template>
<script>
/* @flow */
import _ from 'lodash';
import Multiselect from 'vue-multiselect/dist/vue-multiselect.min';
import CircleMembersMain from './CircleMembersMain';

export default {
  components: {
    CircleMembersMain,
    Multiselect,
  },
  props: {
    circle: {
      type: Object,
      default() {
        return {};
      },
    },
    members: {
      type: Array,
      default() {
        return [];
      },
    },
  },
  data() {
    return {
      selectedToShare: [],
      usersToShare: [],
      userSelectLoading: false,
      emails: '',
      emailError: false,
      errors: [],
      emailsLoading: false,
      existingLoading: false,
      emailsSuccess: false,
      existingSuccess: false,
    };
  },
  methods: {
    getUsersToShare: _.debounce(function searchUsers(search) {
      if (!search) {
        return;
      }
      this.userSelectLoading = !this.userSelectLoading;
      this.$http.post(`/api/post/share/chat/select?q=${search}`).then((response) => {
        this.usersToShare = response.data;
        this.userSelectLoading = !this.userSelectLoading;
      });
    }, 600),
    inviteExisting() {
      if (this.selectedToShare.length === 0) {
        return;
      }
      this.existingLoading = !this.existingLoading;
      this.$http.post('/api/circle/invite_existing_users', {
        circle_slug: this.circle.slug,
        users: this.usersToShare,
      })
        .then(() => {
          this.existingLoading = !this.existingLoading;
          this.existingSuccess = !this.existingSuccess;
          window.location.reload();
        })
        .catch((error) => {
          this.existingLoading = !this.existingLoading;
          this.errors = error.response.data;
        });
    },
    inviteByEmail() {
      if (this.extractEmails(this.emails)) {
        this.emailsLoading = !this.emailsLoading;
        this.$http.post('/api/circle/invite_by_email', {
          circle_slug: this.circle.slug,
          emails: this.extractEmails(),
        })
          .then(() => {
            this.emailsLoading = !this.emailsLoading;
            this.emailsSuccess = !this.emailsSuccess;
            window.location.reload();
          })
          .catch((error) => {
            this.emailsLoading = !this.emailsLoading;
            this.errors = error.response.data;
          });
      } else {
        this.emailError = true;
      }
    },
    extractEmails() {
      return this.emails.match(/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/gi);
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
    @import '../../../../../../sass/front/components/bulma-theme';

    .friends {
        width: 100%;
    }

    .level {
        &-left {
            padding: 0 13px;
        }
        &-right {
            padding: 0 13px;
        }
        &-item {
            padding: 0 13px;
            height: 68px;
            &--last {
                padding-left: 13px;
            }
        }
    }

    .friends {
        &__title {
            //padding: 16px 0;
            font-size: 14px;
            font-weight: $weight-semibold;
            color: $text;
        }
        &__settings {
            height: 68px;
            width: 68px;
            background: url('../../../../../../img/three-dots-icon--light.png')
                center center no-repeat;
            &:hover {
                background-image: url('../../../../../../img/three-dots-icon--dark.png');
            }
        }
    }

    .search-friends {
        //padding-left: 24px;
        //height: 72px;
        display: inline-flex;
        align-items: center;
        &__input {
            border-radius: 3px 0 0 3px;
            margin-right: -1px;
            width: 304px;
            height: 40px;
            padding-left: 16px;
            border: 1px solid $border;

            font-family: 'Roboto', sans-serif;
            font-weight: 400;
            font-size: 12px;
            color: $text;
            outline: none;

        }
        &__input:focus {
            box-shadow: inset 0 1px 2px rgba(10, 10, 10, 0.1);
        }
        &__button, &__button:focus {
            border-radius: 0 3px 3px 0;
            margin-right: -1px;
            background: url('../../../../../../img/magnifying-glass-icon--small.png')
                center center no-repeat $border;
            border-color: transparent;
            outline: none;
            width: 32px;
            height: 40px;
        }
    }

    .search-invite {
        &--level-right {
            width: 70%;
        }
        &--level-right-item {
            width: 100%;
        }
        &--form {
            display: inline-flex;
            align-items: center;
            width: 100%;
        }
        &__input {
            border-radius: 3px 0 0 3px;
            margin-right: -1px;
            width: 100%;
            height: 40px;
            padding-left: 16px;
            border: 1px solid $border;

            font-family: 'Roboto', sans-serif;
            font-weight: 400;
            font-size: 12px;
            color: $text;
            outline: none;

        }
        &__input:focus {
            box-shadow: inset 0 1px 2px rgba(10, 10, 10, 0.1);
        }
        &__button, &__button:focus {
            border-radius: 0 3px 3px 0;
            margin-right: -1px;
            background: url('../../../../../../img/register-icon.png')
                center center no-repeat $border;
            border-color: transparent;
            outline: none;
            width: 32px;
            height: 40px;
        }
    }
</style>
