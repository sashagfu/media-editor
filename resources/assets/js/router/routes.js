import apolloClient from 'Gql';
import VERIFY_USER from 'Gql/users/mutations/verifyUser.graphql';

import HomePage from './../vue/components/HomePage/HomePage';
import HomeFeed from './../vue/components/HomeFeed/HomeFeed';
import ProfilePage from '../vue/components/ProfilePage/ProfilePage';
import NetworkingPage from '../vue/components/NetworkingPage/NetworkingPage';
import SingleProjectPage from '../vue/components/SingleProjectPage/SingleProjectPage';
import DonationsPage from '../vue/components/DonationsPage/DonationsPage';
import SearchResultsPage from '../vue/components/SearchResultsPage/SearchResultPage';
import ProjectStatisticsPage from '../vue/components/ProjectStatisticsPage/ProjectStatisticsPage';
import EditorPage from '../vue/components/EditorPage/EditorPage';
import VerifyPage from '../vue/components/VerifyPage/VerifyPage';
import ResetPasswordPage from '../vue/components/ResetPasswordPage/ResetPasswordPage';

const routes = [
  {
    path: '/',
    name: 'HomePage',
    component: HomePage,
  },
  {
    path: '/login',
    name: 'LoginPage',
    component: HomePage,
  },
  {
    path: '/register',
    name: 'RegisterPage',
    component: HomePage,
  },
  {
    path: '/verify/:email([^@]+@[^\\.]+\\..+)/:verificationCode',
    name: 'VerifyPage',
    component: VerifyPage,
    props: true,

    async beforeEnter(to, from, next) {
      try {
        await apolloClient.mutate({
          mutation: VERIFY_USER,
          variables: {
            email: to.params.email,
            verificationCode: to.params.verificationCode,
          },
        });
        next();
      } catch (error) {
        console.error(error);
        next('/register');
      }
    },
  },
  {
    path: '/password/reset/:token',
    name: 'ResetPasswordPage',
    component: ResetPasswordPage,
    props: true,
  },
  {
    path: '/feed',
    name: 'FeedPage',
    component: HomeFeed,
  },
  {
    path: '/profile/:username',
    name: 'ProfilePage',
    component: ProfilePage,
    props: true,
  },
  {
    path: '/network',
    name: 'NetworkingPage',
    component: NetworkingPage,
  },
  {
    path: '/projects/:projectUuid',
    name: 'SingleProjectPage',
    component: SingleProjectPage,
  },
  {
    path: '/donations',
    name: 'DonationsPage',
    component: DonationsPage,
  },
  {
    path: '/search',
    name: 'SearchResultsPage',
    component: SearchResultsPage,
  },
  {
    path: '/projects/:projectUuid/statistics',
    name: 'ProjectStatisticsPage',
    component: ProjectStatisticsPage,
  },
  {
    path: '/editor/:id(\\d+)',
    name: 'EditorPage',
    component: EditorPage,
    props: true,
  },
];

export default routes;
