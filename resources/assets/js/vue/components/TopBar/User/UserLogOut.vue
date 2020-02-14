<template>
  <div class="UserLogOut user-log-out">
    <el-dialog
      :visible="logOutIsOpen"
      :modal-append-to-body="true"
      :append-to-body="true"
      width="30%"
      top="20vh"
      @update:visible="handleClose"
    >
      <!-- HEADER -->
      <div
        slot="title"
        class="user-log-out__dialog-title"
      >
        {{ trans('common.warning') }}
      </div>
      <!-- BODY -->
      <div class="user-log-out__dialog-main">
        <font-awesome-icon
          :icon="['fas', 'exclamation-circle']"
          class="fa-icon fa-icon--warning"
        />
        {{ trans('common.log_out') }}
      </div>
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
            :class="[
              'dialog-footer__btn',
              {'dialog-footer__btn--with-spinner': goingOut}
            ]"
            size="small"
            type="primary"
            @click="confirm"
          >
            <font-awesome-icon
              v-if="goingOut"
              :icon="['fas', 'spinner']"
              spin
              class="fa-icon fa-icon__spinner"
            />
            <span class="dialog-footer__btn-title">
              {{ trans('users.confirm') }}
            </span>
          </el-button>
        </span>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapActions } from 'vuex';

export default {
  name: 'UserLogOut',
  props: {
    logOutIsOpen: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      goingOut: false,
    };
  },
  methods: {
    ...mapActions('general', [
      'setActiveUser',
    ]),
    handleClose() {
      this.$emit('close-log-out');
    },
    confirm() {
      this.goingOut = true;
      localStorage.removeItem('accessToken');
      localStorage.removeItem('refreshToken');
      localStorage.removeItem('expiresIn');
      this.handleClose();
      this.setActiveUser(null);
      this.$router.push({
        name: 'LoginPage',
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

  .fa-icon {
    fnt($text-light, 14px, $weight-light, left);
    transition: all .3s;
    &__spinner {
      fnt($text-invert, 16px, $weight-light, left);
    }
    &__box {
      margin: -2px 0;
    }
    &--warning {
      color: $warning;
      font-size: 2rem;
      margin: 0 8px;
    }
  }

  .user-log-out {
    &__dialog-title {
      fl-left();
    }
    &__dialog-main {
      fl-center();
    }
  }
  .dialog-footer {
    &__right {
      fl-right();
    }
    &__btn {
      &--with-spinner {
        padding: 9px 12px;
      }
    }
  }

</style>
