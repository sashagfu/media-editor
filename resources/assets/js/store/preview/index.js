import actions from './actions';
import getters from './getters';
import mutations from './mutations';
import state from './state';
import project from './modules/project';

export default {
  namespaced: true,
  actions,
  getters,
  mutations,
  state,
  modules: {
    project,
  },
};
