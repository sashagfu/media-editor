<template>
  <div class="MainWithPicture post-main-with-picture">
    <div class="main__photo"/>
    <div class="main__title">
      Lorem ipsum dolor sit amet, consectadipisicing
    </div>
    <div class="main__text">
      <p class="main__text-item">
        Lorem ipsum dolor sit amet, consectadipisicing elit, sed
        do eiusmod por incidid ut labore et dolore magna aliqua.
        Ut enim ad minim veniam, quis nostrud lorem exercitation ullamco
        laboris nisi ut aliquip ex ea commodo consequat.
        Duis en aute irure dolor in reprehenderit
        in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
        Excepteur sint occaecat cupidatat
        non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
      </p>
      <p class="main__text-item">
        Sed ut perspiciatis unde omnis iste natus error sit voluptatem
        accusantium doloremque laudantium,
        totam rem aperiam, eaque ipsa quae ab illo inventore veritatis
        et quasi architecto beatae vitae
        dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas
        sit aspernatur aut odit aut fugit,
        sed quia consequuntur magni dolores eos qui ratione
        voluptatem sequi nesciunt...
      </p>
    </div>
    <div class="main__read-more-btn">
      <button class="btn">
        Read More
      </button>
    </div>
  </div>
</template>

<script>

export default {
  components: {},
};
</script>

<style
  lang="stylus"
  scoped
>
  @import '../../../../../sass/front/components/bulma-theme';

  .main {
    &__photo {
      background : url('http://lorempixel.com/750/400/nature') center center no-repeat $primary;
      height     : 220px;
      width      : 100%;
    }
    &__title {
      color       : $text-light;
      font-size   : 36px;
      font-weight : $weight-light;
      padding     : 36px 26px;
      text-align  : left;
    }
    &__text {
      color       : $text-light;
      font-size   : 14px;
      font-weight : $weight-light;
      padding     : 0 26px;
      text-align  : left;
    }
    &__text-item {
      padding-bottom : 26px;
      &:last-child {
        padding-bottom : 8px;
      }
    }
    &__read-more-btn {
      display : flex;
      padding : 26px;
    }
  }

  .btn {
    background-color : transparent;
    border           : 1px solid $border;
    border-radius    : 3px;
    color            : $text-light;
    font-size        : 11px;
    font-weight      : $weight-semibold;
    height           : 32px;
    outline          : none;
    transition       : all .3s;
    width            : 104px;
    &:hover {
      background-color : $primary;
      border           : 1px solid $primary;
      color            : $text-invert;
    }
  }

</style>
