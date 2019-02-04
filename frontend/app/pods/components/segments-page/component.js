import Component from '@ember/component';
import { get, set, computed } from '@ember/object';
import { inject as service } from '@ember/service';
import { task } from 'ember-concurrency';
import { debounce } from '@ember/runloop';
import QueryMixin from 'waze-checker/mixins/query-mixin';

export default Component.extend(QueryMixin, {
  tagName: '',

  store: service(),

  segments: null,
  lockRanks: null,
  searchCity: null,
  searchUser: null,
  searchStreet: null,

  init() {
    this._super(...arguments);
    this.lockRanks = [
      { id: 0, name: 'auto (1)' },
      { id: 1, name: '1' },
      { id: 2, name: '2' },
      { id: 3, name: '3' },
      { id: 4, name: '4' },
      { id: 5, name: '5' },
      { id: 6, name: '6' },
    ];
  },

  didReceiveAttrs() {
    this._super(...arguments);
    set(this, 'searchCity', get(this, 'queryParams.searchCity'));
    set(this, 'searchStreet', get(this, 'queryParams.searchStreet'));
    set(this, 'searchUser', get(this, 'queryParams.searchUser'));
    get(this, 'fetchSegments').perform(get(this, 'queryParams'));
  },

  selectedRoadTypes: computed(
    'queryParams.qRoadTypes.[]',
    'roadTypes.[]',
    function() {
      if (
        get(this, 'roadTypes.length') > 0 &&
        get(this, 'queryParams.qRoadTypes.length') > 0
      ) {
        let selectedRoadTypes = get(this, 'roadTypes').filter(item => {
          return get(this, 'queryParams.qRoadTypes').includes(item.id);
        });
        return selectedRoadTypes;
      }
      return [];
    }
  ),

  selectedLockRanks: computed(
    'queryParams.lockRanks.[]',
    'lockRanks.[]',
    function() {
      if (
        get(this, 'lockRanks.length') > 0 &&
        get(this, 'queryParams.lockRanks.length') > 0
      ) {
        let selectedLockRanks = get(this, 'lockRanks').filter(item => {
          return get(this, 'queryParams.lockRanks').includes(item.id);
        });
        return selectedLockRanks;
      }
      return [];
    }
  ),

  getQueryForType(type) {
    if (type == 'withoutSpeed') {
      return {
        or: [
          { and: ['fwdMaxSpeed:0', 'fwdDirection:1'] },
          { and: ['revMaxSpeed:0', 'revDirection:1'] },
        ],
      };
    }

    if (type == 'speedMore90InCity') {
      return {
        and: [
          'street.city.isEmpty:0',
          { or: ['fwdMaxSpeed:>=|90', 'revMaxSpeed:>=|90'] },
        ],
      };
    }

    if (type == 'withLowLock') {
      return {
        or: [
          { and: ['roadType:in|4,7', 'lockRank:<|2'] },
          { and: ['roadType:in|3,6', 'lockRank:<|3'] },
        ],
      };
    }

    if (type == 'withoutTurn') {
      return null;
    }

    if (type == 'notConnected') {
      return null;
    }

    if (type == 'short') {
      return 'length:<|5';
    }

    if (type == 'withNameWithoutCity') {
      return {
        and: [
          'street:>|0',
          'street.city.isEmpty:1',
          {
            or: [
              'street.name:like|улица',
              'street.name:like|iela',
              'street.name:like|проспект',
              'street.name:like|переулок',
              'street.name:like|проезд',
              'street.name:like|площадь',
              'street.name:like|шоссе',
              'street.name:like|тракт',
            ],
          },
        ],
      };
    }

    if (type == 'unpaved') {
      return { or: ['flags:16', 'flags:17'] };
    }

    if (type == 'new') {
      return 'updatedBy:-1';
    }

    if (type == 'toll') {
      let or = [];
      or.push({ and: ['fwdDirection:1', 'fwdToll:1'] });
      or.push({ and: ['revDirection:1', 'revToll:1'] });
      return { or };
    }

    if (type == 'withAverageSpeedCamera') {
      return { or: ['fwdFlags:1', 'revFlags:1'] };
    }

    if (type == 'new') {
      return 'updatedBy:-1';
    }

    if (type == 'revDirection') {
      return { and: ['fwdDirection:0', 'revDirection:1'] };
    }

    return null;
  },

  fetchSegments: task(function*(params) {
    let query = {
      query: [],
      region: get(this, 'region.id'),
      page: params.page,
      //include: 'street,updatedBy',
    };

    let queryForType = this.getQueryForType(params.type);
    if (queryForType) {
      query.query.push(queryForType);
    }

    if (params.qRoadTypes.length > 0) {
      query.roadType = params.qRoadTypes.toString();
    }

    if (params.lockRanks.length > 0) {
      query.lockRank = params.lockRanks.toString();
    }

    if (params.searchCity !== '') {
      query['street.city.name'] = `LIKE|${params.searchCity}`;
    }

    if (params.searchUser !== '') {
      query['updatedBy.userName'] = `LIKE|${params.searchUser}`;
    }

    if (params.searchStreet !== '') {
      query['street.name'] = `LIKE|${params.searchStreet}`;
    }

    if (params.order !== '') {
      let order = params.order.split(' ');
      query.order = {
        [order[0]]: order[1],
      };
    }

    let segments = yield get(this, 'store').query('segment', query);
    set(this, 'segments', segments);
  }),

  _updateParams(property, value) {
    get(this, 'updateQueryParameter')(property, value);
  },

  actions: {
    updateState() {
      get(this, 'fetchSegments').perform(get(this, 'queryParams'));
    },

    selectRoadTypes(roadTypes) {
      let queryRoadTypes = roadTypes.map(item => item.id);
      debounce(this, '_updateParams', 'qRoadTypes', queryRoadTypes, 700);
    },

    selectLockRanks(lockRanks) {
      let queryLockRanks = lockRanks.map(item => item.id);
      debounce(this, '_updateParams', 'lockRanks', queryLockRanks, 700);
    },

  },
});
