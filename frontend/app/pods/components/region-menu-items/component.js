import Component from '@ember/component';
import { get, set } from '@ember/object';
import { inject as service } from '@ember/service';
import { task } from 'ember-concurrency';
import ENV from 'waze-checker/config/environment';

export default Component.extend({
  tagName: '',
  hostname: ENV.hostname,
  amounts: null,

  ajax: service(),

  init() {
    this._super(...arguments);
    get(this, 'fetchAmounts').perform();
  },

  didUpdateAttrs() {
    this._super(...arguments);
    get(this, 'fetchAmounts').perform();
  },

  fetchAmounts: task(function*() {
    let regionId = get(this, 'region.id');
    let amounts = yield get(this, 'ajax').request(
      `${this.hostname}/api/segments/methods/getAmounts/${regionId}`
    );
    set(this, 'amounts', amounts);
  }),

  actions: {
    updateState() {
      get(this, 'fetchAmounts').perform();
    },
  },
});
