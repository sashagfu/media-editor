<template>
  <div class="UserChangePassword user-pswrd">
    <el-dialog
      :visible="chngPswIsOpen"
      :modal-append-to-body="true"
      :append-to-body="true"
      width="40%"
      top="20vh"
      @update:visible="handleClose"
    >
      <!-- HEADER -->
      <div
        slot="title"
        class="user-pswrd__dialog-title"
      >
        {{ trans('users.change_pass') }}
      </div>
      <!-- BODY -->
      <div class="user-pswrd__form-row">
        <div
          :class="[
            'input-box',
            {'input-box--error': error.old}
          ]"
        >
          <label class="input-box__label">
            {{ trans('users.old_pass') }}
          </label>
          <input
            v-if="showPsw"
            v-model="currentUserPsw.old"
            type="text"
            name="old-password"
            class="input-box__input"
            @blur="validateOldPassword"
            @click="skipError('old')"
          >
          <input
            v-else
            v-model="currentUserPsw.old"
            type="password"
            name="old-password"
            autocomplete="current-password"
            class="input-box__input"
            @blur="validateOldPassword"
            @change="skipError('old')"
          >
          <div
            v-if="error.old"
            class="input-box__error"
            @click="skipError('old')"
          >
            {{ error.old }}
          </div>
        </div>
      </div>
      <div class="user-pswrd__form-row">
        <div
          :class="[
            'input-box',
            {'input-box--error': error.new}
          ]"
        >
          <label class="input-box__label">
            {{ trans('users.new_pass') }}
          </label>
          <input
            v-if="showPsw"
            v-model="currentUserPsw.new"
            type="text"
            name="new-password"
            class="input-box__input"
            @blur="validateNewPassword"
            @change="skipError('new')"
          >
          <input
            v-else
            v-model="currentUserPsw.new"
            type="password"
            name="new-password"
            autocomplete="new-password"
            class="input-box__input"
            @blur="validateNewPassword"
            @change="skipError('new')"
          >
          <div
            v-if="error.new"
            class="input-box__error"
            @click="skipError('new')"
          >
            {{ error.new }}
          </div>
        </div>
      </div>
      <div class="user-pswrd__form-row">
        <div
          :class="[
            'input-box',
            {'input-box--error': error.cnf}
          ]"
        >
          <label class="input-box__label">
            {{ trans('users.confirm_pass') }}
          </label>
          <input
            v-if="showPsw"
            v-model="confirmPass"
            type="text"
            name="confirm-password"
            class="input-box__input"
            @blur="validateConfirmPassword"
            @change="skipError('cnf')"
          >
          <input
            v-else
            v-model="confirmPass"
            type="password"
            name="confirm-password"
            autocomplete="confirm-password"
            class="input-box__input"
            @blur="validateConfirmPassword"
            @change="skipError('cnf')"
          >
          <div
            v-if="error.cnf"
            class="input-box__error"
            @click="skipError('cnf')"
          >
            {{ error.cnf }}
          </div>
        </div>
      </div>
      <div class="user-pswrd__form-row">
        <el-switch
          v-model="showPsw"
        />
        <span class="user-pswrd__switch-text">
          {{ trans('users.show_pass') }}
        </span>
      </div>

      <!-- FOOTER -->
      <div
        slot="footer"
        class="dialog-footer"
      >
        <span class="dialog-footer__left"/>
        <span class="dialog-footer__right">
          <el-button
            size="small"
            @click="handleClose"
          >
            {{ trans('users.cancel') }}
          </el-button>
          <el-button
            :class="[
              'user-pswrd__btn',
              {'user-pswrd__btn--spinner': updating}
            ]"
            size="small"
            type="primary"
            @click="confirm"
          >
            <span
              v-if="updating"
              class="fa-icon__box"
            >
              <font-awesome-icon
                :icon="['fas', 'spinner']"
                spin
                class="fa-icon fa-icon__spinner"
              />
            </span>
            <span class="dialog-footer__btn-title">
              {{ trans('users.change_pass') }}
            </span>
          </el-button>
        </span>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import UPDATE_PASSWORD from 'Gql/logins/mutations/updatePassword.graphql';

export default {
  name: 'UserChangePassword',
  props: {
    chngPswIsOpen: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      updating: false,
      showPsw: false,
      confirmPass: '',
      currentUserPsw: {
        new: '',
        old: '',
      },
      error: {
        old: '',
        new: '',
        cnf: '',
      },
    };
  },
  computed: {
    ...mapGetters('general', ['activeUser']),
  },
  methods: {
    handleClose() {
      this.$emit('close-chng-psw');
    },
    skipError(val) {
      this.error[val] = '';
    },
    validateOldPassword() {
      if (!this.currentUserPsw.old.trim()) {
        this.error.old = 'Old passwords field can\'t be empty';
        return false;
      }
      // TODO add async validation for old password
      return true;
    },
    validateNewPassword() {
      if (!this.currentUserPsw.new.trim()) {
        this.error.new = 'New passwords field can\'t be empty';
        return false;
      } else if (this.currentUserPsw.new.trim() === this.currentUserPsw.old.trim()) {
        this.error.new = 'New passwords and old passwords fields must be different';
        return false;
      }
      return true;
    },
    validateConfirmPassword() {
      if (!this.confirmPass.trim()) {
        this.error.cnf = 'Confirm passwords field can\'t be empty';
        return false;
      } else if (this.currentUserPsw.new.trim() !== this.confirmPass.trim()) {
        this.error.cnf = 'New passwords and confirm passwords fields must be identical';
        return false;
      }
      return true;
    },
    confirm() {
      if (!this.validateNewPassword() || !this.validateConfirmPassword()) {
        console.log('validate error');
        return;
      }
      this.updating = true;
      this.$apollo.mutate({
        mutation: UPDATE_PASSWORD,
        variables: {
          email: this.activeUser.email,
          token: window.Laravel.csrfToken,
          password: this.currentUserPsw.new,
          passwordConfirmation: this.confirmPass,
        },
      })
        .then((data) => {
          console.log(data);
          this.updating = false;
        })
        .catch((err) => {
          console.error(err);
          this.updating = false;
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

  .user-pswrd {
    &__dialog-title {
      fl-left();
    }
    &__form-row {
      fl-left();
      &:not(:last-child) {
        margin-bottom: 12px;
      }
    }
    &__switch-text {
      fnt($text-light, 12px, $weight-normal, left);
      padding-left: 8px;
    }
    &__btn {

      &--spinner {
        padding: 9px 12px;
      }
    }
  }

  .dialog-footer {
    fl-between();
    &__right {
      fl-right();
    }
  }

  .fa-icon {
    color: $text-light;
    font-size: 16px;
    transition: all .3s;
    &__box {
      margin: -2px 0;
    }
    &__spinner {
      fnt($text-invert, 16px, $weight-light, left);
    }
    &:hover {
      opacity: 0.8;
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
    width: 100%;
    &--error {
      border-color: $danger;
    }
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
      &--textarea {
        padding: 8px 0;
      }
    }
    &__tags {
      fl-left();
      flex-wrap: wrap;
      margin-left: -8px;
      padding-bottom: 4px;
      width: 100%;
    }
    &--is-success {
      border-color: $success;
    }
    &--is-warning {
      border-color: $warning;
    }
    &__error {
      fnt($warning-invert, .75rem, $weight-normal, left);
      background-color: $warning;
      border-radius: $radius;
      border: 1px solid $warning;
      box-shadow: 0 2px 2px 0 rgba($black-bis, 0.16), 0 0 0 1px rgba($black-bis, 0.08);
      cursor: pointer;
      padding: 4px 12px;
      position: absolute;
      top: 50px;
      transition: box-shadow .3s;
      z-index: 2;
      &:hover {
        box-shadow: 0 3px 8px 0 rgba($black-bis, 0.2), 0 0 0 1px rgba($black-bis, 0.08);
      }
      &--accept-box {
        left: 20px;
        top: 20px;
      }
    }
    &__diamond-error {
      deg45();
      background-color: $warning;
      height: 8px;
      position: absolute;
      top: -4px;
      width: 8px;
      z-index: 1;
    }
  }

</style>

