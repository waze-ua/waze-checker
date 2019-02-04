import Controller from '@ember/controller';
import { set } from '@ember/object';
import QueryParams from 'ember-parachute';

export const myQueryParams = new QueryParams({
  page: {
    defaultValue: 1,
    refresh: true,
  },
  searchUser: {
    defaultValue: '',
    refresh: true,
  },
  order: {
    defaultValue: 'lastEdit desc',
    refresh: true,
  },
});

export default Controller.extend(myQueryParams.Mixin, {
  actions: {
    updateQueryParameter(paramName, paramValue) {
      set(this, paramName, paramValue);
    },
  },
});
