import { ApolloLink, Observable } from 'apollo-link';

class PusherLink extends ApolloLink {
  constructor(options) {
    super();
    // Retain a handle to the Pusher client
    this.pusher = options.pusher;
  }

  request(operation, forward) {
    return new Observable((observer) => {
      // Check the result of the operation
      forward(operation).subscribe({
        next: (data) => {
          // If the operation has the subscription extension, it's a subscription
          // eslint-disable-next-line no-underscore-dangle
          const subscriptionChannel = this._getChannel(
            data,
            operation,
          );

          if (subscriptionChannel) {
            // eslint-disable-next-line no-underscore-dangle
            this._createSubscription(subscriptionChannel, observer);
          } else {
            // No subscription found in the response, pipe data through
            observer.next(data);
            observer.complete();
          }
        },
      });
    });
  }
  // eslint-disable-next-line class-methods-use-this
  _getChannel(data, operation) {
    return !!data.extensions &&
    !!data.extensions.lighthouse_subscriptions &&
    !!data.extensions.lighthouse_subscriptions.channels
      ? data.extensions.lighthouse_subscriptions.channels[operation.operationName]
      : null;
  }

  _createSubscription(subscriptionChannel, observer) {
    const pusherChannel = this.pusher.subscribe(subscriptionChannel);
    // Subscribe for more update
    pusherChannel.bind('lighthouse-subscription', (payload) => {
      // if (!payload.more) {
      if (!payload.more) {
        // This is the end, the server says to unsubscribe
        this.pusher.unsubscribe(subscriptionChannel);
        observer.complete();
      }
      const { result } = payload;
      if (result) {
        // Send the new response to listeners
        observer.next(result);
      }
    });
  }
}

export default PusherLink;
