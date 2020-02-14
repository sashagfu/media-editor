import { ApolloClient } from 'apollo-client';
import { HttpLink } from 'apollo-link-http';
import { setContext } from 'apollo-link-context';
import { ApolloLink } from 'apollo-link';
import Pusher from 'pusher-js';
import { InMemoryCache, defaultDataIdFromObject } from 'apollo-cache-inmemory';
import CustomHeuristicFragmentMatcher from 'Helpers/customFragmentMatcher';
import PusherLink from 'Helpers/PusherLink';

const pusherLink = new PusherLink({
  pusher: new Pusher(window.Laravel.pusher_key, {
    cluster: window.Laravel.pusher_cluster,
    authEndpoint: `${window.Laravel.apiUrl}/graphql/subscriptions/auth`,
    auth: {
      headers: {
        authorization: `Bearer ${localStorage.getItem('accessToken')}`,
      },
      params: {
        _token: window.Laravel.csrfToken,
      },
    },
  }),
});


const httpLink = new HttpLink({
  // You should use an absolute URL here
  uri: `${window.Laravel.apiUrl}/graphql`,
});
// const cache = new InMemoryCache({ addTypename: false });

const authLink = setContext((_, { headers }) => {
  // get the authentication token from local storage if it exists
  const token = localStorage.getItem('accessToken');
  // return the headers to the context so httpLink can read them
  return {
    headers: {
      ...headers,
      authorization: `Bearer ${token}`,
    },
  };
});

const defaultOptions = {
  query: {
    fetchPolicy: 'network-only',
  },
};

// Create the apollo client
const apolloClient = new ApolloClient({
  link: authLink.concat(ApolloLink.from([pusherLink, httpLink])),
  cache: new InMemoryCache({
    dataIdFromObject: result => (result.__typename && result.id ? `${result.__typename}:${result.id}` : defaultDataIdFromObject(result)),
    fragmentMatcher: new CustomHeuristicFragmentMatcher(),
  }),
  // cache: new InMemoryCache({ addTypename: false }),
  defaultOptions,
  connectToDevTools: true,
});

export default apolloClient;
