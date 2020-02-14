import _ from 'lodash';

export default {
  galleryItemId: state => state.galleryItemId,
  galleryItem: (state, getters, rootState, rootGetters) => {
    if (_.isNil(state.galleryItemId)) return {};
    return rootGetters['gallery/items'].find(item => item.id === state.galleryItemId)
            || rootGetters['gallery/itemsText'].find(item => item.id === state.galleryItemId);
  },
};
