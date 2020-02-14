<template>
  <div class="UserProfileSettings user-ps">
    <el-dialog
      :visible="settingsIsOpen"
      :modal-append-to-body="true"
      :append-to-body="true"
      title="Profile Settings"
      top="10vh"
      custom-class="user-ps"
      @update:visible="handleClose"
    >
      <!-- BODY -->
      <el-tabs
        v-model="activeName"
      >
        <el-tab-pane
          label="About"
          name="about"
        >
          <div class="user-ps__form-row">
            <div class="input-box">
              <label
                class="input-box__label"
                for="input-box__display_name"
              >
                {{ trans('users.display_name') }}
              </label>
              <input
                id="input-box__display_name"
                v-model="currentUser.displayName"
                type="text"
                name="title"
                class="input-box__input"
              >
            </div>
          </div>
          <div class="user-ps__form-row">
            <div class="input-box">
              <label
                class="input-box__label"
                for="input-box__email"
              >
                {{ trans('users.email') }}
              </label>
              <input
                id="input-box__email"
                v-model="currentUser.email"
                type="text"
                name="title"
                class="input-box__input"
              >
            </div>
          </div>
          <div class="user-ps__form-row">
            <div class="input-box">
              <label
                class="input-box__label"
                for="input-box__bio"
              >
                {{ trans('users.bio') }}
              </label>
              <input
                id="input-box__bio"
                v-model="currentUser.bio"
                type="text"
                name="bio"
                class="input-box__input"
              >
            </div>
          </div>
        </el-tab-pane>

        <el-tab-pane
          label="Security"
          name="security"
        >
          <div class="user-ps__form-row">
            <el-switch
              v-model="userSettings.privacy.showTopSponsors"
              active-text="Show top sponsors list"
            />
          </div>
          <div class="user-ps__form-row">
            <el-switch
              v-model="userSettings.privacy.showSponsorship"
              active-text="Show my sponsorships list"
            />
          </div>
        </el-tab-pane>
      </el-tabs>


      <!-- FOOTER -->
      <div
        slot="footer"
        class="dialog-footer"
      >
        <span class="dialog-footer__right">
          <el-button
            size="small"
            @click="handleClose"
          >
            {{ trans('users.cancel') }}
          </el-button>
          <el-button
            size="small"
            type="primary"
            @click="confirm"
          >
            <font-awesome-icon
              v-if="uploadingUser || uploadingSettings"
              :icon="['fas', 'spinner']"
              spin
              class="fa-icon fa-icon__spinner"
            />
            <span
              :class="[
                'dialog-footer__btn-title',
                {'dialog-footer__btn-title--spinner': uploadingUser || uploadingSettings}
              ]"
            >
              {{ trans('users.confirm') }}
            </span>
          </el-button>
        </span>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import { has } from 'lodash';
import autosize from 'autosize';

import UPDATE_USER from 'Gql/users/mutations/updateUser.graphql';
import UPDATE_USER_SETTINGS from 'Gql/users/mutations/updateUserSettings.graphql';

export default {
  name: 'UserProfileSettings',
  props: {
    settingsIsOpen: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      uploadingUser: false,
      uploadingSettings: false,
      currentUser: {},
      uploadingFields: ['id', 'displayName', 'email', 'username', 'bio'],
      activeName: 'about',

      userSettings: {
        privacy: {
          showTopSponsors: false,
          showSponsorship: false,
        },
      },
    };
  },
  computed: {
    ...mapGetters('general', [
      'activeUser',
    ]),
  },
  watch: {
    activeUser: {
      immediate: true,
      handler() {
        this.currentUser = Object.assign({}, this.activeUser);
        if (has(this.activeUser, 'settings')) {
          const privacyKeys = Object.keys(this.currentUser.settings.privacy);
          privacyKeys.forEach((key) => {
            if (key !== '__typename') {
              this.userSettings.privacy[key] = this.currentUser.settings.privacy[key];
            }
          });
        }
      },
    },
  },
  updated() {
    autosize(this.$refs.textarea);
  },
  methods: {
    ...mapActions('general', [
      'setActiveUser',
    ]),
    handleClose() {
      this.$emit('close-settings');
    },
    prepareToUpload() {
      const obj = {};
      this.uploadingFields.forEach(f => Object.assign(obj, { [f]: this.currentUser[f] }));
      return obj;
    },
    confirm() {
      this.updateUser();
      this.updateUserSettings();
    },
    async updateUserSettings() {
      try {
        this.uploadingSettings = true;
        await this.$apollo.mutate({
          mutation: UPDATE_USER_SETTINGS,
          variables: {
            settings: this.userSettings,
          },
        });
      } catch (err) {
        console.error(err);
      } finally {
        this.uploadingSettings = false;
        if (!this.uploadingUser && !this.uploadingSettings) {
          this.handleClose();
        }
      }
    },
    async updateUser() {
      try {
        this.uploadingUser = true;
        const user = this.prepareToUpload();
        await this.$apollo.mutate({
          mutation: UPDATE_USER,
          variables: {
            user,
          },
        });
      } catch (err) {
        console.error(err);
      } finally {
        this.uploadingUser = false;
        if (!this.uploadingUser && !this.uploadingSettings) {
          this.handleClose();
        }
      }
    },
  },
};
</script>

<style lang="stylus">
  @import '../../../../../sass/front/components/bulma-theme';

  .user-ps {
    .el-dialog {
      &__body {
        padding: 0 20px 16px;
      }
    }
  }
</style>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .fa-icon {
    fnt($text-light, 14px, $weight-light, left);
    transition : all .3s;
    &__spinner {
      fnt($text-invert, 16px, $weight-light, left);
      margin: -2px;
    }
    &__box {
      margin: -2px 0;
    }
  }

  .user-ps {
    &__form-row {
      &:first-child {
        margin-top: 12px;
      }
      &:not(:last-child) {
        margin-bottom: 12px;
      }
    }
  }
  .dialog-footer {
    &__btn-title {
      &--spinner {
        margin-left: 4px;
      }
    }
  }

  .input-box {
    align-items      : flex-start;
    background-color : $background-light;
    border           : 1px solid $border;
    border-radius    : $radius;
    display          : flex;
    flex-direction   : column;
    padding          : 0 18px;
    position         : relative;
    width            : 100%;
    &__label {
      fnt($text-light, 10px, $weight-normal, left);
      display         : flex;
      flex-direction  : column;
      height          : 20px;
      justify-content : flex-end;
    }
    &__input {
      fnt($text-light, 12px, $weight-normal, left);
      border  : none;
      height  : 32px;
      outline : none;
      resize  : none;
      width   : 100%;
      &--textarea {
        padding : 8px 0;
      }
    }
    &__tags {
      fl-left();
      flex-wrap      : wrap;
      margin-left    : -8px;
      padding-bottom : 4px;
      width          : 100%;
    }
    &--is-success {
      border-color : $success;
    }
    &--is-warning {
      border-color : $warning;
    }
    &__error {
      fnt($warning-invert, .75rem, $weight-normal, left);
      background-color : $warning;
      border           : 1px solid $warning;
      border-radius    : $radius;
      box-shadow       : 0 2px 2px 0 rgba($black-bis, 0.16), 0 0 0 1px rgba($black-bis, 0.08);
      cursor           : pointer;
      padding          : 4px 12px;
      position         : absolute;
      top              : 50px;
      transition       : box-shadow .3s;
      z-index          : 2;
      &:hover {
        box-shadow : 0 3px 8px 0 rgba($black-bis, 0.2), 0 0 0 1px rgba($black-bis, 0.08);
      }
      &--accept-box {
        left : 20px;
        top  : 20px;
      }
    }
    &__diamond-error {
      deg45();
      background-color : $warning;
      height           : 8px;
      position         : absolute;
      top              : -4px;
      width            : 8px;
      z-index          : 1;
    }
  }

</style>
