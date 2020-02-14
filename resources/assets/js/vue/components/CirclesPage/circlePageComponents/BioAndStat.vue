<template>
  <div class="bio-stat">
    <div class="bio">
      about
    </div>
    <div class="stat">
      <div class="stat__title">
        e-mail:
      </div>
      <a
        class="stat__data"
        href="#"
      >
        email
      </a>
    </div>
  </div>
</template>

<script>
/* @flow */
export default {
  components: {},
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .bio {
    padding     : 24px 0;
    font-size   : 13px;
    font-weight : $weight-normal;
    color       : $text-lighter;
    text-align  : center;
  }

  .stat {
    padding : 10px 0;
    &__title {
      font-size   : 11px;
      font-weight : $weight-normal;
      color       : $text-lighter;
      text-align  : center;
    }
    &__data {
      font-size   : 13px;
      font-weight : $weight-semibold;
      color       : $text;
      text-align  : center;
    }
  }
</style>
