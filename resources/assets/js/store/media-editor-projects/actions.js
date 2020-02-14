import axios from 'axios';
import {
  SET_ALL,
  UPDATE_PROJECT,
  DELETE_PROJECT,
  ADD_PROJECT,
} from '../mutation-types';

export default {
  openPlayVideoDialog({ commit }) {
    commit(SET_ALL, { isPlayVideoDialogOpen: true });
  },
  openPlayVideoDialogId({ commit }, id) {
    commit(SET_ALL, { openPlayVideoDialogId: id });
  },
  closePlayVideoDialog({ commit }) {
    commit(SET_ALL, { isPlayVideoDialogOpen: false });
    commit(SET_ALL, { openPlayVideoDialogId: '' });
  },
  fetchProjects({ commit }) {
    commit(SET_ALL, { projectsLoading: true });
    return axios.get('/api/user/projects/fetch')
      .then(({ data }) => {
        commit(SET_ALL, {
          projects: data,
          projectsLoading: false,
        });
      })
      .catch((error) => {
        commit(SET_ALL, {
          projectsLoading: false,
        });
        return error;
      });
  },
  createProject({ commit }, { title, description }) {
    commit(SET_ALL, { projectCreateLoading: true });
    return axios.post('/api/user/project/create', { title, description })
      .then(({ data }) => {
        commit(ADD_PROJECT, {
          project: data,
        });
        commit(SET_ALL, {
          projectCreateLoading: false,
        });
        return data;
      })
      .catch((error) => {
        commit(SET_ALL, { projectCreateLoading: false });
        return error;
      });
  },
  deleteProject({ commit }, projectId) {
    commit(SET_ALL, { projectsUpdate: true });
    return axios.post('/api/user/project/delete', { id: projectId })
      .then((response) => {
        commit(SET_ALL, { projectsUpdate: false });
        commit(DELETE_PROJECT, { projectId });
        return response;
      })
      .catch((error) => {
        commit(SET_ALL, { projectsUpdate: false });
        return error;
      });
  },
  updateProject({ commit }, projectData) {
    if (!projectData.id) return false;
    commit(SET_ALL, { projectsUpdateLoading: true });
    return axios.post('/api/user/project/update', projectData)
      .then(({ data }) => {
        commit(SET_ALL, { projectsUpdateLoading: false });
        commit(UPDATE_PROJECT, data);
        return data;
      })
      .catch((error) => {
        commit(SET_ALL, { projectsUpdateLoading: false });
        return error;
      });
  },
};
