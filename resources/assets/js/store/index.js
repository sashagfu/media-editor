import Vue from 'vue';
import Vuex from 'vuex';
import mediaEditorProjects from './media-editor-projects';
import general from './general';
import chat from './chat';
import feed from './feed';
import notifications from './notifications';
import upload from './upload';
import project from './project';
import gallery from './gallery';
import player from './player';
import preview from './preview';
import dragging from './dragging';
import coordinates from './coordinates';
import settings from './settings';
import text from './text';
import timeline from './timeline';

Vue.use(Vuex);
export const store = new Vuex.Store({
  state: {},
  modules: {
    mediaEditorProjects,
    general,
    chat,
    feed,
    notifications,
    upload,
    project,
    gallery,
    player,
    preview,
    dragging,
    coordinates,
    settings,
    text,
    timeline,
  },
});

export default store;
