import _ from 'lodash';
import { isObjectChange } from 'Editor/utils/helpers';
import {
  SET_ALL,
  SET_PROJECT_DATA,
  SET_FETCHED_PROJECT_DATA,
  ADD_ITEM,
  CHANGE_ITEM,
  UPDATE_ITEMS,
  REMOVE_ITEMS,
  SET_LAYER_PROPS,
  RESTORE_FETCHED_PROJECT_DATA,
} from './mutation-types';

export default {
  [SET_ALL](state, data) {
    Object.assign(state, data);
    return state;
  },
  [SET_PROJECT_DATA](state, projectData) {
    Object.assign(state.projectData, projectData);
    return state.projectData.id;
  },
  [SET_FETCHED_PROJECT_DATA](state, data) {
    const projectData = data;
    if (projectData.value === 'null' || _.isNull(projectData.value)) {
      projectData.value = [];
    }
    Object.assign(state.projectData, projectData);
    Object.assign(state, {
      saved: true,
      fetchedProjectData: JSON.stringify(data),
    });
    return state;
  },
  [RESTORE_FETCHED_PROJECT_DATA](state) {
    Object.assign(state.projectData, JSON.parse(state.fetchedProjectData));
    return state.projectData;
  },
  [ADD_ITEM](state, item) {
    Object.assign(state.projectData, {
      value: state.projectData.value.concat(item),
    });
    Object.assign(state, { saved: false });
    return state.projectData.value;
  },
  [CHANGE_ITEM](state, itemData) {
    const { value: items } = state.projectData;
    const itemIndex = items.findIndex(item => itemData.id === item.id);
    if (isObjectChange(items[itemIndex], itemData)) {
      items[itemIndex] = Object.assign({}, items[itemIndex], itemData);
      Object.assign(state.projectData, { value: items.concat() });
      Object.assign(state, { saved: false });
    }
    return items[itemIndex];
  },
  /**
     * Changes items took in payload as array, if item isn't exist, add it
     * @param state
     * @param itemsData {array}
     * @returns {*}
     */
  [UPDATE_ITEMS](state, itemsData) {
    if (!Array.isArray(itemsData)) {
            console.error(`mutation project/${UPDATE_ITEMS} accept array as payload, but get ${typeof itemsData}`); // eslint-disable-line
      return false;
    }
    itemsData.forEach((itemData) => {
      const { value: items } = state.projectData;
      const itemIndex = items.findIndex(item => itemData.id === item.id);
      if (itemIndex === -1) {
        Object.assign(state.projectData, {
          value: state.projectData.value.concat(itemData),
        });
      } else {
        items[itemIndex] = Object.assign({}, items[itemIndex], itemData);
        Object.assign(state.projectData, { value: items.concat() });
      }
    });
    Object.assign(state, { saved: false });
    return state.projectData.value;
  },
  [REMOVE_ITEMS](state, { itemIds }) {
    let newItems = state.projectData.value;
    itemIds.forEach((id) => {
      newItems = newItems.filter(item => item.id !== id);
    });
    Object.assign(state.projectData, { value: newItems });
    Object.assign(state, { saved: false });
    return state.projectData.value;
  },
  [SET_LAYER_PROPS](state, layerData) {
    const layers = state.projectData.layers.map((layer) => {
      if (layer.id === layerData.id) {
        return Object.assign({}, layer, layerData);
      }
      return layer;
    });
    Object.assign(state.projectData, { layers });
    return state.projectData.layers;
  },
};
