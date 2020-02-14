<template>
  <div class="TopUpDialog donate">
    <el-dialog
      :visible="isDialogOpen"
      :close-on-click-modal="false"
      :append-to-body="true"
      top="15vh"
      width="40%"
      @update:visible="handleClose"
    >
      <div class="donate__body">
        <div class="donate__column">
          <div class="donate__title-line">
            <div class="donate__title">
              {{ trans('donate.your_balance') }}
            </div>
            <div class="donate__right"/>
          </div>
          <div class="donate__line">
            <span class="donate__balance">
              ${{ user.balance }}
            </span>
          </div>
          <div class="donate__title-line">
            <div class="donate__title">
              {{ trans('donate.top_up') }}
            </div>
            <div class="donate__right">
              <!-- if need change currency -->
              <!-- <font-awesome-icon
                v-if="currencyIcon"
                :icon="['fas', currencyIcon]"
                class="fa-icon fa-icon--currency-lb"
              />
              <el-select
                v-model="donate.currency"
                size="small"
                placeholder="Select"
              >
                <el-option
                  v-for="(item, index) in currency"
                  :key="`cur-${index}`"
                  :label="item.label"
                  :value="item.value"
                  :icon="item.icon"
                >
                  <span style="float: left">
                    <font-awesome-icon
                      :icon="['fas', item.icon]"
                      class="fa-icon fa-icon--currency"
                    />
                  </span>
                  <span style="float: right; color: #8492a6; font-size: 13px">
                    {{ item.value }}
                  </span>
                </el-option>
              </el-select> -->
            </div>
          </div>
          <!-- if need one-time donate or monthly -->
          <!-- <div class="donate__line">
            <el-radio
              v-model="donate.monthly"
              label="one"
            >
              {{ trans('donate.one_time') }}
            </el-radio>
            <el-radio
              v-model="donate.monthly"
              label="monthly"
            >
              {{ trans('donate.monthly') }}
            </el-radio>
          </div> -->
          <!-- BUTTONS OF AMOUNT -->
          <div class="donate__line donate__line--ml--8">
            <div class="donate__btn-box">
              <el-button
                :class="[
                  'donate__btn',
                  {'donate__btn--active': donate.amount === 20}
                ]"
                type="primary"
                plain
                @click="chooseAmount(20)"
              >
                <font-awesome-icon
                  v-if="currencyIcon"
                  :icon="['fas', currencyIcon]"
                  class="fa-icon fa-icon--currency-bt"
                />
                <span class="">20</span>
              </el-button>
            </div>
            <div class="donate__btn-box">
              <el-button
                :class="[
                  'donate__btn',
                  {'donate__btn--active': donate.amount === 10}
                ]"
                type="primary"
                plain
                @click="chooseAmount(10)"
              >
                <font-awesome-icon
                  v-if="currencyIcon"
                  :icon="['fas', currencyIcon]"
                  class="fa-icon fa-icon--currency-bt"
                />
                <span class="">10</span>
              </el-button>
            </div>
            <div class="donate__btn-box">
              <el-button
                :class="[
                  'donate__btn',
                  {'donate__btn--active': donate.amount === 15}
                ]"
                type="primary"
                plain
                @click="chooseAmount(15)"
              >
                <font-awesome-icon
                  v-if="currencyIcon"
                  :icon="['fas', currencyIcon]"
                  class="fa-icon fa-icon--currency-bt"
                />
                <span class="">15</span>
              </el-button>
            </div>
            <div class="donate__btn-box">
              <el-button
                :class="[
                  'donate__btn',
                  {'donate__btn--active': donate.amount === 5}
                ]"
                type="primary"
                plain
                @click="chooseAmount(5)"
              >
                <font-awesome-icon
                  v-if="currencyIcon"
                  :icon="['fas', currencyIcon]"
                  class="fa-icon fa-icon--currency-bt"
                />
                <span class="">5</span>
              </el-button>
            </div>
          </div>
          <div class="donate__line donate__line--ml--8">
            <div class="donate__btn-box">
              <el-button
                :class="[
                  'donate__btn',
                  {'donate__btn--active': donate.amount === 50}
                ]"
                type="primary"
                plain
                @click="chooseAmount(50)"
              >
                <font-awesome-icon
                  v-if="currencyIcon"
                  :icon="['fas', currencyIcon]"
                  class="fa-icon fa-icon--currency-bt"
                />
                <span class="">50</span>
              </el-button>
            </div>
            <!-- INPUT OF AMOUNT -->
            <div class="donate__inp-box">
              <el-input
                v-model="amount"
                :class="[
                  'donate__inp',
                  {'donate__inp--active': amount}
                ]"
                type="number"
                size="small"
                placeholder="Other amount"
                @change="inputAmount(amount)"
              >
                <template slot="prepend">
                  <font-awesome-icon
                    v-if="currencyIcon"
                    :icon="['fas', currencyIcon]"
                    class="fa-icon fa-icon--currency-bt"
                  />
                </template>
              </el-input>
            </div>
          </div>
          <div class="donate__line">
            <span class="donate__details">
              {{ trans('donate.top_up_details') }}
            </span>
          </div>
          <div class="donate__line">
            <el-switch
              v-model="agree"
              :active-text="trans('donate.agree')"
            />
          </div>
        </div>
      </div>
      <div
        slot="footer"
        class="dialog-footer"
      >
        <span class="dialog-footer__column"/>
        <span class="dialog-footer__column">
          <el-button
            size="small"
            @click="handleClose"
          >
            {{ trans('donate.cancel') }}
          </el-button>
          <el-button
            :disabled="!agree"
            size="small"
            type="primary"
            @click="donateHandle"
          >
            <font-awesome-icon
              v-if="uploading"
              :icon="['fas', 'spinner']"
              spin
              class="fa-icon fa-icon__spinner"
            />
            <span
              :class="{'dialog-footer__btn-title': uploading}">
              {{ trans('donate.top_up') }}
            </span>
          </el-button>
        </span>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';
import * as constants from 'Helpers/constants';
import CREATE_TOP_UP from 'Gql/payments/mutations/createTopUp.graphql';

export default {
  name: 'TopUpDialog',
  props: {
    user: {
      type: Object,
      default: () => ({}),
    },
    isDialogOpen: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      constants,
      paymentStatus: '',
      donate: {
        currency: 'USD',
        monthly: 'one',
        paymentType: '',
        amount: 0,
      },
      amount: 0,
      uploading: false,
      agree: false,
      currency: [
        {
          value: 'GBP',
          label: 'GBP',
          icon: 'pound-sign',
        },
        {
          value: 'USD',
          label: 'USD',
          icon: 'dollar-sign',
        },
        {
          value: 'EUR',
          label: 'EUR',
          icon: 'euro-sign',
        },
        {
          value: 'JPY',
          label: 'JPY',
          icon: 'yen-sign',
        },
      ],
    };
  },
  computed: {
    ...mapGetters('general', ['activeUser']),
    currencyIcon() {
      const idx = this.currency.findIndex(c => c.value === this.donate.currency);
      if (idx !== -1) {
        return this.currency[idx].icon;
      }
      return '';
    },
  },
  watch: {
    paymentStatus(val) {
      if (val === constants.PAYMENT_STATUS_OK) {
        this.$swal({
          title: this.trans('common.success'),
          text: this.trans('donate.success_message'),
          type: 'success',
          confirmButtonColor: '#51b847',
          buttonsStyling: false,
          confirmButtonClass: 'swal__button swal__button--success',
        });
      } else if (val === constants.PAYMENT_STATUS_FAIL) {
        this.$swal({
          title: this.trans('common.error'),
          text: this.trans('donate.error_message'),
          type: 'error',
          confirmButtonColor: '#b5b5b5',
          confirmButtonText: this.trans('common.close'),
          buttonsStyling: false,
          confirmButtonClass: 'swal__button swal__button--close',
        });
      }
    },
  },
  mounted() {
    const hash = document.location.hash.slice(1).toUpperCase();
    if (hash === constants.PAYMENT_STATUS_OK || hash === constants.PAYMENT_STATUS_FAIL) {
      this.paymentStatus = hash;
      document.location.hash = 'about';
    }
  },
  methods: {
    async donateHandle() {
      if (!this.agree) {
        return;
      }
      if (!this.donate.amount) {
        this.handleClose();
        const { value: amount } = await this.$swal({
          title: this.trans('donate.input_donate'),
          input: 'number',
          type: 'warning',
          inputPlaceholder: this.trans('donate.your_amount'),
          buttonsStyling: false,
          inputClass: 'swal__input',
          confirmButtonClass: 'swal__button swal__button--success',
        });
        if (amount) {
          this.donate.amount = amount;
        }
        this.handleOpen();
      }
      this.topUp();
    },
    async topUp() {
      try {
        this.uploading = true;
        const { data: { createTopUp: resp } } = await this.$apollo.mutate({
          mutation: CREATE_TOP_UP,
          variables: {
            amount: this.donate.amount,
          },
        });
        console.log('topUp resp ', resp);
        window.location = resp.redirectUrl;
      } catch (e) {
        console.error(e);
        this.$swal({
          title: this.trans('common.error'),
          text: this.trans('donate.error_message'),
          type: 'error',
          confirmButtonColor: '#b5b5b5',
          confirmButtonText: this.trans('common.close'),
          buttonsStyling: false,
          confirmButtonClass: 'swal__button swal__button--close',
        });
      } finally {
        this.uploading = false;
        this.handleClose();
      }
    },
    chooseAmount(amount) {
      this.amount = '';
      this.donate.amount = amount;
    },
    inputAmount(amount) {
      this.donate.amount = amount;
    },
    choosePaymentType(type) {
      this.donate.paymentType = type;
    },
    handleClose() {
      this.$emit('close-dialog');
    },
    handleOpen() {
      this.$emit('open-dialog');
    },
  },
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .donate {
    &__body {
      fl-between();
    }
    &__column {
      display: flex;
      flex-direction: column;
    }
    &__left {
      fl-left();
    }
    &__right {
      fl-right();
    }
    &__logo {
      background: url('../../../../img/logo/actionime-logo.png') center center/contain no-repeat;
      opacity: .5;
      padding-top: 50%;
      width: 50%;
    }
    &__title-line {
      fl-between();
      padding-bottom: 8px;
      &--pt-32 {
        padding-top: 32px;
      }
    }
    &__line {
      fl-left();
      padding-bottom: 8px;
      &--ml--8 {
        margin-left: -8px;
      }
      &--ml--12 {
        margin-left: -12px;
      }
    }
    &__balance {
      fl-center();
      fnt($primary, 28px, $weight-bold, center);
      border-radius: $radius-large;
      border: 2px dashed $primary;
      padding: 12px 0;
      width: 100%;
    }
    &__title {
      fnt($text-light, 28px, $weight-normal, left);
    }
    &__btn-box {
      width: 25%;
      padding-left: 8px;
    }
    &__inp-box {
      width: 75%;
      padding-left: 8px;
    }
    &__btn {
      width: 100%;
      padding: 8px 20px;
      &:hover .fa-icon--currency-bt {
        color: $text-invert;
      }
      &:focus .fa-icon--currency-bt {
        color: $text-invert;
      }
      &--active {
        background: $primary;
        border-color: $primary;
        color: $text-invert;
        & .fa-icon--currency-bt {
          color: $text-invert;
        }
      }
    }
    &__secure {
      fnt($grey-light, 14px, $weight-light, left);
      text-transform: uppercase;
      margin-left: 8px;
    }
    &__details {
      fnt($text-light, 14px, $weight-light, left);
    }
  }

  .donate-btn {
    fl-center();
    background-color: $grey-light;
    border-radius: $radius;
    cursor: pointer;
    height: 72px;
    margin-left: 12px;
    padding: 12px 0;
    width: 50%;
    &--disabled {
      background-color: $grey-lighter;
      cursor: not-allowed;
    }
    &--al-active {
      background-color: $primary;
    }
    &--pp-active {
      background-color: $paypal;
    }
    &__headline {
      fnt($text-invert, 18px, $weight-bold, left);
      text-transform: capitalize;
    }
    &__payment-line {
      fl-center();
      &--pp {
        margin-left: -6px;
      }
    }
    &__payment-title {
      fnt($text-invert, 22px, $weight-bold, left);
      font-style: italic;
    }
    &__al-logo {
      background: url('../../../../img/logo/actionime-logo--white.png')
      center center/contain no-repeat;
      flex: 0 0 auto;
      height: 40px;
      margin: 0 6px;
      width: 40px;
    }
    &__al-title-box {
      display: flex;
      flex-direction: column;
    }
    &__al-title {
      fnt($text-invert, 20px, $weight-bold, center);
      line-height: 1;
    }
    &__al-sub-title {
      fnt($text-invert, 12px, $weight-normal, left);
      line-height: 1;
    }
  }

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
    &--currency-lb {
      fnt($text-light, 18px, $weight-light, left);
      margin-right: 8px;
    }
    &--currency-bt {
      fnt($primary, 14px, $weight-light, left);
      transition : all .1s;
    }
    &--lock {
      fnt($grey-lighter, 14px, $weight-light, left);
    }
    &--visa {
      fnt($white, 40px, $weight-light, left);
      margin: 0 6px;
    }
    &--paypal {
      fnt($white, 14px, $weight-light, left);
    }
  }

  .dialog-footer {
    fl-between();
    &__column {
      fl-right();
      width: 50%;
    }
    &__btn-title {
      padding-left: 4px;
    }

  }
  .el-select {
    width: 120px;
  }
</style>

<style lang="stylus">
  @import '../../../../sass/front/components/bulma-theme';

  .swal {
    &__input {
      color: $primary !important;
      font-size: 14px !important;
      font-weight: $weight-semibold !important;
      text-align: center;
      background-color: $white;
      border-radius: $radius !important;
      border: 1px solid $primary !important;
      height: 32px !important;
      line-height: 32px !important;
      outline: none;
      padding: 0 15px !important;
      width: 224px !important;
      &::-webkit-outer-spin-button,
      &::-webkit-inner-spin-button {
        -webkit-appearance: none !important;
      }
    }
    &__button {
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
  }

  .donate {
    &__inp--active {
      & .el-input-group {
        &__prepend {
          background-color: $primary;
          border-color: $primary;
        }
      }
      & .fa-icon--currency-bt {
        color: $text-invert;
      }
      & .el-input__inner {
        border-color: $primary;
      }
    }
    & .el-input__inner {
      fnt($primary, 14px, $weight-semibold, left);
      -moz-appearance: textfield;
      &:hover {
        border-color: $primary;
      }
      &::-webkit-outer-spin-button,
      &::-webkit-inner-spin-button {
        -webkit-appearance: none;
      }
      &::-webkit-input-placeholder {
        color       : $grey-light;
        font-weight : 300;
      }
      &::-moz-placeholder {
        color       : $grey-light;
        font-weight : 300;
      }
      &:-moz-placeholder {
        color       : $grey-light;
        font-weight : 300;
      }
      &:-ms-input-placeholder {
        color       : $grey-light;
        font-weight : 300;
      }
    }
    & .el-dialog__body {
      padding: 30px 32px;
    }
    & .el-dialog__footer {
      padding: 20px 32px;
    }
  }
</style>
