<template>
  <div class="ProfilePageUserBox pp-user-box box">
    <div class="pp-user-box__header">
      <div class="pp-user-box__avatar-box">
        <Avatar
          v-if="user.avatar.includes('/profile/default/avatar')"
          :size="126"
          :username="user.display_name"
          custom-class="pp-user-box__avatar"
        />
        <div
          v-else
          :style="{'background-image': `url(${user.avatar})`}"
          class="pp-user-box__avatar"
        />
        <el-dropdown
          v-if="isMine"
          :class="[
            'pp-user-box__dropdown-box',
            {'pp-user-box__dropdown-box--active': userAvatarMenuActive}
          ]"
          trigger="click"
          size="small"
          @command="handleCommand"
        >
          <div
            :class="[
              'pp-user-box__edit-box',
              {'pp-user-box__edit-box--active': userAvatarMenuActive}
            ]"
            @click.stop="toggleUserAvatarMenu"
          >
            <font-awesome-icon
              :icon="['fas', 'camera']"
              class="fa-icon fa-icon--edit"
            />
          </div>
          <el-dropdown-menu slot="dropdown">
            <el-dropdown-item
              command="upload"
            >
              <div class="pp-user-box__edit-box-item">
                <div class="pp-user-box__edit-box-pic">
                  <font-awesome-icon
                    :icon="['fas', 'user-circle']"
                    class="fa-icon fa-icon--menu"
                  />
                </div>
                <div class="pp-user-box__edit-box-title">
                  {{ trans('users.upload') }}
                </div>
              </div>
            </el-dropdown-item>
            <el-dropdown-item
              command="clear"
            >
              <div class="pp-user-box__edit-box-item">
                <div class="pp-user-box__edit-box-pic">
                  <font-awesome-icon
                    :icon="['fas', 'times-circle']"
                    class="fa-icon fa-icon--menu"
                  />
                </div>
                <div class="pp-user-box__edit-box-title">
                  {{ trans('users.clear') }}
                </div>
              </div>
            </el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </div>
      <div class="pp-user-box__title">
        {{ user.display_name }}
      </div>
      <div class="pp-user-box__sub-title">
        @{{ user.username }}
      </div>
      <!-- BUTTON GROUP -->
      <div
        v-if="isMine"
        class="pp-user-box__users-row">
        <FollowingButton
          :user="user"
        />
        <FollowersButton
          :user="user"
        />
        <TopUpButton
          v-bind="$props"
          class="pp-user-box__btn-donate"
        />
      </div>
      <div
        v-if="!isMine && activeUser.id !== undefined"
        class="pp-user-box__users-col">
        <div class="pp-user-box__follow-section">
          <FollowButton
            v-if="activeUser.uuid && user.uuid !== activeUser.uuid"
            :following="user"
            :active-user="activeUser"
            class="pp-user-box__btn-follow"
          />
          <FollowersButton
            v-if="activeUser.uuid"
            :user="user"
          />
        </div>
        <div class="pp-user-box__actions-section">
          <MessageButton
            :user="user"
            class="pp-user-box__btn-message"
          />
          <DonateButton
            :user="user"
            v-bind="$props"
            class="pp-user-box__btn-donate"
          />
        </div>
      </div>
      <div
        v-if="(user.bio || user.uuid === activeUser.uuid) && !editingBio"
        class="pp-user-box__bio"
      >
        <div
          class="pp-user-box__bio--overlay overlay"
          @click="showEditBioForm"
        >
          <div class="overlay__circle">
            <font-awesome-icon
              :icon="['fas', 'pencil-alt']"
              class="fa-icon fa-icon--edit"
            />
          </div>
        </div>
        <div
          class="pp-user-box__bio--main"
        >
          <div
            v-if="!user.bio"
            class="pp-user-box__bio--title"
          >
            {{ trans('users.bio') }}
          </div>
          <div
            class="pp-user-box__bio--body"
          >
            {{ user.bio }}
          </div>
        </div>
      </div>
      <div
        v-if="editingBio"
        class="input-box"
      >
        <el-input
          id="input-box__bio"
          v-model="bio"
          type="textarea"
          rows="7"
          name="bio"
          class="input-box__input"
        />
        <div class="actions">
          <el-button
            size="mini"
            type="plain"
            @click="closeEditBioForm"
          >
            Cancel
          </el-button>
          <el-button
            size="mini"
            @click="updateBio"
          >Save</el-button>
        </div>
      </div>
    </div>

    <!-- FOOTER WITH STATISTIC -->

    <div class="pp-user-box__stars-box">
      <div class="pp-user-box__stars-item">
        <font-awesome-icon
          :icon="['far', 'star']"
          class="fa-icon fa-icon--star"
        />
        <div class="pp-user-box__stars-count">
          {{ user.total_likes + user.total_stars }}
        </div>
        <div class="pp-user-box__stars-title">
          {{ trans('profile_page.stars') }}
        </div>
      </div>
      <div class="pp-user-box__stars-item">
        <font-awesome-icon
          :icon="['fas', 'paperclip']"
          class="fa-icon fa-icon--paperclip"
        />
        <div class="pp-user-box__stars-count">
          <!-- TODO change number to computed count-->
          60
        </div>
        <div class="pp-user-box__stars-title">
          {{ trans('profile_page.clipped') }}
        </div>
      </div>
    </div>
    <ProfilePageCropAvatarDialog
      :is-dialog-open="isCropAvatarDialogOpen"
      :uploading="uploading"
      @close-dialog="closeDialog"
      @avatar-upload="avatarUpload"
    />
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import AVATAR_UPLOAD from 'Gql/users/mutations/updateAvatar.graphql';
import UPDATE_USER from 'Gql/users/mutations/updateUser.graphql';

import Avatar from 'Pages/ProfilePage/Avatar';
import ProfilePageCropAvatarDialog from 'Pages/ProfilePage/About/ProfilePageCropAvatarDialog';
import FollowButton from 'Pages/Common/FollowButton';
import DonateButton from 'Pages/Common/DonateButton';
import TopUpButton from 'Pages/Common/TopUpButton';
import MessageButton from 'Pages/Common/MessageButton';
import FollowingButton from 'Pages/Common/FollowingButton';
import FollowersButton from 'Pages/Common/FollowersButton';


export default {
  name: 'ProfilePageUserBox',
  components: {
    Avatar,
    ProfilePageCropAvatarDialog,
    FollowButton,
    DonateButton,
    TopUpButton,
    MessageButton,
    FollowingButton,
    FollowersButton,
  },
  props: {
    user: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      isCropAvatarDialogOpen: false,
      userAvatarMenuActive: false,
      uploading: false,
      agree: false,
      editingBio: false,
      bio: '',
    };
  },
  computed: {
    ...mapGetters('general', ['activeUser']),
    isMine() {
      return this.user.uuid === this.activeUser.uuid;
    },
  },
  mounted() {
    window.addEventListener('click', this.closeUserAvatarMenu, false);
  },
  methods: {
    handleClick() {
      if (this.isMine) {
        window.location = '/network#followers';
      }
    },
    closeDialog() {
      this.isCropAvatarDialogOpen = false;
    },
    toggleUserAvatarMenu() {
      this.userAvatarMenuActive = !this.userAvatarMenuActive;
    },
    closeUserAvatarMenu() {
      this.userAvatarMenuActive = false;
    },
    handleCommand(command) {
      this.closeUserAvatarMenu();
      if (command === 'upload') {
        this.isCropAvatarDialogOpen = true;
      } else if (command === 'clear') {
        this.clearAvatar();
      }
    },
    clearAvatar() {
      this.$confirm('This will permanently clear your avatar. Continue?', 'Warning', {
        confirmButtonText: 'OK',
        cancelButtonText: 'Cancel',
        type: 'warning',
      }).then(() => {
        this.avatarUpload('');
      });
    },
    avatarUpload(image) {
      this.uploading = true;
      this.$apollo.mutate({
        mutation: AVATAR_UPLOAD,
        variables: {
          url: image,
        },
      })
        .then(({ data: { updateAvatar } }) => {
          this.user.avatar = updateAvatar.avatar;
          this.$notify({
            title: 'Success',
            message: this.trans('users.uploaded'),
            type: 'success',
          });
          this.uploading = false;
          this.closeDialog();
        })
        .catch((error) => {
          console.error(error);
          this.uploading = false;
        });
    },
    showEditBioForm() {
      this.editingBio = true;
      this.bio = this.user.bio;
    },
    async updateBio() {
      const {
        id,
        username,
        email,
        displayName,
      } = this.activeUser;
      this.$apollo.mutate({
        mutation: UPDATE_USER,
        variables: {
          user: {
            id,
            username,
            email,
            displayName,
            bio: this.bio,
          },
        },
      })
        .then(({ data: { updateUser } }) => {
          this.user.bio = updateUser.bio;
          this.editingBio = false;
        });
    },
    closeEditBioForm() {
      this.editingBio = false;
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
    transition : all .3s;
    &--star {
      color: $yellow;
    }
    &--paperclip {
      color: $purple;
    }
    &--user {
      color: $grey;
    }
    &--edit {
      color: $white;
      font-size: 18px;
    }
    &--menu {
      color: $grey;
      font-size: 14px;
    }
  }

  .pp-user-box {
    &__header {
      fl-left();
      flex-direction: column;
      padding: 48px 0 36px;
    }
    &__avatar-box {
      fl-center();
      position: relative;
      height: 126px;
      width: 126px;
    }
    &__avatar {
      cover-all();
      fl-center();
      background: center center/cover no-repeat $grey-lighter;
      border-radius: 50%;
    }
    &__dropdown-box {
      fl-center();
      background-color: $grey-light;
      border-radius: 50%;
      border: 2px solid $white;
      bottom: 0;
      cursor: pointer;
      height: 36px;
      opacity: 0;
      position: absolute;
      right: 0;
      transition: all .3s;
      width: 36px;
      &--active {
        opacity: 1;
      }
    }
    &__edit-box {
      fl-center();
      cover-all();
      border-radius: 50%;
      opacity: 0;
      outline:none;
      transition: all .3s;
      width: 100%;
      height: 32px;
      &--active {
        opacity: 1;
      }
    }
    &__edit-box-item {
      fl-left();
    }
    &__edit-box-pic {
      padding-right: 4px;
    }
    &__edit-box-item:hover .fa-icon--menu{
      color: $primary;
    }
    &__avatar-box:hover &__edit-box,
    &__avatar-box:hover &__dropdown-box {
      opacity: 1;
    }
    &__title {
      fnt($text, 24px, $weight-bold, center);
      padding-top: 22px;
    }
    &__sub-title {
      fnt($text-light, 14px, $weight-normal, center);
      padding-bottom: 8px;
    }
    &__stars-box {
      fl-left();
      border-top: 1px solid $border;
    }
    &__stars-item {
      fl-center();
      border-right: 1px solid $border;
      flex-direction: column;
      height: 88px;
      width: 50%;
      &:last-child {
        border-right: none;
      }
      &--pointer {
        cursor: pointer;
      }
    }
    &__stars-count {
      fnt($text-light, 14px, $weight-normal, center);
      padding-top: 8px;
    }
    &__stars-title {
      fnt($text-lighter, 12px, $weight-light, center);
    }

    &__users-row {
      fl-center();
      flex-wrap: wrap;
    }
    &__users-col {
      fl-center();
      flex-direction: column;
      flex-wrap: wrap;
    }
    &__btn-follow {
      margin-bottom: 8px;
    }
    &__btn-donate {
      margin-left: 8px;
    }
    &__bio {
      fnt($text-lighter, 13px, $weight-light, center);
      margin: 0 40px;
      margin-top: 10px;
      text-align: left;
      position: relative;

      &--title {
        margin-bottom: 5px;
        font-size: 14px;
        font-weight: bold;
      }

      &--main {
        margin: 10px 20px 10px;
      }

      &--overlay {
        width: 100%;
        height: 100%;
        position: absolute;
        background: #414d5347;
        cursor: pointer;
        display: none;
        justify-content: space-around;
        align-items: center;
      }

      .overlay {

        &__circle {
          border: 1px dashed #fff;
          border-radius: 100%;
          width: 50px;
          height: 50px;
          display: flex;
          justify-content: space-around;
          align-items: center;
        }
      }
    }
    &__bio:hover {
      div:first-child {
        display: flex;
      }
    }
    &__follow-section {
      display: flex;
      justify-content space-between;
      width: 100%;
    }
    &__actions-section {
      display: flex;
      justify-content space-between;
    }
  }

  .input-box {
    align-items: flex-start;
    background-color: $background-light;
    border-radius: $radius;
    display: flex;
    flex-direction: column;
    padding: 0 18px;
    position: relative;
    width: 100%;

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
      outline: none;
      resize: none;
      width: 100%;

      &--textarea {
        padding: 8px 0;
      }
    }

    &__input {
      textarea:hover {
        border: 1px solid #dcdfe6;
      }
    }

    .actions {
      display: flex;
      width: 100%;
      justify-content flex-end;
      margin-top: 10px;
    }
  }
</style>
