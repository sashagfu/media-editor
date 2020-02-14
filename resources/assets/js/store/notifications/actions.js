import axios from 'axios';
import { SET_ITEM, ADD_ITEM } from '../mutation-types';

export default {
  setUnreadNotificationsCount({ commit }, data) {
    if (data) {
      commit(SET_ITEM, { unreadNotificationsCount: data.notifications_count });
    } else {
      axios.get('/api/profile/notifications/count').then((response) => {
        commit(SET_ITEM, { unreadNotificationsCount: response.data });
      });
    }
  },

  addNotifications({ commit }, notifications) {
    commit(ADD_ITEM, { notifications });
  },
  setNotifications({ commit }) {
    axios.get('/api/notifications/get')
      .then(({ data }) => {
        commit(SET_ITEM, { notifications: data.data });
        commit(SET_ITEM, { nextPageUrl: data.nextPageUrl });
      });
  },
  readAllNotifications({ commit, getters }) {
    if (getters.unreadNotificationsCount > 0) {
      commit(SET_ITEM, { readingLoading: true });

      axios.post('/api/notifications/read/all')
        .then(() => {
          commit(SET_ITEM, { unreadNotificationsCount: 0 });
          const readNotifications = getters.notifications.map((notif) => {
            const n = Object.assign({}, notif);
            if (!n.read_at) {
              n.read_at = Date.now();
            }
            return n;
          });
          commit(SET_ITEM, { notifications: readNotifications });
          commit(SET_ITEM, { readingLoading: false });
        }).catch(() => {
          // }).catch((error) => {
          // console.log('notification/actions readAllNotifications error ', error);
          commit(SET_ITEM, { readingLoading: false });
        });
    }
  },
  scrollNotifications({ commit, getters }) {
    if (getters.nextPageUrl === null) {
      return;
    }
    commit(SET_ITEM, { notifLoading: true });
    axios.get(getters.nextPageUrl)
      .then(({ data }) => {
        commit(SET_ITEM, { notifLoading: false });
        commit(ADD_ITEM, { notifications: data.data });
        commit(SET_ITEM, { nextPageUrl: data.next_page_url });
      }).catch(() => {
        // }).catch((error) => {
        //     console.log('notification/actions scrollNotifications error ', error);
        commit(SET_ITEM, { notifLoading: false });
      });
  },
};
