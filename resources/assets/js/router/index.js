import Vue from 'vue';
import Router from 'vue-router';
import apollo from 'Gql';
import FETCH_ME from 'Gql/users/queries/fetchMe.graphql';
import { store } from './../store';

import routes from './routes';

const NON_AUTH_PAGES = [
  'HomePage',
  'LoginPage',
  'RegisterPage',
  'VerifyPage',
  'ResetPasswordPage',
];

Vue.use(Router);

const router = new Router({
  mode: 'history',
  base: process.env.BASE_URL,
  routes,
});

router.beforeEach(async (to, from, next) => {
  const user = store.getters['general/activeUser'];
  const accessToken = localStorage.getItem('accessToken');

  if (NON_AUTH_PAGES.includes(to.name) && !accessToken) {
    next();
    return;
  }

  if (!accessToken) {
    store.dispatch('general/setActiveUser', null);
    next('/login');
  }

  if (NON_AUTH_PAGES.includes(to.name) && accessToken) {
    next('/feed');
    return;
  }

  if (!user) {
    try {
      const { data: { fetchMe } } = await apollo.query({
        query: FETCH_ME,
      });
      store.dispatch('general/setActiveUser', fetchMe);
      next();
    } catch (error) {
      localStorage.removeItem('accessToken');
      next('/login');
    }
  }

  next();
});

export default router;
