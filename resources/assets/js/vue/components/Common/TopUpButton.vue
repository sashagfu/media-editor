<template>
  <div class="TopUpButton">
    <div
      class="top-up-btn"
      @click="openDonateDialog"
    >
      <font-awesome-icon
        :icon="['fas', 'dollar-sign']"
        class="fa-icon"
      />
      <div class="top-up-btn__text">
        {{ trans('profile_page.top_up') }}
      </div>
    </div>
    <TopUpDialog
      v-bind="$props"
      :is-dialog-open="isDonateDialogOpen"
      @close-dialog="closeDialog"
      @open-dialog="openDonateDialog"
    />
  </div>
</template>

<script>
import TopUpDialog from 'Pages/DonationsPage/TopUpDialog';

export default {
  name: 'TopUpButton',
  components: {
    TopUpDialog,
  },
  props: {
    user: {
      type: Object,
      default: () => ({}),
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

  .top-up-btn
    fl-center()
    background-color $white
    border 1px solid $paypal
    border-radius 9px
    cursor pointer
    height 18px
    margin-bottom 8px
    transition all .3s
    width 92px
    &:hover
      opacity 0.5
    &__text
      fnt($paypal, 12px, $weight-normal, center)
      padding-left 8px

</style>
