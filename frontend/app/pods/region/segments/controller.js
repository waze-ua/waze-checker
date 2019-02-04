import Controller from '@ember/controller';
import { set } from '@ember/object';
import QueryParams from 'ember-parachute';
export const queryParams = new QueryParams({
  page: {
    defaultValue: 1,
    refresh: true,
  },
  type: {
    defaultValue: 'all',
    refresh: true,
  },
  qRoadTypes: {
    defaultValue: [],
    refresh: true,
    serialize(value) {
      return value.toString();
    },
    deserialize(value = '') {
      return value.split(',');
    },
  },
  lockRanks: {
    defaultValue: [],
    refresh: true,
  },
  searchCity: {
    defaultValue: '',
    refresh: true,
  },
  searchStreet: {
    defaultValue: '',
    refresh: true,
  },
  searchUser: {
    defaultValue: '',
    refresh: true,
  },
  order: {
    defaultValue: 'id asc',
    refresh: true,
  },
});

export default Controller.extend(queryParams.Mixin, {
  actions: {
    updateQueryParameter(param, paramValue) {
      if (typeof param === 'object') {
        this.setProperties(param);
      } else {
        set(this, param, paramValue);
      }
    },
  },
});
