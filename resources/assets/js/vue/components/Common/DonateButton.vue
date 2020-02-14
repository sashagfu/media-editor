<template>
  <div
    :title="trans('profile_page.donate')"
    class="DonateButton">
    <div
      :class="[
        'donate-btn',
        {'donate-btn--round': round}
      ]"
      @click="openDonateDialog"
    >
      <font-awesome-icon
        :icon="['fas', 'dollar-sign']"
        class="fa-icon"
      />
      <div
        v-if="!round"
        class="donate-btn__text">
        {{ trans('profile_page.donate') }}
      </div>
    </div>

    <DonationsDialog
      v-bind="$props"
      :is-dialog-open="isDonateDialogOpen"
      @close-dialog="closeDialog"
      @open-dialog="openDonateDialog"
    />
  </div>
</template>

<script>
import DonationsDialog from 'Pages/DonationsPage/DonationsDialog';

export default {
  name: 'DonateButton',
  components: {
    DonationsDialog,
  },
  props: {
    user: {
      type: Object,
      default: () => ({}),
    },
    round: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      isDonateDialogOpen: false,
    };
  },
  methods: {
    closeDialog() {
      this.isDonateDialogOpen = false;
    },
    openDonateDialog() {
      this.isDonateDialogOpen = true;
    },
  },

};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../sass/front/components/bulma-theme';

  .fa-icon
    fnt($paypal, 11px, $weight-light, left)

  .donate-btn
    fl-center()
    background-color $white
    border-radius 9px
    border 1px solid $paypal
    cursor pointer
    height 18px
    margin-bottom 8px
    transition all .3s
    width 92px
    &--round
      border-radius 50%
      height 26px
      width 26px
    &:hover
      opacity 0.5
    &__text
      fnt($paypal, 12px, $weight-normal, center)
      padding-left: 8px

</style>
