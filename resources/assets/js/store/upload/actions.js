import Vue from 'vue';
import Evaporate from 'evaporate';
import CREATE_FILE from 'Gql/projects/mutations/createProjectFile.graphql';
import CREATE_FILES from 'Gql/projects/mutations/createProjectFiles.graphql';
import ApolloClient from 'Gql';
import moment from 'moment';

import {
  SET_ALL,
  ADD_FILE,
  SET_ITEM,
} from '../mutation-types';

export default {
  createUploader({ commit }) {
    const {
      awsSignUrl,
      awsKey,
      awsBucket,
    } = window.Laravel;
    const uploader = new Evaporate({
      signerUrl: awsSignUrl,
      aws_key: awsKey, // eslint-disable-line camelcase
      bucket: awsBucket,
      cloudfront: true,
      logging: false,
      awsSignatureVersion: 4,
    });
    commit(SET_ALL, { uploader });
  },
  openCreateDialog({ commit }) {
    commit(SET_ALL, { isCreateProjectDialogOpen: true });
  },
  openDialog({ commit }) {
    commit(SET_ALL, { isDialogOpen: true });
  },
  closeCreateDialog({ commit }) {
    commit(SET_ALL, { isCreateProjectDialogOpen: false });
  },
  closeDialog({ commit }) {
    commit(SET_ALL, { isDialogOpen: false });
  },

  addFile({ commit }, file) {
    commit(ADD_FILE, { file });
  },
  resetFiles({ commit }) {
    commit(SET_ALL, { files: [] });
  },
  startUpload({ dispatch, getters }) {
    dispatch('setUploading', true);
    getters.files.forEach((file) => {
      if (!file.uploaded && !file.uploading) {
        file.uploading = true; // eslint-disable-line
        getters.uploader.add(file);
      }
    });
  },
  clearFiles({ commit, getters }) {
    const files = getters.files.filter(file => file.uploading);
    commit(SET_ALL, { files });
  },
  removeFile({ commit, getters }, { id }) {
    const files = getters.files.filter(file => file.id !== id);
    commit(SET_ALL, { files });
  },
  cancelUpload({ getters }, { id } = {}) {
    let fileId;
    if (id) {
      const f = getters.files.find(file => file.id === id);
      fileId = `${getters.uploader.config.bucket}/${f.name}`;
    }
    getters.uploader.cancel(fileId);
  },
  uploadReadyProject({ commit }, isReady) {
    commit(SET_ITEM, { isReady });
  },
  processUploadedFile({
    rootGetters,
    dispatch,
    getters,
    commit,
  }, { file, xhr }) {
    const url = xhr.responseURL.split('?')[0];
    const projectId = rootGetters['general/newProjectId'];
    const { isReady } = getters;
    ApolloClient.mutate({
      mutation: CREATE_FILE,
      variables: {
        url,
        projectId,
        isReady,
      },
    })
      .then(({ data: { createProjectFile } }) => {
        Vue.set(file, 'uploaded', true);
        Vue.set(file, 'uploading', false);

        const files = getters.files.filter(fl => fl.id !== file.id);
        commit(SET_ALL, { files });

        dispatch('gallery/addItem', { item: createProjectFile }, { root: true });
      })
      .catch((error) => {
        console.error(error);
      });
  },
  processUploadedFiles({
    rootGetters,
    getters,
    commit,
    dispatch,
  }, filesData) {
    const fileUrls = filesData.map((f) => {
      const url = f.xhr.responseURL.split('?')[0];

      return url;
    });
    // if this is not creating new project
    const projectId = (rootGetters['general/newProjectId'])
      ? rootGetters['general/newProjectId']
      : rootGetters['project/id'];

    const { isReady } = getters;

    ApolloClient.mutate({
      mutation: CREATE_FILES,
      variables: {
        urls: fileUrls,
        projectId,
        isReady,
      },
    })
      .then(() => {
        filesData.forEach((fd) => {
          Vue.set(fd.file, 'uploaded', true);
          Vue.set(fd.file, 'uploading', false);
        });

        // Clear files
        const files = [];
        const uploadedFiles = [];
        commit(SET_ALL, { files, uploadedFiles });
        dispatch('setUploading', false);
        dispatch('setProcessing', true);
        localStorage.setItem('uploadingFiles', moment().format('YYYY-MM-DD HH:mm:ss'));
      })
      .catch(error => console.error(error));
  },
  addToUploaded({ getters, commit }, file) {
    const { uploadedFiles } = getters;
    uploadedFiles.push(file);

    commit(SET_ALL, { uploadedFiles });
  },
  setProcessing({ commit }, status) {
    commit(SET_ALL, { processing: status });
  },
  setUploading({ commit }, status) {
    commit(SET_ALL, { uploading: status });
  },
};
