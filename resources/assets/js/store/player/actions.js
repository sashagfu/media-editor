import { SET_ALL } from './mutation-types';
import initialState from './state';

export default {
  togglePlaying({ commit, getters, rootGetters }) {
    const items = rootGetters['project/items'];
    if (items.length) {
      commit(SET_ALL, { playing: !getters.playing });
    }
  },
  setCurrentTime({ commit }, currentTime) {
    commit(SET_ALL, { currentTime });
  },
  setDuration({ commit }, duration) {
    commit(SET_ALL, { duration });
  },
  setLoading({ commit }, loading) {
    commit(SET_ALL, { loading });
  },
  reset({ commit }, stateChanges = {}) {
    commit(SET_ALL, Object.assign(initialState(), stateChanges));
  },
};
