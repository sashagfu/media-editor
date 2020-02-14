import uuid from 'uuid';
import _ from 'lodash';
import moment from 'moment';

import ApolloClient from 'Gql';
import FETCH_PROJECT from 'Gql/projects/queries/fetchProjectValue.graphql';
import UPDATE_PROJECT from 'Gql/projects/mutations/updateProject.graphql';

import * as fileTypes from 'Editor/config/file-types';

import { ITEM_THUMB } from 'Editor/config/settings';
import {
  getInitialRelativePosition,
  getInitialRelativeSize,
} from 'Editor/utils/item-transform';
import { cloneDeep, isObjectChange } from 'Editor/utils/helpers';

import {
  SET_ALL,
  SET_FETCHED_PROJECT_DATA,
  SET_PROJECT_DATA,
  ADD_ITEM,
  CHANGE_ITEM,
  UPDATE_ITEMS,
  REMOVE_ITEMS,
  SET_LAYER_PROPS,
} from './mutation-types';

export default {
  renameProject({ commit }, projectData) {
    const { title, description, tags } = projectData;
    commit(SET_PROJECT_DATA, {
      title,
      description,
      tags,
    });
  },

  setProjectData({ commit }, projectData) {
    commit(SET_PROJECT_DATA, projectData);
  },

  async fetchProject({ commit }, { id }) {
    commit(SET_PROJECT_DATA, { id });

    try {
      const { data: { fetchProject } } = await ApolloClient.query({
        query: FETCH_PROJECT,
        variables: {
          id,
        },
      });
      commit(SET_FETCHED_PROJECT_DATA, cloneDeep(fetchProject));
    } catch (e) {
      console.error(e);
    } finally {
      commit(SET_ALL, { loading: false });
    }
  },
  async saveProject({ commit, getters, rootGetters }) {
    if (getters.saving || getters.saved) return false;

    try {
      commit(SET_ALL, { saving: true });
      const { data: { updateProject } } = await ApolloClient.mutate({
        mutation: UPDATE_PROJECT,
        variables: {
          project: cloneDeep(getters.projectData),
        },
      });

      const projectData = cloneDeep(getters.projectData);

      const playing = rootGetters['player/playing'];
      if (playing) {
        // Set update project value when playing will be paused
        commit('SET_ALL', {
          pendingProjectValue: updateProject.value,
        });
      } else {
        // Update project value
        Object.assign(projectData, {
          ...projectData,
          value: updateProject.value,
        });

        commit(SET_ALL, {
          projectData,
        });
      }

      commit(SET_ALL, {
        saved: true,
      });
      return updateProject;
    } catch (e) {
      console.error(e);
      return e;
    } finally {
      commit(SET_ALL, { saving: false });
    }
  },
  addItem({ commit, rootGetters }, {
    itemUuid,
    layerId,
    position,
    maxLength,
  }) {
    let file = rootGetters['gallery/items'].find(galleryItem => (
      _.toString(galleryItem.uuid) === _.toString(itemUuid)
    ));
    if (!file) { // if item is text frame
      file = rootGetters['gallery/itemsText'].find(galleryItem => (
        _.toString(galleryItem.uuid) === _.toString(itemUuid)
      ));
    }
    if (!file) { // if item in clips saved
      file = rootGetters['gallery/itemsClip'].find(galleryItem => (
        _.toString(galleryItem.uuid) === _.toString(itemUuid)
      ));
    }
    if (!file) { // if item in recent projects saved
      file = rootGetters['gallery/itemsRecentProjects'].find(galleryItem => (
        _.toString(galleryItem.uuid) === _.toString(itemUuid)
      ));
    }
    // If time is defined, set it if not means it's image then set image default duration
    let length = !_.isUndefined(file.time)
      ? parseFloat(file.time)
      : rootGetters['settings/itemThumb'].imageDefaultDuration;
    // Limit length by maxLength
    length = length > maxLength ? maxLength : length;

    const item = {
      id: uuid(),
      uuid: uuid(),
      layerId,
      position,
      file,
      length,
      startFrom: 0,
      transform: {
        scale: 1,
        position: getInitialRelativePosition(file),
        size: getInitialRelativeSize(file),
      },
    };
    if (file && !item.file.fileType !== fileTypes.IMAGE) {
      Object.assign(item, {
        volumeControl: [{
          id: '0',
          length: 0,
          level: 1,
        }, {
          id: '1',
          length: 0,
          level: 1,
        }],
      });
    }
    commit(ADD_ITEM, item);
  },
  removeItems({ commit }, selectedItems) {
    commit(REMOVE_ITEMS, { itemIds: selectedItems });
  },
  changeItem({ commit, getters }, itemData) {
    const { items } = getters;
    const item = items.find(it => itemData.id === it.id);

    if (isObjectChange(item, itemData)) {
      commit(CHANGE_ITEM, itemData);
    }
  },
  splitItemByPx({ dispatch, rootGetters }, { item, splitPositionPx }) {
    const zoomedPxPerSec = rootGetters['timeline/zoomedPxPerSec'];
    const splitPosition = (splitPositionPx + ITEM_THUMB.boxBorder) / zoomedPxPerSec;
    dispatch('splitItem', {
      item,
      splitPosition,
    });
  },
  splitItem({ commit }, { item, splitPosition }) {
    const newItem = Object.assign(_.cloneDeep(item), {
      id: uuid(),
      uuid: uuid(),
      position: item.position + splitPosition,
      length: item.length - splitPosition,
      startFrom: item.startFrom + splitPosition,
    });
    // commit(ADD_ITEM, newItem);

    const changedItem = {
      id: item.id,
      length: splitPosition,
    };
    // commit(CHANGE_ITEM, changedItem);
    commit(UPDATE_ITEMS, [newItem, changedItem]);
  },
  unlinkAudioFromItems({ commit }, items) {
    items.forEach((item) => {
      const newItemAsAudio = Object.assign(_.cloneDeep(item), {
        id: uuid(),
        uuid: uuid(),
        showAs: fileTypes.AUDIO,
        type: fileTypes.AUDIO,
      });
      commit(ADD_ITEM, newItemAsAudio);
      commit(CHANGE_ITEM, {
        id: item.id,
        unlinked: true,
      });
    });
  },
  setLayerVolume({ commit }, { layerId, volume }) {
    commit(SET_LAYER_PROPS, {
      id: layerId,
      volume,
    });
  },
  toggleHeightLayer({ commit }, { id, height }) {
    commit(SET_LAYER_PROPS, {
      id,
      height,
    });
  },
  setCoverImage({ commit, rootGetters }) {
    const cursorPosition = moment.duration(rootGetters['timeline/cursorPosition']);
    let hh = cursorPosition.hours();
    if (hh < 10) {
      hh = `0${hh}`;
    }
    let mm = cursorPosition.minutes();
    if (mm < 10) {
      mm = `0${mm}`;
    }
    let ss = cursorPosition.seconds();
    if (ss < 10) {
      ss = `0${ss}`;
    }
    let mss = cursorPosition.milliseconds();
    if (mss < 100 && mss > 9) {
      mss = `0${mss}`;
    } else if (mss <= 9) {
      mss = `00${mss}`;
    }
    const thumbTime = `${hh}:${mm}:${ss}.${mss}`;
    commit(SET_PROJECT_DATA, { thumbTime });
    commit(SET_ALL, { saved: false });
  },

  setPendingProjectValue({ commit }, value) {
    commit('SET_ALL', {
      pendingProjectValue: value,
    });
  },
};
