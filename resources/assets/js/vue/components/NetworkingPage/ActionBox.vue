<template>
  <div class="ActionBox actions">
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
      <el-dropdown-menu
        slot="dropdown"
        class="actions__menu"
      >
        <!-- FOLLOW / UNFOLLOW -->
        <template v-if="!showFollowBtn">
          <el-dropdown-item
            v-if="isFollowing"
            class="actions__item"
            command="unfollow"
          >
            {{ trans('network.unfollow') }}
          </el-dropdown-item>
          <el-dropdown-item
            v-else
            class="actions__item"
            command="follow"
          >
            {{ trans('network.follow') }}
          </el-dropdown-item>
        </template>

        <!-- REPORT PROFILE -->
        <el-dropdown-item
          class="actions__item"
          command="report"
        >
          {{ trans('network.report_profile') }}
        </el-dropdown-item>

        <!-- BLOCK PROFILE -->
        <el-dropdown-item
          v-if="isBlocked"
          class="actions__item"
          command="unblock"
        >
          {{ trans('network.unblock_profile') }}
        </el-dropdown-item>
        <el-dropdown-item
          v-else
          class="actions__item"
          command="block"
        >
          {{ trans('network.block_profile') }}
        </el-dropdown-item>

        <!-- ON / OFF NOTIFICATIONS -->
        <el-dropdown-item
          v-if="notificationsOn"
          class="actions__item"
          command="offNotifications"
        >
          {{ trans('network.off_notifications') }}
        </el-dropdown-item>
        <el-dropdown-item
          v-else
          class="actions__item"
          command="onNotifications"
        >
          {{ trans('network.on_notifications') }}
        </el-dropdown-item>
      </el-dropdown-menu>
    </el-dropdown>

    <el-dialog
      v-if="isReporting"
      :visible.sync="isReporting"
      :append-to-body="true"
      class="action-dialog"
      @open="onReportingDialogOpen"
      @close="onReportingDialogClose"
    >
      <div
        slot="title"
        class="action-dialog__title"
      >
        <div class="action-dialog__title-text">
          {{ trans('network.report_profile') }}
        </div>
      </div>
      <div
        class="reasons"
      >
        <label
          class="input-box__label"
          for="input-box__title"
        >
          {{ trans('network.report_reasons') }}
        </label>
        <el-checkbox-group
          v-model="reportReasons"
          class="input-box__reasons"
        >
          <el-checkbox
            :label="3"
            class="input-box--checkbox"
          >
            {{ trans('reports.user_inappropriate_content') }}
          </el-checkbox>
          <el-checkbox
            :label="4"
            class="input-box--checkbox"
          >
            {{ trans('reports.user_fake_profile') }}
          </el-checkbox>
          <el-checkbox
            :label="5"
            class="input-box--checkbox"
          >
            {{ trans('reports.user_deceased') }}
          </el-checkbox>
          <el-checkbox
            :label="10"
            class="input-box--checkbox"
          >
            {{ trans('reports.user_other') }}
          </el-checkbox>
        </el-checkbox-group>
      </div>
      <div
        v-if="otherSelected"
        class="input-box"
      >
        <label
          class="input-box__label"
          for="input-box__desc"
        >
          {{ trans('network.description') }}
        </label>
        <textarea
          id="input-box__desc"
          ref="textarea"
          v-model.trim="report.description"
          type="text"
          name="description"
          class="input-box__input input-box__input--textarea"
        />
      </div>

      <div
        slot="footer"
        class="action-dialog__footer"
      >
        <el-button
          plain
          size="small"
          @click="isReporting = false"
        >
          {{ trans('common.cancel') }}
        </el-button>
        <el-button
          type="info"
          size="small"
          @click="handleReportProfile"
        >
          <font-awesome-icon
            v-if="reportLoading"
            :icon="['fas', 'spinner']"
            spin
            class="fa-icon fa-icon--spinner"
          />
          <font-awesome-icon
            v-else
            :icon="['fas', 'flag']"
            class="fa-icon fa-icon--spinner"
          />
          <span class="action-dialog__btn-title">
            {{ trans('network.report') }}
          </span>
        </el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import autosize from 'autosize';
import * as constants from 'Helpers/constants';

import DELETE_FOLLOWER from 'Gql/users/mutations/deleteFollower.graphql';
import CREATE_FOLLOWER from 'Gql/users/mutations/createFollower.graphql';
import CREATE_REPORT from 'Gql/reports/mutations/createReport.graphql';

export default {
  name: 'ActionBox',
  props: {
    user: {
      type: Object,
      default: () => ({}),
    },
    activeUser: {
      type: Object,
      default: () => ({}),
    },
    showFollowBtn: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      isReporting: false,
      reportLoading: false,
      reportReasons: [],
      report: {
        description: '',
      },
    };
  },
  computed: {
    isFollowing() {
      return this.user.isFollowing;
    },
    isBlocked() {
      return false;
    },
    notificationsOn() {
      return true;
    },
    otherSelected() {
      return this.reportReasons.find(r => r === constants.REPORT_REASON_OTHER);
    },
  },
  mounted() {
    autosize(this.$refs.textarea);
  },
  updated() {
    autosize(this.$refs.textarea);
  },
  methods: {
    handleCommand(command) {
      switch (command) {
        case 'unfollow':
        case 'follow': {
          this.toggleFollow();
          break;
        }
        case 'report': {
          this.isReporting = true;
          break;
        }
        case 'unblock': {
          // this.isRename = true;
          break;
        }
        case 'block': {
          // this.isRename = true;
          break;
        }
        case 'offNotifications': {
          // this.isRename = true;
          break;
        }
        case 'onNotifications': {
          // this.isRename = true;
          break;
        }
        default: {
          // this.isDelete = false;
          // this.isRename = false;
        }
      }
    },
    toggleFollow() {
      // this.uploading = true;
      this.$apollo.mutate({
        mutation: this.isFollowing ? DELETE_FOLLOWER : CREATE_FOLLOWER,
        variables: {
          userId: this.user.id,
        },
      })
        .then(() => {
          // this.isFollowing = !this.isFollowing;
          // this.uploading = false;
        })
        .catch((error) => {
          console.error(error);
          // this.uploading = false;
        });
    },
    onReportingDialogOpen() {},
    onReportingDialogClose() {},
    async handleReportProfile() {
      this.reportLoading = true;
      this.$apollo.mutate({
        mutation: CREATE_REPORT,
        variables: {
          report: {
            reportableId: this.user.id,
            reportableType: 'user',
            reasons: this.reportReasons,
            description: this.report.description,
          },
        },
      })
        .then(() => {
          this.reportLoading = false;
          this.reportReasons = [];
          this.report.description = '';
          this.isReporting = false;
        });
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
    fnt($text-light, 1rem, $weight-normal, left);
    transition : all .3s;
    cursor     : pointer;
    &:hover {
      color : $text;
    }
    &--warning {
      font-size : 32px;
      color     : $warning;
      &:hover {
        color : $warning;
      }
    }
    /* &--invert {
      color : $text-invert;
      &:hover {
        color : $grey-lighter;
      }
    } */
    &--edit {
      font-size: 12px;
      color: $primary;
    }
    &--spinner {
      color     : $text-invert;
      font-size : 16px;
      margin    : -2px;
      &:hover {
        color : $text-invert;
      }
    }
  }
  .el-button:hover .fa-icon--edit {
    color: $text-invert;
  }

  .actions {
    fl-center();

    &__dropdown-button {
      fl-center();
      cursor : pointer;
      height : 12px;
      width  : 26px;
    }

    &__item {
      fnt($text-light, 12px, $weight-normal, left);
      transition  : color .3s;
      line-height : 26px;
      padding     : 0 12px;
      text-transform: capitalize;
      &:hover {
        color            : $text;
        background-color : $grey-lighter;
      }
      /* &--danger {
        color : $red;
        &:hover {
          color : $red;
        }
      } */
    }

  }

  .action-dialog {
    &__title {
      fl-left();
    }
    &__title-text {
      fnt($text, 18px, $weight-normal, left);
      text-transform: capitalize;
    }
    &__input {
      fnt($text, 12px, $weight-normal, left);
      border        : 1px solid $border;
      border-radius : 3px 0 0 3px;
      height        : 40px;
      margin-right  : -1px;
      outline       : none;
      padding-left  : 16px;
      width         : 100%;
      transition    : box-shadow .3s;

      &:focus {
        box-shadow : inset 0 1px 2px rgba(10, 10, 10, .1);
      }
    }
    &__main {
      fl-center();
    }
    &__main-text {
      fnt($text, 14px, $weight-normal, left);
      padding-left : 12px;
    }
    &__btn-title {
      margin-left: 8px;
      text-transform: capitalize;
    }
  }

  .reasons {
    margin-bottom: 20px;

    .input-box {
      &__label {
        font-size: 14px;
        margin-bottom: 5px;
      }
    }
  }

  .input-box {
    align-items      : flex-start;
    background-color : $background-light;
    border-radius    : $radius;
    border           : 1px solid $border;
    display          : flex;
    flex-direction   : column;
    margin-bottom    : 20px;
    padding          : 0 18px;
    position         : relative;
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
      width   : 100%;
      resize  : none;
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
      border-radius    : $radius;
      border           : 1px solid $warning;
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

<style
  lang="stylus"
>
  .reasons
    .el-checkbox__label
      font-size 13px

</style>
