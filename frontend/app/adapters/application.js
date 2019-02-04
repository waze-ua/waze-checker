import DS from 'ember-data';
import config from 'waze-checker/config/environment';

export default DS.JSONAPIAdapter.extend({
  host: config.hostname,
  namespace: 'api',
});
