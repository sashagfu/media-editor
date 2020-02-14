import Vue from 'vue';
import _ from 'lodash';
import {
  SET_ALL,
  UPDATE_PROJECT,
  DELETE_PROJECT,
  ADD_PROJECT,
} from '../mutation-types';

export default {
  [SET_ALL](state, data) {
    Object.assign(state, data);
    return state;
  },
  [UPDATE_PROJECT](state, projectData) {
    const changingProject = state.projects.find(project => project.id === projectData.id);
    if (changingProject) {
      _.forEach(projectData, (val, key) => {
        Vue.set(changingProject, key, val);
      });
    }
    return state.projects;
  },
  [DELETE_PROJECT](state, { projectId }) {
    const projects = state.projects.filter(project => project.id !== projectId);
    Object.assign(state, { projects });
    return state.projects;
  },
  [ADD_PROJECT](state, { project }) {
    const projects = state.projects.concat(project);
    Object.assign(state, { projects });
    return state.projects;
  },
};
