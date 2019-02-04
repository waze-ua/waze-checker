import Component from '@ember/component';
import { get, set, computed } from '@ember/object';
import { inject as service } from '@ember/service';
import { task } from 'ember-concurrency';
import QueryMixin from 'waze-checker/mixins/query-mixin';

export default Component.extend(QueryMixin, {
  classNames: ['ui', 'grid'],
  store: service(),
  users: null,
  searchUser: null,

  init() {
    this._super(...arguments);
  },

  didReceiveAttrs() {
    this._super(...arguments);
    set(this, 'searchUser', get(this, 'queryParams.searchUser'));
    get(this, 'fetchUsers').perform(get(this, 'queryParams'));
  },

  fetchUsers: task(function*(params) {
    let query = {
      page: params.page,
    };

    if (params.searchUser !== '') {
      query['userName'] = `like|${params.searchUser}`;
    }

    if (params.order !== '') {
      let order = params.order.split(' ');
      query.order = {
        [order[0]]: order[1],
      };
    }

    let users = yield get(this, 'store').query('user', query);

    set(this, 'users', users);
  }),

  _updateParams(property, value) {
    get(this, 'updateQueryParameter')(property, value);
  },
});
