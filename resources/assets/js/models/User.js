export default class User {
    id = 0;
    username = '';
    email = '';
    displayName = '';
    avatar = '';
    isFollowing = false;
    canBeFollowed = true;

    constructor({
      id,
      username,
      email,
      display_name, // eslint-disable-line camelcase
      avatar,
      isFollowing,
      canBeFollowed,
    }) {
      this.id = id;
      this.username = username;
      this.email = email;
      this.displayName = display_name; // eslint-disable-line camelcase
      this.avatar = avatar;
      this.isFollowing = isFollowing;
      this.canBeFollowed = canBeFollowed;
    }
}
