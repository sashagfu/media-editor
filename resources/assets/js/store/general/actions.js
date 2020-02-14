import { SET_ITEM } from '../mutation-types';

export default {
  setActiveUser({ commit }, activeUser = null) {
    console.log(activeUser);
    commit(SET_ITEM, { activeUser });
  },
  setHomePageActiveBox({ commit }, activeBox) {
    commit(SET_ITEM, { homePageActiveBox: activeBox });
  },
  setNewProject({ commit }, project) {
    commit(SET_ITEM, { newProject: project });
  },
};

