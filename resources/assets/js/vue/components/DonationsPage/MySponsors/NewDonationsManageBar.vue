<template>
  <div class="NewDonationsManageBar nd-manage-bar">
    <div class="nd-manage-bar__left">
      <div class="nd-manage-bar__title">
        {{ trans('donate.new_donations') }}
      </div>
    </div>
    <div class="nd-manage-bar__right">
      <el-checkbox
        :value="pending"
        @input="togglePending"
      >
        {{ trans('donate.pending') }}
      </el-checkbox>
      <el-checkbox
        :value="accepted"
        @input="toggleAccepted"
      >
        {{ trans('donate.accepted') }}
      </el-checkbox>
      <el-checkbox
        :value="declined"
        @input="toggleDeclined"
      >
        {{ trans('donate.declined') }}
      </el-checkbox>
      <el-checkbox
        :value="expired"
        @input="toggleExpired"
      >
        {{ trans('donate.expired') }}
      </el-checkbox>
      <el-input
        :value="searchText"
        :placeholder="trans('common.quick_search')"
        size="mini"
        class="input-with-select nd-manage-bar__search-inp"
        @input="debounceInput"
      >
        <el-button
          v-if="!searchText"
          slot="append"
          class="nd-manage-bar__search-btn"
        >
          <font-awesome-icon
            :icon="['fas', 'search']"
            class="fa-icon"
          />
        </el-button>
        <el-button
          v-if="searchText"
          slot="append"
          class="nd-manage-bar__search-btn"
          @click="clickEsc"
        >
          <font-awesome-icon
            :icon="['fas', 'times']"
            class="fa-icon"
          />
        </el-button>
      </el-input>
      <div class="nd-manage-bar__status-box">
        <el-button
          class="nd-manage-bar__status-btn"
          type="primary"
          size="mini"
          plain
          @click="acceptDeclineAllOpen(true)"
        >
          {{ trans('donate.accept_donation_all') }}
        </el-button>
        <el-button
          class="nd-manage-bar__status-btn"
          type="danger"
          size="mini"
          plain
          @click="acceptDeclineAllOpen()"
        >
          {{ trans('donate.decline_donation_all') }}
        </el-button>
      </div>

      <!-- Confirmations dialog -->
      <el-dialog
        :visible="dialogIsOpen"
        :modal-append-to-body="true"
        :append-to-body="true"
        width="30%"
        top="20vh"
        @update:visible="handleClose"
      >
        <div
          slot="title"
          class="dialog__title"
        >
          {{ trans('common.warning') }}
        </div>
        <div class="dialog__main">
          <font-awesome-icon
            :icon="['fas', 'exclamation-circle']"
            class="fa-icon fa-icon--box fa-icon--warning"
          />
          {{ acceptAll ? trans('donate.all_accept_confirm') : trans('donate.all_decline_confirm') }}
        </div>
        <span
          slot="footer"
          class="dialog__footer"
        >
          <el-button
            size="mini"
            @click="handleClose"
          >
            {{ trans('donate.cancel') }}
          </el-button>
          <el-button
            v-if="acceptAll"
            type="primary"
            size="mini"
            class="dialog__btn"
            @click="handleAcceptDeclineAll(4)"
          >
            <font-awesome-icon
              v-if="loading"
              :icon="['fas', 'spinner']"
              spin
              class="fa-icon fa-icon--spinner"
            />
            <template v-else>
              <!--{{ trans('donate.accept_donation_all') }}-->
              {{ trans('donate.confirm') }}
            </template>
          </el-button>
          <el-button
            v-else
            type="danger"
            size="mini"
            class="dialog__btn"
            @click="handleAcceptDeclineAll(3)"
          >
            <font-awesome-icon
              v-if="loading"
              :icon="['fas', 'spinner']"
              spin
              class="fa-icon fa-icon--spinner"
            />
            <template v-else>
              <!--{{ trans('donate.decline_donation_all') }}-->
              {{ trans('donate.confirm') }}
            </template>
          </el-button>
        </span>
      </el-dialog>
    </div>
  </div>
</template>

<script>
import moment from 'moment';
import { debounce } from 'lodash';

import ACCEPT_ALL_DONATIONS from 'Gql/donations/mutations/acceptAllDonations.graphql';
import DECLINE_ALL_DONATIONS from 'Gql/donations/mutations/declineAllDonations.graphql';
import FETCH_NEW_DONATIONS from 'Gql/donations/queries/fetchIncomingDonations.graphql';

export default {
  name: 'NewDonationsManageBar',
  props: {
    searchText: {
      type: String,
      default: '',
    },
    pending: {
      type: Boolean,
      default: false,
    },
    accepted: {
      type: Boolean,
      default: false,
    },
    declined: {
      type: Boolean,
      default: false,
    },
    expired: {
      type: Boolean,
      default: false,
    },
    user: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      loading: false,
      acceptAll: false,
      dialogIsOpen: false,
    };
  },
  methods: {
    handleClose() {
      this.dialogIsOpen = false;
    },
    acceptDeclineAllOpen(acceptAll = false) {
      this.acceptAll = acceptAll;
      this.dialogIsOpen = true;
    },
    togglePending() {
      this.$emit('toggle-pending');
    },
    toggleAccepted() {
      this.$emit('toggle-accepted');
    },
    toggleDeclined() {
      this.$emit('toggle-declined');
    },
    toggleExpired() {
      this.$emit('toggle-expired');
    },
    clickEsc() {
      this.$emit('input-search-text', '');
    },
    debounceInput: debounce(function updateSearch(text) {
      this.$emit('input-search-text', text);
    }, 600),
    async handleAcceptDeclineAll(st) {
      // st -> status
      // 3 -> decline
      // 4 -> accept
      try {
        this.loading = true;
        await this.$apollo.mutate({
          mutation: st === 3 ? DECLINE_ALL_DONATIONS : ACCEPT_ALL_DONATIONS,
          update: (store) => {
            const fetchNewDonations = {
              query: FETCH_NEW_DONATIONS,
              variables: {
                userId: this.user.id,
                // status: 2, // only status pending
              },
            };
            const data = store.readQuery(fetchNewDonations);
            const incomingDonations = data.fetchIncomingDonations.map((donation) => {
              if (donation.status === 2) {
                const nDonation = Object.assign({}, donation, {
                  // st -> status
                  // 3 -> decline
                  // 4 -> accept
                  status: st,
                  updatedAt: moment().format(),
                });
                return nDonation;
              }
              return donation;
            });
            data.fetchIncomingDonations = incomingDonations;
            store.writeQuery({ ...fetchNewDonations, data });
          },
        });
      } catch (e) {
        console.error(e);
      } finally {
        this.loading = false;
        this.handleClose();
      }
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
    color      : $grey-light;
    font-size  : 16px;
    transition : all .2s;
    &:hover {
      opacity: 0.8;
    }
    &--spinner {
      color     : $text-invert;
      margin    : -2px;
      &:hover {
        opacity: 1;
      }
    }
    &--warning {
      color     : $warning;
      font-size : 2rem;
    }
    &--box {
      margin : 0 8px;
    }
  }

  .nd-manage-bar {
    fl-between();
    margin-bottom: 1.5rem;
    width: 100%;
    &__left {
      fl-left();
    }
    &__right {
      fl-right();
    }
    &__title {
      fnt($text-lighter, 18px, $weight-semibold, left);
      text-transform: uppercase;
      line-height: 1;
    }
    &__search-inp {
      margin-left: 24px;
    }
    &__search-btn {
      width: 58px;
      background-color: rgba($primary, 0.1);
    }
    &__status-box {
      fl-right();
      padding-left: 26px;
    }
    &__status-btn {
      width: 88px;
    }
  }
  .dialog {
    &__title {
      fl-left();
    }
    &__main {
      fl-center();
    }
    &__footer {
      fl-right();
    }
    &__btn {
      width: 88px;
    }
  }

</style>

<style
  lang="stylus"
>
  @import '../../../../../sass/front/components/bulma-theme';

  .nd-manage-bar {
    .el-input__inner {
      fnt($text-light, 12px, $weight-normal, left);
      &:hover {
        border-color: $primary;
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
      &:hover {
        border-color: $white-shadow !important;
      }
      &:focus {
        border-color: $grey-lighter !important;
      }
    }
  }
</style>
