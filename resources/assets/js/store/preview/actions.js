import { SET_ALL } from './mutation-types';

export default {
  setGalleryItemId({ commit, getters, dispatch }, { galleryItemId }) {
    if (galleryItemId === getters.galleryItemId) return;
    // reset player values
    dispatch('player/reset', { playing: true }, { root: true });
    commit(SET_ALL, { galleryItemId });
  },
  resetGalleryItemId({ getters, commit, dispatch }) {
    if (getters.galleryItemId) {
      dispatch('player/reset', null, { root: true });
      commit(SET_ALL, { galleryItemId: null });
    }
  },
};
