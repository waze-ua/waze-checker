import Component from '@ember/component';
import { get, set } from '@ember/object';
import { inject as service } from '@ember/service';
import { task } from 'ember-concurrency';

export default Component.extend({
  store: service(),

  regions: null,

  init() {
    this._super(...arguments);
    get(this, 'fetchRegions').perform();
  },

  fetchRegions: task(function*() {
    let regions = yield get(this, 'store').findAll('region');
    set(this, 'regions', regions);
  }),
});
