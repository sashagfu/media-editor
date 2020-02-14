<template>
  <div class="WithdrawFundsDialog wf-dialog">
    <el-dialog
      :visible="withdrawFundsIsOpen"
      :modal-append-to-body="true"
      :append-to-body="true"
      width="40%"
      top="20vh"
      @update:visible="handleClose"
    >
      <!-- HEADER -->
      <div
        slot="title"
        class="wf-dialog__dialog-title"
      >
        {{ trans('users.withdraw_funds') }}
        <!-- <el-checkbox
          v-model="verified"
          style="margin-left: 12px"
        >
          verified
        </el-checkbox> -->
      </div>

      <!-- BODY -->
      <template v-if="!activeUser.paypalVerified">
        <div class="wf-dialog__form-row wf-dialog__form-row--centered">
          <font-awesome-icon
            :icon="['fas', 'exclamation-circle']"
            class="fa-icon fa-icon--warning"
          />
          <span class="wf-dialog__warning-msg">
            {{ trans('users.pp_unconfirmed') }}
          </span>
        </div>
        <div class="wf-dialog__form-row wf-dialog__form-row--marginless">
          <div
            :class="[
              'input-box',
              {'input-box--error': error}
            ]"
          >
            <div class="input-box__item">
              <font-awesome-icon
                :icon="['fab', 'paypal']"
                class="fa-icon fa-icon--pp"
              />
            </div>
            <div class="input-box__item input-box__item--full">
              <label class="input-box__label">
                {{ trans('users.your_pp_acc') }}
              </label>
              <input
                v-model="paypalEmail"
                type="text"
                name="text"
                class="input-box__input"
                @change="skipError()"
              >
              <div
                v-if="error"
                class="input-box__error"
                @click="skipError()"
              >
                {{ error }}
              </div>
            </div>
            <div class="input-box__item">
              <div
                :class="[
                  'input-box__btn',
                  {'input-box__btn--disabled': !paypalEmail}
                ]"
                @click="confirm"
              >
                <font-awesome-icon
                  v-if="loading"
                  :icon="['fas', 'spinner']"
                  spin
                  class="fa-icon fa-icon__spinner"
                />
                <template v-else>
                  {{ trans('users.confirm') }}
                </template>
              </div>
            </div>
          </div>
        </div>
      </template>
      <template v-else>
        <div class="wf-dialog__form-row wf-dialog__form-row--centered wf-dialog__form-row--title">
          {{ `You've got $${activeUser.balance} on your account` }}
        </div>
        <div class="wf-dialog__form-row wf-dialog__form-row--centered wf-dialog__form-row--rounded">
          <div
            :style="{
              'height': `${sizeT}px`,
              'width': `${sizeT}px`
            }"
            class="amount__round">
            <div class="amount__title">
              ${{ activeUser.balance - withdrawing }}
            </div>
          </div>
          <div class="amount__arrow">
            <font-awesome-icon
              :icon="['fas', 'arrow-right']"
              class="fa-icon fa-icon--arrow"
            />
          </div>
          <div
            :style="{
              'height': `${sizeW}px`,
              'width': `${sizeW}px`
            }"
            class="amount__round"
          >
            <div class="amount__title">
              ${{ withdrawing }}
            </div>
          </div>
        </div>
        <div class="wf-dialog__form-row">
          <div class="wf-dialog__slider">
            <el-slider
              v-model="withdrawing"
              :min="minAmount"
              :max="Number(activeUser.balance)"
              :show-tooltip="false"
            />
          </div>
        </div>
      </template>

      <!-- FOOTER -->
      <div
        v-if="!activeUser.paypalVerified"
        slot="footer"
        class="dialog-footer"
      />
      <div
        v-else
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
              'wf-dialog__btn',
              {'wf-dialog__btn--spinner': loading}
            ]"
            size="small"
            type="primary"
            @click="withdraw"
          >
            <span
              v-if="loading"
              class="fa-icon__box"
            >
              <font-awesome-icon
                :icon="['fas', 'spinner']"
                spin
                class="fa-icon fa-icon__spinner"
              />
            </span>
            <span class="dialog-footer__btn-title">
              {{ trans('users.withdraw') }}
            </span>
          </el-button>
        </span>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import * as constants from 'Helpers/constants';
import { mapGetters } from 'vuex';

export default {
  name: 'WithdrawFundsDialog',
  props: {
    withdrawFundsIsOpen: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      verifyStatus: '',
      loading: false,
      error: '',
      paypalEmail: '',
      minAmount: 25,
      withdrawing: 25,
    };
  },
  computed: {
    ...mapGetters('general', ['activeUser']),
    sizeT() {
      return 100 + (((this.activeUser.balance - this.withdrawing) / this.activeUser.balance) * 100);
    },
    sizeW() {
      return 100 + ((this.withdrawing / this.activeUser.balance) * 100);
    },
  },
  watch: {
    verifyStatus(val) {
      if (val === constants.VERIFY_STATUS_OK) {
        this.$notify.success({
          title: this.trans('common.success'),
          message: this.trans('user.verify_success'),
        });
      } else if (val === constants.VERIFY_STATUS_FAIL) {
        this.$notify.error({
          title: this.trans('common.error'),
          message: this.trans('user.verify_error'),
        });
      }
    },
    activeUser() {
      this.paypalEmail = this.activeUser.paypalEmail;
    },
  },
  mounted() {
    const hash = document.location.hash.slice(1).toUpperCase();
    if (hash === constants.VERIFY_STATUS_OK || hash === constants.VERIFY_STATUS_FAIL) {
      this.verifyStatus = hash;
      document.location.hash = 'about';
    }
  },
  methods: {
    skipError() {
      this.error = '';
    },
    handleClose() {
      this.$emit('close-withdraw-funds');
    },
    confirm() {
      this.loading = true;
      this.$http.post('/create_verification', { paypalEmail: this.paypalEmail })
        .then(({ data: src }) => {
          window.location = src;
        })
        .catch((e) => {
          console.error(e);
          this.loading = false;
        });
    },
    withdraw() {
      this.loading = true;
      this.$http.post('/create_payout', { amount: this.withdrawing })
        .then(({ data: { status } }) => {
          if (status === 'success') {
            this.$swal({
              title: this.trans('common.success'),
              text: this.trans('user.verify_success'),
              type: 'success',
              confirmButtonColor: '#51b847',
              buttonsStyling: false,
              confirmButtonClass: 'swal__button swal__button--success',
            });
          } else {
            this.$swal({
              title: this.trans('common.error'),
              text: this.trans('user.verify_error'),
              type: 'error',
              confirmButtonColor: '#b5b5b5',
              confirmButtonText: this.trans('common.close'),
              buttonsStyling: false,
              confirmButtonClass: 'swal__button swal__button--close',
            });
          }
          this.loading = false;
          this.handleClose();
        })
        .catch((e) => {
          console.error(e);
          this.loading = false;
        });
    },
  },
};
</script>

<style lang="stylus">
  @import '../../../../sass/front/components/bulma-theme';

  .swal__button {
    fnt($text-invert, 14px, $weight-normal, center);
    border-radius: $radius;
    cursor: pointer;
    min-width: 72px;
    outline: none;
    padding: 11px 12px;
    &--success {
      background-color: $primary;
      border-color: $primary;
      &:hover {
        background-color: rgba($primary, 0.8);
      }
    }
    &--close {
      background-color: $grey-light;
      border-color: $grey-light;
      &:hover {
        background-color: rgba($grey-light, 0.8);
      }
    }
  }
</style>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .amount {
    &__round {
      fl-center();
      height: 160px;
      width: 160px;
      border: 5px solid $primary;
      border-radius: 50%;
      transition: all .3s;
    }
    &__arrow {
      fl-center();
      margin: 0 24px;
    }
    &__title {
      fnt($primary, 36px, $weight-bold, center);
    }
  }

  .wf-dialog {
    &__dialog-title {
      fl-left();
    }
    &__form-row {
      fl-left();
      &:not(:last-child) {
        margin-bottom: 12px;
      }
      &--marginless {
        margin: 0 !important;
      }
      &--centered {
        fl-center();
      }
      &--title {
        margin-bottom: 24px;
      }
      &--rounded {
        height: 200px;
      }
    }
    &__slider {
      width: 100%;
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
    &__warning-msg {
      fnt($text, 14px, $weight-normal, left);
      padding-left: 12px;
    }
    &__frame {
      position: fixed;
      width: 1200px;
      left: 15vw;
      top: 10vh;
      height: 70vh;
    }
  }

  .dialog-footer {
    fl-between();
    &__right {
      fl-right();
    }
  }

  .fa-icon {
    color      : $text-light;
    font-size  : 16px;
    transition : all .3s;
    &__box {
      margin: -2px 0;
    }
    &__spinner {
      fnt($text-invert, 16px, $weight-light, left);
    }
    &--pp {
      color      : $text-lighter;
      font-size  : 36px;
    }
    &--warning {
      color      : $warning;
      font-size  : 24px;
    }
    &:hover {
      opacity: 0.8;
    }
    &--arrow {
      font-size: 36px;
      color: $primary;
    }
  }

  .input-box {
    fl-left();
    background-color : $background-light;
    border-radius    : $radius;
    border           : 1px solid $border;
    padding          : 0 8px;
    position         : relative;
    width            : 100%;
    &--error {
      border-color: $danger;
    }
    &__item {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      &--full {
        flex: 1 1 auto;
      }
      &:not(:first-child) {
        margin-left: 8px;
      }
    }
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
    &--is-success {
      border-color : $success;
    }
    &--is-warning {
      border-color : $warning;
    }
    &--is-danger {
      border-color : $danger;
    }
    &__btn {
      fnt($text-invert, 12px, $weight-normal, center);
      fl-center();
      background-color: $primary;
      border-radius: $radius;
      cursor: pointer;
      height: 36px;
      transition: all .3s;
      width: 62px;
      &--disabled {
        background-color: $grey-lighter;
        cursor: not-allowed;
      }
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
  }

</style>
